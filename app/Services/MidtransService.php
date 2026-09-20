<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Seat;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Buat Snap token untuk pembayaran (bisa 1 atau banyak tiket).
     */
    public function createSnapToken(Transaction $transaction, ?Ticket $ticket = null): string
    {
        $transaction->load(['tickets.schedule.route', 'tickets.seat', 'user']);
        $tickets = $transaction->tickets;

        if ($tickets->isEmpty() && $ticket) {
            $tickets = collect([$ticket]);
        }

        $user = $transaction->user ?? auth()->user();
        $firstTicket = $tickets->first() ?? $ticket;

        $items = [];
        foreach ($tickets as $t) {
            $items[] = [
                'id'       => 'TICKET-' . $t->id,
                'price'    => (int) ($t->schedule->route->harga ?? ($transaction->amount / max(1, $tickets->count()))),
                'quantity' => 1,
                'name'     => substr('Tiket ' . ($t->schedule->route->kota_asal ?? '') . '-' . ($t->schedule->route->kota_tujuan ?? '') . ' (' . ($t->seat->nomor_kursi ?? '') . ') ' . $t->nama_penumpang, 0, 50),
            ];
        }

        if (empty($items)) {
            $items[] = [
                'id'       => 'TRX-' . $transaction->id,
                'price'    => (int) $transaction->amount,
                'quantity' => 1,
                'name'     => 'Tiket Perjalanan Travelink',
            ];
        }

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

        return Snap::getSnapToken($params);
    }

    /**
     * Handle webhook notification dari Midtrans.
     * Returns ['status' => 'success'|'failed'|'pending', 'order_id' => string]
     */
    public function handleNotification(array $payload): array
    {
        // Verifikasi signature
        $serverKey      = config('midtrans.server_key');
        $signatureKey   = hash('sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey
        );

        if ($signatureKey !== $payload['signature_key']) {
            Log::warning('Midtrans webhook: invalid signature for order ' . ($payload['order_id'] ?? 'unknown'));
            throw new \Exception('Invalid signature key');
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $orderId           = $payload['order_id'];

        // Tentukan status akhir
        if ($transactionStatus === 'capture') {
            $status = ($fraudStatus === 'accept') ? 'success' : 'failed';
        } elseif ($transactionStatus === 'settlement') {
            $status = 'success';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $status = 'failed';
        } else {
            $status = 'pending';
        }

        return [
            'status'   => $status,
            'order_id' => $orderId,
        ];
    }
}
