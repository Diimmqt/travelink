<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Controller untuk mengelola tampilan e-ticket, riwayat pemesanan, dan pengajuan refund oleh penumpang.
 */
class TicketController extends Controller
{
    /**
     * Menampilkan detail e-ticket beserta QR Code boarding dan daftar tiket lain dalam transaksi yang sama.
     * Jika status tiket masih pending, method ini akan mengecek status terbaru langsung ke API Midtrans.
     *
     * @param  \App\Models\Ticket  $ticket  Model tiket berdasarkan route model binding
     * @return \Illuminate\View\View
     */
    public function show(Ticket $ticket)
    {
        // Pastikan user hanya bisa lihat tiket sendiri (atau admin)
        if ($ticket->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        // Jika status masih pending, lakukan sinkronisasi status HANYA melalui query API langsung ke server Midtrans (Server-to-Server)
        if ($ticket->status === 'pending') {
            try {
                $transaction = $ticket->transaction;
                // Hanya cek ke Midtrans jika transaksi memiliki idempotency_key (order_id Midtrans)
                if ($transaction && $transaction->idempotency_key) {
                    // Konfigurasi kredensial dan batas waktu koneksi API Midtrans
                    \Midtrans\Config::$serverKey = config('midtrans.server_key');
                    \Midtrans\Config::$isProduction = config('midtrans.is_production');
                    \Midtrans\Config::$curlOptions = [
                        CURLOPT_TIMEOUT => 5,
                        CURLOPT_CONNECTTIMEOUT => 4,
                    ];

                    // Panggil API Midtrans untuk mengetahui status transaksi terkini
                    $res = \Midtrans\Transaction::status($transaction->idempotency_key);
                    $trxStatus = is_object($res) ? ($res->transaction_status ?? null) : ($res['transaction_status'] ?? null);
                    $fraudStatus = is_object($res) ? ($res->fraud_status ?? null) : ($res['fraud_status'] ?? null);

                    // Jika pembayaran sudah lunas (settlement atau capture tanpa indikasi fraud)
                    if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                        // Perbarui status tiket, kursi, dan transaksi dalam satu transaksi database
                        \Illuminate\Support\Facades\DB::transaction(function () use ($ticket, $transaction, $res) {
                            $ticket->update(['status' => 'paid']);
                            $ticket->seat?->update(['status' => 'booked', 'locked_until' => null]);
                            $transaction->update([
                                'status' => 'success',
                                'payment_method' => is_object($res) ? ($res->payment_type ?? null) : ($res['payment_type'] ?? null),
                            ]);

                            // Jika dalam satu transaksi ada lebih dari 1 tiket, perbarui seluruh tiket dan kursinya sekaligus
                            if ($ticket->transaction_id) {
                                Ticket::where('transaction_id', $ticket->transaction_id)->update(['status' => 'paid']);
                                $allTxTickets = Ticket::with('seat')->where('transaction_id', $ticket->transaction_id)->get();
                                foreach ($allTxTickets as $txTicket) {
                                    $txTicket->seat?->update(['status' => 'booked', 'locked_until' => null]);
                                }
                            }
                        });
                        // Muat ulang data tiket terbaru dari database
                        $ticket->refresh();
                    // Jika pembayaran ditolak, kedaluwarsa, atau dibatalkan
                    } elseif (in_array($trxStatus, ['deny', 'expire', 'cancel'])) {
                        // Tandai tiket expired, transaksi gagal, dan lepaskan kunci kursi agar tersedia kembali
                        \Illuminate\Support\Facades\DB::transaction(function () use ($ticket, $transaction) {
                            $ticket->update(['status' => 'expired']);
                            $ticket->seat?->update(['status' => 'available', 'locked_until' => null]);
                            $transaction->update(['status' => 'failed']);

                            if ($ticket->transaction_id) {
                                Ticket::where('transaction_id', $ticket->transaction_id)->update(['status' => 'expired']);
                                $allTxTickets = Ticket::with('seat')->where('transaction_id', $ticket->transaction_id)->get();
                                foreach ($allTxTickets as $txTicket) {
                                    $txTicket->seat?->update(['status' => 'available', 'locked_until' => null]);
                                }
                            }
                        });
                        $ticket->refresh();
                    }
                }
            } catch (\Throwable $e) {
                // Jika koneksi ke Midtrans gagal/timeout, catat peringatan saja agar halaman tiket tetap bisa dibuka
                \Illuminate\Support\Facades\Log::warning('Midtrans sync error on ticket view: ' . $e->getMessage());
            }
        }

        // Muat relasi lengkap yang diperlukan untuk menampilkan detail tiket di halaman view
        $ticket->load([
            'schedule.route',
            'schedule.vehicle',
            'seat',
            'pickupPoint',
            'dropoffPoint',
            'user',
            'transaction',
        ]);

        // Ambil semua tiket lain yang berada dalam satu transaksi (untuk pemesanan multi-penumpang)
        $relatedTickets = $ticket->transaction_id 
            ? Ticket::where('transaction_id', $ticket->transaction_id)->with(['seat', 'schedule.route'])->get()
            : collect([$ticket]);

        // Generate QR code sebagai SVG string hanya jika pembayaran sudah berhasil
        $qrCode = null;
        if (in_array($ticket->status, ['paid', 'boarded'])) {
            $qrCode = QrCode::format('svg')
                ->size(140)
                ->margin(0)
                ->errorCorrection('M')
                ->generate($ticket->qr_token);
        }

        return view('tickets.show', compact('ticket', 'qrCode', 'relatedTickets'));
    }

    /**
     * Menampilkan daftar riwayat tiket milik pengguna yang sedang login dengan pagination 10 data per halaman.
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $tickets = Ticket::with(['schedule.route', 'seat'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.history', compact('tickets'));
    }

    /**
     * Mengajukan permohonan refund/reschedule untuk tiket yang berstatus lunas (paid).
     *
     * @param  \Illuminate\Http\Request  $request  Objek request HTTP
     * @param  \App\Models\Ticket        $ticket   Tiket yang diajukan untuk refund
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestRefund(Request $request, Ticket $ticket)
    {
        // Hanya pemilik tiket yang boleh mengajukan refund
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Pastikan hanya tiket berstatus 'paid' yang bisa diajukan refund
        if ($ticket->status !== 'paid') {
            return back()->with('error', 'Hanya tiket yang berstatus lunas yang dapat mengajukan refund/reschedule.');
        }

        // Ubah status tiket menjadi 'refund_requested' agar muncul di daftar persetujuan Admin
        $ticket->update(['status' => 'refund_requested']);

        return back()->with('success', 'Pengajuan refund/reschedule berhasil dikirim ke Admin untuk ditinjau.');
    }
}
