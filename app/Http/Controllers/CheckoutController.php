<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use App\Models\PickupPoint;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $booking = session('booking');

        if (!$booking || !isset($booking['nama_penumpang'])) {
            return redirect()->route('schedules.search')
                ->with('error', 'Data pemesanan tidak ditemukan. Silakan mulai pencarian kembali.');
        }

        $schedule = Schedule::with(['route', 'vehicle'])->find($booking['schedule_id']);
        $seat     = Seat::find($booking['seat_id']);
        $pickup   = PickupPoint::find($booking['pickup_point_id']);
        $dropoff  = PickupPoint::find($booking['dropoff_point_id']);

        // Check if effective seat status is allowed for checkout
        if (!$seat || $seat->effective_status === 'booked') {
            session()->forget('booking');
            return redirect()->route('schedules.detail', $booking['schedule_id'])
                ->with('error', 'Waktu pemilihan kursi habis atau kursi telah dipesan. Silakan pilih kursi kembali.');
        }

        return view('checkout.show', [
            'booking'   => $booking,
            'schedule'  => $schedule,
            'seat'      => $seat,
            'pickup'    => $pickup,
            'dropoff'   => $dropoff,
            'clientKey' => config('midtrans.client_key'),
            'snapUrl'   => config('midtrans.snap_url'),
        ]);
    }

    public function process(Request $request, MidtransService $midtrans)
    {
        $booking = session('booking');

        if (!$booking || !isset($booking['nama_penumpang'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Data pemesanan tidak valid. Silakan ulangi pencarian.',
                    'redirect' => route('schedules.search'),
                ], 422);
            }
            return redirect()->route('schedules.search')
                ->with('error', 'Data pemesanan tidak valid.');
        }

        try {
            $pendingTicketId = session('pending_ticket_id');
            $existingTicket = $pendingTicketId ? Ticket::with('transaction')->find($pendingTicketId) : null;

            if ($existingTicket && $existingTicket->status === 'pending' && $existingTicket->seat_id == $booking['seat_id']) {
                $transaction = $existingTicket->transaction;
                if (!$transaction) {
                    $schedule = Schedule::find($booking['schedule_id']);
                    $transaction = Transaction::create([
                        'user_id'          => Auth::id(),
                        'ticket_id'        => $existingTicket->id,
                        'payment_method'   => null,
                        'amount'           => $schedule->route->harga,
                        'status'           => 'pending',
                        'idempotency_key'  => 'TRX-' . strtoupper(Str::random(16)),
                    ]);
                } else {
                    // Selalu perbarui idempotency_key saat retry agar Midtrans menerima order_id baru
                    $transaction->update([
                        'idempotency_key' => 'TRX-' . strtoupper(Str::random(16)),
                    ]);
                }
                $token = $midtrans->createSnapToken($transaction, $existingTicket);
                $ticketId = $existingTicket->id;
            } else {
                $snapResult = DB::transaction(function () use ($booking, $midtrans) {
                    // Lock seat row for update
                    $seat = Seat::where('id', $booking['seat_id'])->lockForUpdate()->first();

                    if (!$seat) {
                        throw new \Exception('Kursi tidak ditemukan.');
                    }

                    // Expire stale tickets for this schedule
                    Ticket::where('schedule_id', $booking['schedule_id'])
                        ->where('status', 'pending')
                        ->where('created_at', '<', now()->subMinutes(10))
                        ->update(['status' => 'expired']);

                    // Verify no other active ticket exists for this seat
                    $otherActiveTicket = Ticket::where('seat_id', $seat->id)
                        ->where(function ($query) {
                            $query->whereIn('status', ['paid', 'boarded'])
                                  ->orWhere(function ($q) {
                                      $q->where('status', 'pending')
                                        ->where('created_at', '>=', now()->subMinutes(10));
                                  });
                        })
                        ->first();

                    if ($otherActiveTicket) {
                        throw new \Exception('Kursi ini telah dipesan oleh transaksi lain. Silakan pilih kursi kembali.');
                    }

                    $schedule = Schedule::find($booking['schedule_id']);

                    // 1. Buat Ticket
                    $ticket = Ticket::create([
                        'schedule_id'      => $booking['schedule_id'],
                        'seat_id'          => $booking['seat_id'],
                        'user_id'          => Auth::id(),
                        'pickup_point_id'  => $booking['pickup_point_id'],
                        'dropoff_point_id' => $booking['dropoff_point_id'],
                        'nama_penumpang'   => $booking['nama_penumpang'],
                        'qr_token'         => (string) Str::uuid(),
                        'status'           => 'pending',
                    ]);

                    // Update seat lock timestamp
                    $seat->update([
                        'status' => 'locked',
                        'locked_until' => now()->addMinutes(10)
                    ]);

                    // 2. Buat Transaction
                    $transaction = Transaction::create([
                        'user_id'          => Auth::id(),
                        'ticket_id'        => $ticket->id,
                        'payment_method'   => null,
                        'amount'           => $schedule->route->harga,
                        'status'           => 'pending',
                        'idempotency_key'  => 'TRX-' . strtoupper(Str::random(16)),
                    ]);

                    // 3. Buat Snap token Midtrans
                    return [
                        'token'      => $midtrans->createSnapToken($transaction, $ticket),
                        'ticket_id'  => $ticket->id,
                    ];
                });

                $token = $snapResult['token'];
                $ticketId = $snapResult['ticket_id'];
                session()->put('pending_ticket_id', $ticketId);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'    => true,
                    'snap_token' => $token,
                    'ticket_id'  => $ticketId,
                ]);
            }

            return view('checkout.payment', [
                'snapToken'  => $token,
                'clientKey'  => config('midtrans.client_key'),
                'snapUrl'    => config('midtrans.snap_url'),
                'ticketId'   => $ticketId,
            ]);

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->route('schedules.detail', $booking['schedule_id'])
                ->with('error', $e->getMessage());
        }
    }
}
