<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class ScheduleDetail extends Component
{
    public $schedule;
    public $pickup_point_id = '';
    public $dropoff_point_id = '';

    public function mount(Schedule $schedule)
    {
        $this->schedule = $schedule->load(['route.pickupPoints', 'vehicle']);
    }

    public function selectSeat($seatId)
    {
        // Validation
        if (empty($this->pickup_point_id) || empty($this->dropoff_point_id)) {
            session()->flash('error', 'Silakan pilih titik jemput dan titik turun terlebih dahulu.');
            return;
        }

        try {
            DB::transaction(function () use ($seatId) {
                // Expire any stale pending tickets older than 10 minutes for this schedule
                Ticket::where('schedule_id', $this->schedule->id)
                    ->where('status', 'pending')
                    ->where('created_at', '<', now()->subMinutes(10))
                    ->update(['status' => 'expired']);

                // Lock the specific seat row for update
                $seat = Seat::where('id', $seatId)->lockForUpdate()->first();

                if (!$seat) {
                    throw new \Exception('Kursi tidak ditemukan.');
                }

                // Check for active paid/boarded tickets for this seat
                $activePaidTicket = Ticket::where('seat_id', $seat->id)
                    ->whereIn('status', ['paid', 'boarded'])
                    ->exists();

                if ($activePaidTicket || $seat->status === 'booked') {
                    $seat->update(['status' => 'booked', 'locked_until' => null]);
                    throw new \Exception('Kursi sudah dipesan dan terisi.');
                }

                // Check for active unexpired pending ticket
                $activePendingTicket = Ticket::where('seat_id', $seat->id)
                    ->where('status', 'pending')
                    ->where('created_at', '>=', now()->subMinutes(10))
                    ->first();

                $currentBooking = session('booking');
                $isSameSessionSeat = $currentBooking && isset($currentBooking['seat_id']) && $currentBooking['seat_id'] == $seat->id;

                if ($activePendingTicket && !$isSameSessionSeat) {
                    throw new \Exception('Kursi sedang dalam proses pembayaran oleh transaksi lain.');
                }

                // Check if locked by another user session
                if ($seat->status === 'locked' && $seat->locked_until && $seat->locked_until >= now() && !$isSameSessionSeat) {
                    throw new \Exception('Kursi sedang dipilih oleh penumpang lain.');
                }

                // Lock seat for this user
                $seat->update([
                    'status' => 'locked',
                    'locked_until' => now()->addMinutes(10)
                ]);

                // Simpan pilihan ke session
                session()->put('booking', [
                    'schedule_id' => $this->schedule->id,
                    'seat_id' => $seat->id,
                    'pickup_point_id' => $this->pickup_point_id,
                    'dropoff_point_id' => $this->dropoff_point_id,
                    'locked_until' => $seat->locked_until
                ]);
            });

            // Redirect ke halaman passenger form
            return redirect()->route('booking.passenger');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        // Auto-expire stale pending tickets on render so UI is always accurate
        Ticket::where('schedule_id', $this->schedule->id)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(10))
            ->update(['status' => 'expired']);

        $pickupPoints = $this->schedule->route->pickupPoints->where('tipe', 'jemput');
        $dropoffPoints = $this->schedule->route->pickupPoints->where('tipe', 'turun');
        
        // Dapatkan data kursi terbaru
        $seats = $this->schedule->seats()->orderBy('id')->get();

        return view('livewire.schedule-detail', [
            'pickupPoints' => $pickupPoints,
            'dropoffPoints' => $dropoffPoints,
            'seats' => $seats,
        ])->layout('layouts.app');
    }
}
