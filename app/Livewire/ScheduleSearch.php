<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Route;
use App\Models\Schedule;
use Carbon\Carbon;

/**
 * Komponen Livewire untuk mencari jadwal keberangkatan travel berdasarkan kota asal, kota tujuan, dan tanggal.
 */
class ScheduleSearch extends Component
{
    // Filter input kota asal dari pengguna
    public $kota_asal = '';
    // Filter input kota tujuan dari pengguna
    public $kota_tujuan = '';
    // Filter tanggal keberangkatan (format Y-m-d)
    public $tanggal = '';
    // Penanda apakah pengguna sudah menekan tombol pencarian
    public $hasSearched = false;

    /**
     * Inisialisasi komponen dengan menetapkan tanggal pencarian awal ke hari ini.
     *
     * @return void
     */
    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    /**
     * Memvalidasi form pencarian dan menandai bahwa pencarian telah dilakukan.
     *
     * @return void
     */
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

    /**
     * Computed property untuk mengambil daftar jadwal yang cocok beserta perhitungan sisa kursi tersedia.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSchedulesProperty()
    {
        // Jangan lakukan query ke database sebelum seluruh filter diisi
        if (!$this->hasSearched && (empty($this->kota_asal) || empty($this->kota_tujuan) || empty($this->tanggal))) {
            return collect();
        }

        if (empty($this->kota_asal) || empty($this->kota_tujuan) || empty($this->tanggal)) {
            return collect();
        }

        // Format tanggal agar sesuai untuk perbandingan whereDate SQL
        $date = Carbon::parse($this->tanggal)->format('Y-m-d');

        // Escape karakter wildcard SQL ('%' dan '_') agar pencarian LIKE tetap aman
        $escapedAsal = addcslashes($this->kota_asal, '%_');
        $escapedTujuan = addcslashes($this->kota_tujuan, '%_');

        // Cari jadwal berstatus 'scheduled' pada tanggal & rute yang cocok, lalu hitung sisa kursinya
        return Schedule::with(['route', 'vehicle', 'seats'])
            ->whereHas('route', function ($query) use ($escapedAsal, $escapedTujuan) {
                $query->where('kota_asal', 'like', "%{$escapedAsal}%")
                      ->where('kota_tujuan', 'like', "%{$escapedTujuan}%");
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

                // Sisipkan atribut dinamis sisa_kursi untuk ditampilkan pada kartu jadwal di view
                $schedule->sisa_kursi = $availableSeats;
                return $schedule;
            });
    }

    /**
     * Merender tampilan pencarian jadwal beserta daftar unik kota asal dan tujuan dari tabel routes.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.schedule-search', [
            'schedules' => $this->schedules,
            'availableAsal' => Route::select('kota_asal')->distinct()->pluck('kota_asal'),
            'availableTujuan' => Route::select('kota_tujuan')->distinct()->pluck('kota_tujuan'),
        ])->layout('layouts.app');
    }
}
