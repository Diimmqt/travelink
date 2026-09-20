<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\Seat;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received', $payload);

        try {
            $result = $midtrans->handleNotification($payload);
        } catch (\Exception $e) {
            Log::error('Midtrans signature error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 403);
        }

        // Temukan transaksi berdasarkan idempotency_key (= order_id di Midtrans)
        $transaction = Transaction::with(['tickets.seat', 'ticket.seat'])->where('idempotency_key', $result['order_id'])->first();

        if (!$transaction) {
            Log::error('Transaction not found for order_id: ' . $result['order_id']);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $allTickets = $transaction->tickets;
        if ($allTickets->isEmpty() && $transaction->ticket) {
            $allTickets = collect([$transaction->ticket]);
        }

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

        return response()->json(['message' => 'OK'], 200);
    }
}
