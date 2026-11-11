<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controller untuk menampilkan daftar jadwal loket dan lembar manifest penumpang per armada bagi Kasir.
 */
class ManifestController extends Controller
{
    /**
     * Menampilkan daftar jadwal hari ini untuk pengecekan ketersediaan kursi & manifest.
     * Mendukung filter tanggal melalui parameter query 'date' (default hari ini).
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP yang dapat berisi query 'date'
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil parameter tanggal dari URL; jika tidak ada, gunakan tanggal hari ini
        $date = $request->query('date', Carbon::today()->toDateString());

        // Ambil jadwal pada tanggal tersebut beserta tiket yang berstatus paid/boarded untuk menghitung okupansi
        $schedules = Schedule::with(['route', 'vehicle', 'seats', 'tickets' => function ($q) {
            $q->whereIn('status', ['paid', 'boarded']);
        }])
        ->whereDate('waktu_berangkat', $date)
        ->orderBy('waktu_berangkat', 'asc')
        ->paginate(15);

        return view('kasir.schedules', compact('schedules', 'date'));
    }

    /**
     * Menampilkan dan mencetak lembar manifest penumpang untuk sopir.
     * Daftar penumpang diurutkan berdasarkan nomor kursi agar mudah diperiksa petugas lapangan.
     *
     * @param  \App\Models\Schedule  $schedule  Model jadwal keberangkatan yang ditampilkan manifest-nya
     * @return \Illuminate\View\View
     */
    public function show(Schedule $schedule)
    {
        // Muat relasi rute, kendaraan, dan daftar kursi dari jadwal ini
        $schedule->load(['route', 'vehicle', 'seats']);

        // Ambil semua tiket yang sah (lunas/sudah naik) dan urutkan secara numerik berdasarkan nomor kursi
        $tickets = Ticket::with(['seat', 'user', 'pickupPoint', 'dropoffPoint'])
            ->where('schedule_id', $schedule->id)
            ->whereIn('status', ['paid', 'boarded'])
            ->get()
            ->sortBy(function ($ticket) {
                return (int) ($ticket->seat->nomor_kursi ?? 999);
            });

        return view('kasir.manifest', compact('schedule', 'tickets'));
    }
}

