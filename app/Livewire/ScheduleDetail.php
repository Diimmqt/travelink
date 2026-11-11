<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PickupPoint;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

/**
 * Komponen Livewire untuk langkah kedua pemesanan: memilih titik jemput, titik turun, dan kursi armada.
 */
class ScheduleDetail extends Component
{
    // Data jadwal beserta relasi rute dan armada
    public $schedule;
    // ID titik jemput yang dipilih pengguna
    public $pickup_point_id = '';
    // ID titik turun yang dipilih pengguna
    public $dropoff_point_id = '';
    // Daftar data penumpang dari session
    public $passengers = [];
    // Jumlah total penumpang yang memesan (menentukan jumlah kursi yang wajib dipilih)
    public $passengerCount = 1;
    // Array ID kursi yang sedang dipilih pengguna
    public $selectedSeats = [];

    /**
     * Computed property untuk mengambil daftar titik jemput yang sesuai dengan kota asal atau rute jadwal.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getJemputPointsProperty()
    {
        $kotaAsal = $this->schedule->route->kota_asal ?? '';
        return PickupPoint::where(function($q) use ($kotaAsal) {
            $q->where('kota', $kotaAsal)
              ->orWhere('route_id', $this->schedule->route_id);
        })->whereIn('tipe', ['jemput', 'keduanya'])->orderBy('nama_titik')->get();
    }

    /**
     * Computed property untuk mengambil daftar titik turun yang sesuai dengan kota tujuan atau rute jadwal.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTurunPointsProperty()
    {
        $kotaTujuan = $this->schedule->route->kota_tujuan ?? '';
        return PickupPoint::where(function($q) use ($kotaTujuan) {
            $q->where('kota', $kotaTujuan)
              ->orWhere('route_id', $this->schedule->route_id);
        })->whereIn('tipe', ['turun', 'keduanya'])->orderBy('nama_titik')->get();
    }

    /**
     * Inisialisasi komponen dengan memvalidasi session booking dari form penumpang,
     * serta mengisi otomatis titik jemput/turun dan kursi apabila sebelumnya sudah dipilih.
     *
     * @param  \App\Models\Schedule  $schedule  Model jadwal dari route model binding
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function mount(Schedule $schedule)
    {
        $this->schedule = $schedule->load(['route', 'vehicle']);

        // Check if passenger booking exists in session for this schedule
        // Jika pengguna belum mengisi data penumpang untuk jadwal ini, arahkan ke halaman form penumpang dulu
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
        // Jika hanya ada 1 pilihan titik jemput atau titik turun, pilih otomatis agar mempermudah pengguna
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

    /**
     * Menangani aksi klik kursi (memilih atau membatalkan pilihan kursi) sesuai kuota jumlah penumpang.
     *
     * @param  int|string  $seatId  ID kursi yang diklik oleh pengguna
     * @return void
     */
    public function selectSeat($seatId)
    {
        // Pastikan kursi benar-benar milik jadwal keberangkatan ini
        $seat = Seat::where('id', $seatId)
            ->where('schedule_id', $this->schedule->id)
            ->first();

        // Jika kursi tidak tersedia, izinkan pembatalan jika sebelumnya memang ada di daftar pilihan user
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
        // Jika kursi sudah dipilih, batalkan pilihannya; jika belum, tambahkan selama belum melebihi jumlah penumpang
        if (in_array($seatId, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_diff($this->selectedSeats, [$seatId]));
        } else {
            if (count($this->selectedSeats) >= $this->passengerCount) {
                // Untuk 1 penumpang, klik kursi baru langsung menggantikan kursi lama agar lebih praktis
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

    /**
     * Memvalidasi pilihan titik jemput/turun dan kelengkapan jumlah kursi, mengunci kursi sementara
     * selama 10 menit di database, menyimpan ke session, lalu melanjutkan ke halaman checkout.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function proceedToCheckout()
    {
        $hasPickup = $this->jemputPoints->isNotEmpty();
        $hasDropoff = $this->turunPoints->isNotEmpty();

        $validPickupIds = $this->jemputPoints->pluck('id')->toArray();
        $validDropoffIds = $this->turunPoints->pluck('id')->toArray();

        // Validasi bahwa titik jemput yang dipilih valid untuk rute jadwal ini
        if ($hasPickup && (empty($this->pickup_point_id) || !in_array($this->pickup_point_id, $validPickupIds))) {
            session()->flash('error', 'Silakan pilih titik jemput yang valid terlebih dahulu.');
            return;
        }

        // Validasi bahwa titik turun yang dipilih valid untuk rute jadwal ini
        if ($hasDropoff && (empty($this->dropoff_point_id) || !in_array($this->dropoff_point_id, $validDropoffIds))) {
            session()->flash('error', 'Silakan pilih titik turun yang valid terlebih dahulu.');
            return;
        }

        // Pastikan jumlah kursi yang dipilih sama persis dengan jumlah penumpang
        if (count($this->selectedSeats) !== $this->passengerCount) {
            $missing = $this->passengerCount - count($this->selectedSeats);
            session()->flash('error', "Silakan pilih {$this->passengerCount} kursi (kurang {$missing} kursi lagi).");
            return;
        }

        try {
            // Gunakan DB::transaction + lockForUpdate agar tidak terjadi bentrok pemilihan kursi antar pengguna
            DB::transaction(function () {
                // Expire stale pending tickets
                // Kedaluwarsakan tiket pending yang lebih lama dari 10 menit agar kursi bisa dipakai lagi
                Ticket::where('schedule_id', $this->schedule->id)
                    ->where('status', 'pending')
                    ->where('created_at', '<', now()->subMinutes(10))
                    ->update(['status' => 'expired']);

                // Lock each selected seat, ensuring seat belongs to this schedule
                // Kunci tiap kursi terpilih selama 10 menit menuju proses checkout
                foreach ($this->selectedSeats as $seatId) {
                    $seat = Seat::where('id', $seatId)
                        ->where('schedule_id', $this->schedule->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$seat) {
                        throw new \Exception('Kursi tidak ditemukan pada jadwal ini.');
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
                // Simpan seluruh rincian pilihan kursi dan titik jemput/turun ke session booking
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

    /**
     * Merender tampilan detail jadwal & peta kursi sembari membersihkan tiket pending yang sudah kadaluarsa.
     *
     * @return \Illuminate\View\View
     */
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
