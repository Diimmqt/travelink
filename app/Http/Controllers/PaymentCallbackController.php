<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\Seat;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk menerima notifikasi webhook (server-to-server) dari Midtrans
 * ketika status pembayaran pengguna berubah (berhasil atau gagal/kadaluarsa).
 */
class PaymentCallbackController extends Controller
{
    /**
     * Memproses payload notifikasi pembayaran dari Midtrans dan memperbarui status transaksi, tiket, dan kursi.
     *
     * @param  \Illuminate\Http\Request       $request   Permintaan HTTP berisi JSON webhook dari Midtrans
     * @param  \App\Services\MidtransService  $midtrans  Service untuk memverifikasi signature dan status notifikasi
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, MidtransService $midtrans)
    {
        // Ambil seluruh data payload webhook dan catat ke log untuk keperluan pelacakan/debugging
        $payload = $request->all();
        Log::info('Midtrans Webhook Received for Order: ' . ($payload['order_id'] ?? 'unknown'), [
            'status_code'        => $payload['status_code'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
        ]);

        // Verifikasi keaslian signature key dari Midtrans agar tidak bisa dipalsukan pihak luar
        try {
            $result = $midtrans->handleNotification($payload);
        } catch (\Exception $e) {
            Log::error('Midtrans signature error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 403);
        }

        // Temukan transaksi berdasarkan idempotency_key (= order_id di Midtrans)
        $transaction = Transaction::with(['tickets.seat', 'ticket.seat'])->where('idempotency_key', $result['order_id'])->first();

        // Jika transaksi tidak ditemukan di database, catat log error dan kembalikan status 404
        if (!$transaction) {
            Log::error('Transaction not found for order_id: ' . $result['order_id']);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Kumpulkan semua tiket yang terkait dengan transaksi ini (mendukung multi-tiket maupun single-tiket lama)
        $allTickets = $transaction->tickets;
        if ($allTickets->isEmpty() && $transaction->ticket) {
            $allTickets = collect([$transaction->ticket]);
        }

        // Jika pembayaran berhasil, ubah status transaksi menjadi success, tiket menjadi paid, dan kursi menjadi booked
        if ($result['status'] === 'success') {
            // Update transaction
            $transaction->update([
                'status'         => 'success',
                'payment_method' => $payload['payment_type'] ?? null,
            ]);

            // Update all tickets & seats
            foreach ($allTickets as $ticket) {
                $ticket->update(['status' => 'paid']);
                $ticket->seat?->update(['status' => 'booked', 'locked_until' => null]);
            }

            Log::info('Payment success for transaction #' . $transaction->id . ' (' . $allTickets->count() . ' tickets)');

        // Jika pembayaran gagal/ditolak/kadaluarsa, tandai transaksi gagal, tiket expired, dan bebaskan kembali kursinya
        } elseif ($result['status'] === 'failed') {
            // Update transaction
            $transaction->update(['status' => 'failed']);

            // Update tickets & release seats
            foreach ($allTickets as $ticket) {
                $ticket->update(['status' => 'expired']);
                $ticket->seat?->update(['status' => 'available', 'locked_until' => null]);
            }

            Log::info('Payment failed for transaction #' . $transaction->id . ' — seats released.');
        }

        // Beri respons 200 OK agar Midtrans mengetahui webhook telah diterima dengan baik
        return response()->json(['message' => 'OK'], 200);
    }
}
