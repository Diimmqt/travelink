<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Route;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleSearch extends Component
{
    public $kota_asal = '';
    public $kota_tujuan = '';
    public $tanggal = '';
    public $hasSearched = false;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function search()
    {
        $this->validate([
            'kota_asal' => 'required',
            'kota_tujuan' => 'required',
            'tanggal' => 'required|date',
        ], [
            'kota_asal.required' => 'Silakan pilih kota asal.',
            'kota_tujuan.required' => 'Silakan pilih kota tujuan.',
            'tanggal.required' => 'Silakan tentukan tanggal keberangkatan.',
        ]);

        $this->hasSearched = true;
    }

    public function getSchedulesProperty()
    {
        if (!$this->hasSearched && (empty($this->kota_asal) || empty($this->kota_tujuan) || empty($this->tanggal))) {
            return collect();
        }

        if (empty($this->kota_asal) || empty($this->kota_tujuan) || empty($this->tanggal)) {
            return collect();
        }

        $date = Carbon::parse($this->tanggal)->format('Y-m-d');

        return Schedule::with(['route', 'vehicle', 'seats'])
            ->whereHas('route', function ($query) {
                $query->where('kota_asal', 'like', "%{$this->kota_asal}%")
                      ->where('kota_tujuan', 'like', "%{$this->kota_tujuan}%");
            })
            ->whereDate('waktu_berangkat', $date)
            ->where('status', 'scheduled')
            ->orderBy('waktu_berangkat', 'asc')
            ->get()
            ->map(function ($schedule) {
                // Sisa kursi: status available ATAU (status locked TAPI locked_until < now())
                $availableSeats = $schedule->seats->filter(function ($seat) {
                    return $seat->status === 'available' || 
                           ($seat->status === 'locked' && $seat->locked_until && $seat->locked_until < now());
                })->count();

                $schedule->sisa_kursi = $availableSeats;
                return $schedule;
            });
    }

    public function render()
    {
        return view('livewire.schedule-search', [
            'schedules' => $this->schedules,
            'availableAsal' => Route::select('kota_asal')->distinct()->pluck('kota_asal'),
            'availableTujuan' => Route::select('kota_tujuan')->distinct()->pluck('kota_tujuan'),
        ])->layout('layouts.app');
    }
}
