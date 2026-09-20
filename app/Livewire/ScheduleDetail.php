<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PickupPoint;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class ScheduleDetail extends Component
{
    public $schedule;
    public $pickup_point_id = '';
    public $dropoff_point_id = '';
    public $passengers = [];
    public $passengerCount = 1;
    public $selectedSeats = [];

    public function getJemputPointsProperty()
    {
        $kotaAsal = $this->schedule->route->kota_asal ?? '';
        return PickupPoint::where(function($q) use ($kotaAsal) {
            $q->where('kota', $kotaAsal)
              ->orWhere('route_id', $this->schedule->route_id);
        })->whereIn('tipe', ['jemput', 'keduanya'])->orderBy('nama_titik')->get();
    }

    public function getTurunPointsProperty()
    {
        $kotaTujuan = $this->schedule->route->kota_tujuan ?? '';
        return PickupPoint::where(function($q) use ($kotaTujuan) {
            $q->where('kota', $kotaTujuan)
              ->orWhere('route_id', $this->schedule->route_id);
        })->whereIn('tipe', ['turun', 'keduanya'])->orderBy('nama_titik')->get();
    }

    public function mount(Schedule $schedule)
    {
        $this->schedule = $schedule->load(['route', 'vehicle']);

        // Check if passenger booking exists in session for this schedule
        $booking = session('booking');
        if (!$booking || !isset($booking['schedule_id']) || $booking['schedule_id'] != $this->schedule->id || empty($booking['passengers'])) {
            return redirect()->route('booking.passenger', ['schedule_id' => $this->schedule->id]);
        }

        $this->passengers = $booking['passengers'];
        $this->passengerCount = count($this->passengers);

        // Pre-fill pickup / dropoff if already in session
        if (!empty($booking['pickup_point_id'])) {
            $this->pickup_point_id = $booking['pickup_point_id'];
        }
        if (!empty($booking['dropoff_point_id'])) {
            $this->dropoff_point_id = $booking['dropoff_point_id'];
        }

        // Auto-select single pickup/dropoff points if available
        $jemput = $this->jemputPoints;
        if ($jemput && $jemput->count() === 1 && empty($this->pickup_point_id)) {
            $this->pickup_point_id = $jemput->first()->id;
        }

        $turun = $this->turunPoints;
        if ($turun && $turun->count() === 1 && empty($this->dropoff_point_id)) {
            $this->dropoff_point_id = $turun->first()->id;
        }

        // Pre-fill previously selected seats if any
        if (!empty($booking['seat_ids']) && is_array($booking['seat_ids'])) {
            $this->selectedSeats = $booking['seat_ids'];
        } elseif (!empty($booking['seat_id'])) {
            $this->selectedSeats = [$booking['seat_id']];
        }
    }

    public function selectSeat($seatId)
    {
        $seat = Seat::find($seatId);
        if (!$seat || $seat->effective_status !== 'available') {
            // If already in selectedSeats, allow unselecting
            if (in_array($seatId, $this->selectedSeats)) {
                $this->selectedSeats = array_values(array_diff($this->selectedSeats, [$seatId]));
            } else {
                session()->flash('error', 'Kursi ini tidak tersedia.');
            }
            return;
        }

        // Toggle selection
        if (in_array($seatId, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_diff($this->selectedSeats, [$seatId]));
        } else {
            if (count($this->selectedSeats) >= $this->passengerCount) {
                if ($this->passengerCount === 1) {
                    $this->selectedSeats = [$seatId];
                } else {
                    session()->flash('error', "Anda telah memilih {$this->passengerCount} kursi. Klik kursi terpilih untuk membatalkan sebelum memilih kursi lain.");
                }
            } else {
                $this->selectedSeats[] = $seatId;
            }
        }
    }

    public function proceedToCheckout()
    {
        $hasPickup = $this->jemputPoints->isNotEmpty();
        $hasDropoff = $this->turunPoints->isNotEmpty();

        if ($hasPickup && empty($this->pickup_point_id)) {
            session()->flash('error', 'Silakan pilih titik jemput terlebih dahulu.');
            return;
        }

        if ($hasDropoff && empty($this->dropoff_point_id)) {
            session()->flash('error', 'Silakan pilih titik turun terlebih dahulu.');
            return;
        }

        if (count($this->selectedSeats) !== $this->passengerCount) {
            $missing = $this->passengerCount - count($this->selectedSeats);
            session()->flash('error', "Silakan pilih {$this->passengerCount} kursi (kurang {$missing} kursi lagi).");
            return;
        }

        try {
            DB::transaction(function () {
                // Expire stale pending tickets
                Ticket::where('schedule_id', $this->schedule->id)
                    ->where('status', 'pending')
                    ->where('created_at', '<', now()->subMinutes(10))
                    ->update(['status' => 'expired']);

                // Lock each selected seat
                foreach ($this->selectedSeats as $seatId) {
                    $seat = Seat::where('id', $seatId)->lockForUpdate()->first();
                    if (!$seat) {
                        throw new \Exception('Kursi tidak ditemukan.');
                    }

                    if ($seat->effective_status !== 'available') {
                        throw new \Exception("Kursi {$seat->nomor_kursi} sudah tidak tersedia.");
                    }

                    $seat->update([
                        'status' => 'locked',
                        'locked_until' => now()->addMinutes(10)
                    ]);
                }

                // Update session
                $booking = session('booking', []);
                $booking['schedule_id'] = $this->schedule->id;
                $booking['passengers'] = $this->passengers;
                $booking['passenger_count'] = $this->passengerCount;
                $booking['seat_ids'] = array_values($this->selectedSeats);
                $booking['seat_id'] = $this->selectedSeats[0] ?? null;
                $booking['pickup_point_id'] = $this->pickup_point_id;
                $booking['dropoff_point_id'] = $this->dropoff_point_id;
                session()->put('booking', $booking);
            });

            return redirect()->route('checkout.show');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        // Auto-expire stale pending tickets on render
        Ticket::where('schedule_id', $this->schedule->id)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(10))
            ->update(['status' => 'expired']);

        $seats = $this->schedule->seats()->orderBy('id')->get();

        // Get selected seat objects for display
        $selectedSeatObjects = $seats->whereIn('id', $this->selectedSeats);

        return view('livewire.schedule-detail', [
            'pickupPoints' => $this->jemputPoints,
            'dropoffPoints' => $this->turunPoints,
            'seats' => $seats,
            'selectedSeatObjects' => $selectedSeatObjects,
        ])->layout('layouts.app');
    }
}
