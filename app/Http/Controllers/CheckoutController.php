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

        if (!$booking || empty($booking['passengers']) || empty($booking['seat_ids'])) {
            return redirect()->route('schedules.search')
                ->with('error', 'Data pemesanan tidak ditemukan. Silakan mulai pencarian kembali.');
        }

        $schedule = Schedule::with(['route', 'vehicle'])->find($booking['schedule_id']);
        $seats    = Seat::whereIn('id', $booking['seat_ids'])->orderBy('id')->get();
        $pickup   = PickupPoint::find($booking['pickup_point_id']);
        $dropoff  = PickupPoint::find($booking['dropoff_point_id']);

        if (!$schedule || $seats->count() !== count($booking['passengers'])) {
            session()->forget('booking');
            return redirect()->route('schedules.detail', $booking['schedule_id'])
                ->with('error', 'Pilihan kursi tidak lengkap. Silakan pilih kembali.');
        }

        // Check if any seat is booked by another transaction
        foreach ($seats as $seat) {
            if ($seat->effective_status === 'booked') {
                session()->forget('booking');
                return redirect()->route('schedules.detail', $booking['schedule_id'])
                    ->with('error', "Kursi {$seat->nomor_kursi} telah dipesan. Silakan pilih kursi kembali.");
            }
        }

        $totalPrice = $schedule->route->harga * count($booking['passengers']);

        return view('checkout.show', [
            'booking'    => $booking,
            'schedule'   => $schedule,
            'seats'      => $seats,
            'pickup'     => $pickup,
            'dropoff'    => $dropoff,
            'totalPrice' => $totalPrice,
            'clientKey'  => config('midtrans.client_key'),
            'snapUrl'    => config('midtrans.snap_url'),
        ]);
    }

    public function process(Request $request, MidtransService $midtrans)
    {
        $booking = session('booking');

        if (!$booking || empty($booking['passengers']) || empty($booking['seat_ids'])) {
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
            $pendingTransactionId = session('pending_transaction_id');
            $existingTransaction = $pendingTransactionId ? Transaction::with('tickets')->find($pendingTransactionId) : null;

            if ($existingTransaction && $existingTransaction->status === 'pending') {
                // Perbarui idempotency_key agar Midtrans menerima order_id baru saat retry
                $existingTransaction->update([
                    'idempotency_key' => 'TRX-' . strtoupper(Str::random(16)),
                ]);
                $token = $midtrans->createSnapToken($existingTransaction);
                $firstTicket = $existingTransaction->tickets->first() ?? $existingTransaction->ticket;
                $ticketId = $firstTicket ? $firstTicket->id : 0;
            } else {
                $snapResult = DB::transaction(function () use ($booking, $midtrans) {
                    $schedule = Schedule::findOrFail($booking['schedule_id']);
                    $passengerCount = count($booking['passengers']);
                    $seatIds = $booking['seat_ids'];

                    // Lock all seat rows for update
                    $seats = Seat::whereIn('id', $seatIds)->lockForUpdate()->get();

                    if ($seats->count() !== $passengerCount) {
                        throw new \Exception('Jumlah kursi tidak sesuai dengan jumlah penumpang.');
                    }

                    // Expire stale tickets for this schedule
                    Ticket::where('schedule_id', $schedule->id)
                        ->where('status', 'pending')
                        ->where('created_at', '<', now()->subMinutes(10))
                        ->update(['status' => 'expired']);

                    // Verify no other active ticket exists for these seats
                    $occupied = Ticket::whereIn('seat_id', $seatIds)
                        ->where(function ($query) {
                            $query->whereIn('status', ['paid', 'boarded'])
                                  ->orWhere(function ($q) {
                                      $q->where('status', 'pending')
                                        ->where('created_at', '>=', now()->subMinutes(10));
                                  });
                        })
                        ->exists();

                    if ($occupied) {
                        throw new \Exception('Satu atau lebih kursi telah dipesan transaksi lain. Silakan pilih kursi kembali.');
                    }

                    $totalAmount = $schedule->route->harga * $passengerCount;

                    // 1. Buat Transaction induk
                    $transaction = Transaction::create([
                        'user_id'          => Auth::id(),
                        'ticket_id'        => null,
                        'payment_method'   => null,
                        'amount'           => $totalAmount,
                        'status'           => 'pending',
                        'idempotency_key'  => 'TRX-' . strtoupper(Str::random(16)),
                    ]);

                    $createdTickets = [];

                    // 2. Buat Tiket untuk setiap penumpang
                    foreach ($booking['passengers'] as $idx => $passenger) {
                        $seatId = $seatIds[$idx] ?? $seatIds[0];
                        $seat = $seats->firstWhere('id', $seatId);

                        $ticket = Ticket::create([
                            'schedule_id'      => $schedule->id,
                            'seat_id'          => $seatId,
                            'user_id'          => Auth::id(),
                            'transaction_id'   => $transaction->id,
                            'pickup_point_id'  => $booking['pickup_point_id'],
                            'dropoff_point_id' => $booking['dropoff_point_id'],
                            'nama_penumpang'   => $passenger['nama_lengkap'],
                            'nik'              => $passenger['nik'],
                            'qr_token'         => (string) Str::uuid(),
                            'status'           => 'pending',
                        ]);

                        $seat->update([
                            'status'       => 'locked',
                            'locked_until' => now()->addMinutes(10),
                        ]);

                        $createdTickets[] = $ticket;
                    }

                    // Set primary ticket_id on transaction for backwards compatibility
                    $transaction->update([
                        'ticket_id' => $createdTickets[0]->id,
                    ]);

                    // 3. Buat Snap token Midtrans
                    return [
                        'token'          => $midtrans->createSnapToken($transaction),
                        'ticket_id'      => $createdTickets[0]->id,
                        'transaction_id' => $transaction->id,
                    ];
                });

                $token = $snapResult['token'];
                $ticketId = $snapResult['ticket_id'];
                session()->put('pending_transaction_id', $snapResult['transaction_id']);
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
