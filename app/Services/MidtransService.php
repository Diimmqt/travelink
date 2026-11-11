<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Seat;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

// Service pembungkus (wrapper) untuk integrasi pembayaran online menggunakan payment gateway Midtrans
class MidtransService
{
    // Konstruktor: menginisialisasi konfigurasi global SDK Midtrans dari file config/midtrans.php
    public function __construct()
    {
        // Atur Server Key, Client Key, mode Sandbox/Production, sanitasi input, dan 3D Secure
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Buat Snap token untuk pembayaran (bisa 1 atau banyak tiket).
     */
    // Membuat Snap Token Midtrans untuk memunculkan pop-up pembayaran;
    // menerima objek $transaction dan opsional $ticket tunggal, lalu mengembalikan string Snap Token
    public function createSnapToken(Transaction $transaction, ?Ticket $ticket = null): string
    {
        // Muat relasi tiket, jadwal, rute, kursi, dan pengguna sekaligus agar hemat query (eager loading)
        $transaction->load(['tickets.schedule.route', 'tickets.seat', 'user']);
        $tickets = $transaction->tickets;

        // Jika relasi tickets pada transaksi kosong tetapi parameter $ticket diberikan, jadikan sebagai koleksi 1 item
        if ($tickets->isEmpty() && $ticket) {
            $tickets = collect([$ticket]);
        }

        // Ambil data pengguna dari transaksi, atau fallback ke pengguna yang sedang login
        $user = $transaction->user ?? auth()->user();
        // Ambil tiket pertama untuk referensi nama pelanggan pada form Midtrans
        $firstTicket = $tickets->first() ?? $ticket;

        // Siapkan daftar rincian item pembayaran yang akan dikirim ke Midtrans
        $items = [];
        foreach ($tickets as $t) {
            // Susun data tiap tiket: ID, harga rute (atau rata-rata nominal transaksi), jumlah 1, dan nama item maksimal 50 karakter
            $items[] = [
                'id'       => 'TICKET-' . $t->id,
                'price'    => (int) ($t->schedule->route->harga ?? ($transaction->amount / max(1, $tickets->count()))),
                'quantity' => 1,
                'name'     => substr('Tiket ' . ($t->schedule->route->kota_asal ?? '') . '-' . ($t->schedule->route->kota_tujuan ?? '') . ' (' . ($t->seat->nomor_kursi ?? '') . ') ' . $t->nama_penumpang, 0, 50),
            ];
        }

        // Jika tidak ada tiket sama sekali, buat satu item default agar permintaan ke Midtrans tetap valid
        if (empty($items)) {
            $items[] = [
                'id'       => 'TRX-' . $transaction->id,
                'price'    => (int) $transaction->amount,
                'quantity' => 1,
                'name'     => 'Tiket Perjalanan Travelink',
            ];
        }

        // Susun payload parameter sesuai format API Midtrans Snap (detail transaksi, pelanggan, dan item)
        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->idempotency_key,
                'gross_amount' => (int) $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $firstTicket->nama_penumpang ?? ($user->name ?? 'Penumpang'),
                'email'      => $user->email ?? 'passenger@travelink.test',
            ],
            'item_details' => $items,
        ];

        // Kirim parameter ke API Midtrans dan kembalikan Snap Token yang dihasilkan
        return Snap::getSnapToken($params);
    }

    /**
     * Handle webhook notification dari Midtrans.
     * Returns ['status' => 'success'|'failed'|'pending', 'order_id' => string]
     */
    // Memverifikasi dan memproses notifikasi webhook status pembayaran dari server Midtrans;
    // menerima array $payload dari request webhook, memvalidasi signature SHA-512, lalu mengembalikan array status & order_id
    public function handleNotification(array $payload): array
    {
        // Validasi keberadaan payload fields yang wajib ada
        // Pastikan seluruh field penting dari Midtrans tersedia sebelum diproses lebih lanjut
        if (
            empty($payload['order_id']) ||
            empty($payload['status_code']) ||
            !isset($payload['gross_amount']) ||
            empty($payload['signature_key']) ||
            empty($payload['transaction_status'])
        ) {
            Log::warning('Midtrans webhook: payload tidak lengkap');
            throw new \Exception('Missing required webhook payload fields');
        }

        // Verifikasi signature
        // Ambil Server Key dari konfigurasi untuk menghitung ulang tanda tangan digital (signature)
        $serverKey = config('midtrans.server_key');
        if (empty($serverKey)) {
            Log::error('Midtrans webhook: server key belum dikonfigurasi di environment');
            throw new \Exception('Midtrans server key not configured');
        }

        // Hitung hash SHA-512 dari gabungan order_id + status_code + gross_amount + serverKey sesuai standar Midtrans
        $signatureKey = hash('sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey
        );

        // Bandingkan signature hasil hitungan dengan signature dari payload secara aman (hash_equals mencegah timing attack)
        if (!hash_equals($signatureKey, (string) $payload['signature_key'])) {
            Log::warning('Midtrans webhook: invalid signature for order ' . ($payload['order_id'] ?? 'unknown'));
            throw new \Exception('Invalid signature key');
        }

        // Ekstrak status transaksi, status fraud (khusus kartu kredit), dan order_id dari payload
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $orderId           = $payload['order_id'];

        // Tentukan status akhir
        // Petakan status transaksi Midtrans menjadi 3 status sederhana aplikasi: 'success', 'failed', atau 'pending'
        if ($transactionStatus === 'capture') {
            // Untuk pembayaran kartu kredit ('capture'), sukses hanya jika lolos pemeriksaan fraud ('accept')
            $status = ($fraudStatus === 'accept') ? 'success' : 'failed';
        } elseif ($transactionStatus === 'settlement') {
            // Pembayaran telah berhasil diselesaikan (lunas)
            $status = 'success';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            // Pembayaran ditolak, kedaluwarsa, atau dibatalkan
            $status = 'failed';
        } else {
            // Status selain di atas (misalnya 'pending') tetap dianggap menunggu pembayaran
            $status = 'pending';
        }

        // Kembalikan hasil pemetaan status beserta order_id transaksi terkait
        return [
            'status'   => $status,
            'order_id' => $orderId,
        ];
    }
}
