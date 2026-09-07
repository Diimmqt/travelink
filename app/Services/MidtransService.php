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
     * Buat Snap token untuk pembayaran.
     */
    public function createSnapToken(Transaction $transaction, Ticket $ticket): string
    {
        $ticket->load(['schedule.route', 'seat', 'user']);

        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->idempotency_key,
                'gross_amount' => (int) $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $ticket->nama_penumpang,
                'email'      => $ticket->user->email,
            ],
            'item_details' => [
                [
                    'id'       => 'TICKET-' . $ticket->id,
                    'price'    => (int) $transaction->amount,
                    'quantity' => 1,
                    'name'     => 'Tiket ' . $ticket->schedule->route->kota_asal . ' - ' . $ticket->schedule->route->kota_tujuan . ' | Kursi ' . $ticket->seat->nomor_kursi,
                ],
            ],
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
