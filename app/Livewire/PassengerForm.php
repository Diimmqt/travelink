<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;

class PassengerForm extends Component
{
    public $schedule_id;
    public $schedule;
    public $availableSeats = 0;
    public $passengers = [];

    protected $rules = [
        'passengers' => 'required|array|min:1',
        'passengers.*.nama_lengkap' => 'required|string|min:3|max:100',
        'passengers.*.nik' => ['required', 'string', 'regex:/^[0-9]{16}$/'],
    ];

    protected $messages = [
        'passengers.*.nama_lengkap.required' => 'Nama lengkap penumpang wajib diisi.',
        'passengers.*.nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
        'passengers.*.nik.required' => 'NIK wajib diisi.',
        'passengers.*.nik.regex' => 'NIK harus 16 digit angka sesuai KTP.',
    ];

    public function mount()
    {
        $scheduleId = request()->query('schedule_id', session('booking.schedule_id'));

        if (!$scheduleId) {
            return redirect()->route('schedules.search');
        }

        $this->schedule_id = $scheduleId;
        $this->schedule = Schedule::with(['route', 'vehicle', 'seats'])->find($scheduleId);

        if (!$this->schedule) {
            session()->forget('booking');
            return redirect()->route('schedules.search');
        }

        // Hitung ketersediaan kursi
        $this->availableSeats = $this->schedule->seats->filter(function ($seat) {
            return $seat->status === 'available' || 
                   ($seat->status === 'locked' && $seat->locked_until && $seat->locked_until < now());
        })->count();

        if ($this->availableSeats < 1) {
            session()->flash('error', 'Maaf, seluruh kursi pada jadwal ini sudah terisi.');
            return redirect()->route('schedules.search');
        }

        // Inisialisasi daftar penumpang
        $savedBooking = session('booking');
        if ($savedBooking && isset($savedBooking['schedule_id']) && $savedBooking['schedule_id'] == $scheduleId && !empty($savedBooking['passengers'])) {
            $this->passengers = $savedBooking['passengers'];
        } else {
            $this->passengers = [
                [
                    'nama_lengkap' => auth()->user()->name ?? '',
                    'nik' => '',
                ]
            ];
        }
    }

    public function addPassenger()
    {
        $maxAllowed = min(6, $this->availableSeats);
        if (count($this->passengers) < $maxAllowed) {
            $this->passengers[] = [
                'nama_lengkap' => '',
                'nik' => '',
            ];
        }
    }

    public function removePassenger($index)
    {
        if (count($this->passengers) > 1 && isset($this->passengers[$index])) {
            unset($this->passengers[$index]);
            $this->passengers = array_values($this->passengers);
        }
    }

    public function submitForm()
    {
        $this->validate();

        $maxAllowed = min(6, $this->availableSeats);
        if (count($this->passengers) > $maxAllowed) {
            $this->addError('passengers', "Maksimal pemesanan adalah {$maxAllowed} tiket.");
            return;
        }

        // Simpan data penumpang ke session
        session()->put('booking', [
            'schedule_id' => $this->schedule->id,
            'passengers' => $this->passengers,
            'passenger_count' => count($this->passengers),
        ]);

        // Lanjut ke pemilihan titik jemput/turun & kursi di schedule detail
        return redirect()->route('schedules.detail', ['schedule' => $this->schedule->id]);
    }

    public function render()
    {
        return view('livewire.passenger-form')->layout('layouts.app');
    }
}
