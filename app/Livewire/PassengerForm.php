<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;

/**
 * Komponen Livewire untuk langkah pertama pemesanan: pengisian data identitas penumpang (nama & NIK KTP).
 */
class PassengerForm extends Component
{
    // ID jadwal yang sedang dipesan
    public $schedule_id;
    // Objek model Schedule beserta relasi rute, armada, dan kursi
    public $schedule;
    // Jumlah kursi yang masih tersedia pada jadwal ini
    public $availableSeats = 0;
    // Array daftar penumpang yang diinput pengguna (nama_lengkap dan nik)
    public $passengers = [];

    // Aturan validasi untuk array penumpang dan format NIK 16 digit angka
    protected $rules = [
        'passengers' => 'required|array|min:1',
        'passengers.*.nama_lengkap' => 'required|string|min:3|max:100',
        'passengers.*.nik' => ['required', 'string', 'regex:/^[0-9]{16}$/'],
    ];

    // Pesan error validasi kustom dalam bahasa Indonesia
    protected $messages = [
        'passengers.*.nama_lengkap.required' => 'Nama lengkap penumpang wajib diisi.',
        'passengers.*.nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
        'passengers.*.nik.required' => 'NIK wajib diisi.',
        'passengers.*.nik.regex' => 'NIK harus 16 digit angka sesuai KTP.',
    ];

    /**
     * Inisialisasi komponen saat halaman dibuka: memuat jadwal, menghitung sisa kursi,
     * dan mengisi awal data penumpang dari session atau profil user yang login.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function mount()
    {
        // Ambil schedule_id dari query parameter URL atau dari session booking sebelumnya
        $scheduleId = request()->query('schedule_id', session('booking.schedule_id'));

        if (!$scheduleId) {
            return redirect()->route('schedules.search');
        }

        $this->schedule_id = $scheduleId;
        $this->schedule = Schedule::with(['route', 'vehicle', 'seats'])->find($scheduleId);

        // Jika jadwal tidak ditemukan, bersihkan session booking dan kembalikan ke halaman pencarian
        if (!$this->schedule) {
            session()->forget('booking');
            return redirect()->route('schedules.search');
        }

        // Hitung ketersediaan kursi
        // Kursi dianggap tersedia jika statusnya 'available' atau kunci sementaranya ('locked') sudah melewati batas waktu
        $this->availableSeats = $this->schedule->seats->filter(function ($seat) {
            return $seat->status === 'available' || 
                   ($seat->status === 'locked' && $seat->locked_until && $seat->locked_until < now());
        })->count();

        // Jika seluruh kursi sudah penuh, tampilkan pesan error dan kembalikan ke pencarian
        if ($this->availableSeats < 1) {
            session()->flash('error', 'Maaf, seluruh kursi pada jadwal ini sudah terisi.');
            return redirect()->route('schedules.search');
        }

        // Inisialisasi daftar penumpang
        // Jika pengguna kembali dari langkah berikutnya untuk jadwal yang sama, pulihkan data penumpang dari session
        $savedBooking = session('booking');
        if ($savedBooking && isset($savedBooking['schedule_id']) && $savedBooking['schedule_id'] == $scheduleId && !empty($savedBooking['passengers'])) {
            $this->passengers = $savedBooking['passengers'];
        } else {
            // Default 1 penumpang awal dengan nama diambil dari akun yang sedang login
            $this->passengers = [
                [
                    'nama_lengkap' => auth()->user()->name ?? '',
                    'nik' => '',
                ]
            ];
        }
    }

    /**
     * Menambahkan satu baris input penumpang baru hingga batas maksimal 6 orang atau sisa kursi yang ada.
     *
     * @return void
     */
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

    /**
     * Menghapus baris penumpang pada indeks tertentu (minimal harus tersisa 1 penumpang).
     *
     * @param  int  $index  Indeks elemen penumpang pada array $passengers
     * @return void
     */
    public function removePassenger($index)
    {
        if (count($this->passengers) > 1 && isset($this->passengers[$index])) {
            unset($this->passengers[$index]);
            // Susun ulang indeks array agar berurutan kembali (0, 1, 2, ...)
            $this->passengers = array_values($this->passengers);
        }
    }

    /**
     * Memvalidasi seluruh data penumpang, menyimpannya ke session 'booking',
     * lalu mengarahkan ke halaman pemilihan titik jemput/turun dan kursi.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function submitForm()
    {
        // Jalankan aturan validasi input nama dan NIK
        $this->validate();

        // Pastikan jumlah penumpang tidak melebihi batas maksimal (6 atau sisa kursi)
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

    /**
     * Merender tampilan form penumpang dengan layout utama aplikasi.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.passenger-form')->layout('layouts.app');
    }
}
