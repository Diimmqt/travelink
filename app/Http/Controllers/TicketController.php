<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    public function show(Ticket $ticket)
    {
        // Pastikan user hanya bisa lihat tiket sendiri (atau admin)
        if ($ticket->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        // Jika status masih pending, coba sinkronkan status dengan Midtrans atau query params dari redirect
        if ($ticket->status === 'pending') {
            $queryTrxStatus = request()->query('transaction_status');
            $statusCode     = request()->query('status_code');

            if ($queryTrxStatus === 'settlement' || $queryTrxStatus === 'capture' || $statusCode === '200') {
                $ticket->update(['status' => 'paid']);
                $ticket->seat?->update(['status' => 'booked', 'locked_until' => null]);
                $ticket->transaction?->update(['status' => 'success']);

                if ($ticket->transaction_id) {
                    Ticket::where('transaction_id', $ticket->transaction_id)->update(['status' => 'paid']);
                    $allTxTickets = Ticket::with('seat')->where('transaction_id', $ticket->transaction_id)->get();
                    foreach ($allTxTickets as $txTicket) {
                        $txTicket->seat?->update(['status' => 'booked', 'locked_until' => null]);
                    }
                }
                $ticket->refresh();
            } else {
                try {
                    $transaction = $ticket->transaction;
                    if ($transaction && $transaction->idempotency_key) {
                        \Midtrans\Config::$serverKey = config('midtrans.server_key');
                        \Midtrans\Config::$isProduction = config('midtrans.is_production');
                        \Midtrans\Config::$curlOptions = [
                            CURLOPT_TIMEOUT => 5,
                            CURLOPT_CONNECTTIMEOUT => 4,
                        ];

                        $res = \Midtrans\Transaction::status($transaction->idempotency_key);
                        $trxStatus = is_object($res) ? ($res->transaction_status ?? null) : ($res['transaction_status'] ?? null);
                        $fraudStatus = is_object($res) ? ($res->fraud_status ?? null) : ($res['fraud_status'] ?? null);

                        if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                            $ticket->update(['status' => 'paid']);
                            $ticket->seat?->update(['status' => 'booked', 'locked_until' => null]);
                            $transaction->update([
                                'status' => 'success',
                                'payment_method' => is_object($res) ? ($res->payment_type ?? null) : ($res['payment_type'] ?? null),
                            ]);
                            if ($ticket->transaction_id) {
                                Ticket::where('transaction_id', $ticket->transaction_id)->update(['status' => 'paid']);
                                $allTxTickets = Ticket::with('seat')->where('transaction_id', $ticket->transaction_id)->get();
                                foreach ($allTxTickets as $txTicket) {
                                    $txTicket->seat?->update(['status' => 'booked', 'locked_until' => null]);
                                }
                            }
                            $ticket->refresh();
                        } elseif (in_array($trxStatus, ['deny', 'expire', 'cancel'])) {
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
                            $ticket->refresh();
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore sync error in offline/local mock
                }
            }
        }

        $ticket->load([
            'schedule.route',
            'schedule.vehicle',
            'seat',
            'pickupPoint',
            'dropoffPoint',
            'user',
            'transaction',
        ]);

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

    public function history()
    {
        $tickets = Ticket::with(['schedule.route', 'seat'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.history', compact('tickets'));
    }

    public function requestRefund(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($ticket->status !== 'paid') {
            return back()->with('error', 'Hanya tiket yang berstatus lunas yang dapat mengajukan refund/reschedule.');
        }

        $ticket->update(['status' => 'refund_requested']);

        return back()->with('success', 'Pengajuan refund/reschedule berhasil dikirim ke Admin untuk ditinjau.');
    }
}
