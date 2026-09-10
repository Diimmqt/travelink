<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\PickupPoint;

class PassengerForm extends Component
{
    public $nama_penumpang = '';
    public $bookingData = null;
    
    public $schedule;
    public $seat;
    public $pickup;
    public $dropoff;

    public function mount()
    {
        $this->bookingData = session('booking');
        
        if (!$this->bookingData) {
            return redirect()->route('schedules.search');
        }

        $this->schedule = Schedule::with(['route', 'vehicle'])->find($this->bookingData['schedule_id']);
        $this->seat = Seat::find($this->bookingData['seat_id']);
        $this->pickup = !empty($this->bookingData['pickup_point_id']) ? PickupPoint::find($this->bookingData['pickup_point_id']) : null;
        $this->dropoff = !empty($this->bookingData['dropoff_point_id']) ? PickupPoint::find($this->bookingData['dropoff_point_id']) : null;
        
        if (!$this->schedule || !$this->seat) {
            session()->forget('booking');
            return $this->redirectRoute('schedules.search', navigate: true);
        }

        // Ensure seat is still locked for this session
        if ($this->seat->status !== 'locked' || ($this->seat->locked_until && $this->seat->locked_until < now())) {
            session()->forget('booking');
            session()->flash('error', 'Waktu pemilihan kursi habis. Silakan pilih kembali.');
            return $this->redirectRoute('schedules.detail', ['schedule' => $this->schedule->id], navigate: true);
        }
    }

    public function submitForm()
    {
        $this->validate([
            'nama_penumpang' => 'required|min:3',
        ]);

        // Cek lagi apakah lock masih valid
        if ($this->seat->status !== 'locked' || ($this->seat->locked_until && $this->seat->locked_until < now())) {
            session()->forget('booking');
            session()->flash('error', 'Waktu pemilihan kursi habis. Silakan pilih kembali.');
            return $this->redirectRoute('schedules.detail', ['schedule' => $this->schedule->id], navigate: true);
        }

        // Update session dgn nama penumpang
        $booking = session('booking');
        $booking['nama_penumpang'] = $this->nama_penumpang;
        session()->put('booking', $booking);

        // Redirect ke halaman checkout
        return redirect()->route('checkout.show');
    }

    public function render()
    {
        return view('livewire.passenger-form')->layout('layouts.app');
    }
}
