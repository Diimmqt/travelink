<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BoardingLog;
use App\Models\Schedule;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controller untuk menampilkan halaman dashboard operasional loket khusus petugas Kasir.
 */
class DashboardController extends Controller
{
    /**
     * Dashboard operasional loket untuk kasir.
     * Menghitung jadwal keberangkatan hari ini, jumlah tiket terjual, jumlah penumpang yang sudah boarding,
     * serta 10 aktivitas pemindaian boarding terbaru pada hari ini.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil tanggal hari ini (pukul 00:00:00) sebagai acuan filter data harian
        $today = Carbon::today();

        // 1. Jadwal Keberangkatan Hari Ini
        // Ambil seluruh jadwal berstatus 'scheduled' yang berangkat hari ini, urut dari paling awal
        $todaySchedules = Schedule::with(['route', 'vehicle', 'seats'])
            ->whereDate('waktu_berangkat', $today)
            ->where('status', 'scheduled')
            ->orderBy('waktu_berangkat', 'asc')
            ->get();

        // 2. Statistik Operasional Hari Ini
        $totalSchedulesToday = $todaySchedules->count();

        // Kumpulkan seluruh ID jadwal hari ini untuk menghitung statistik tiket terkait
        $scheduleIdsToday = $todaySchedules->pluck('id');

        // Hitung total tiket yang sudah lunas ('paid') atau sudah naik ('boarded') pada jadwal hari ini
        $totalTicketsToday = Ticket::whereIn('schedule_id', $scheduleIdsToday)
            ->whereIn('status', ['paid', 'boarded'])
            ->count();

        // Hitung jumlah penumpang yang sudah berhasil scan boarding hari ini
        $totalBoardedToday = Ticket::whereIn('schedule_id', $scheduleIdsToday)
            ->where('status', 'boarded')
            ->count();

        // Ambil 10 catatan pemindaian QR boarding terbaru pada hari ini
        $recentBoardings = BoardingLog::with(['ticket.schedule.route', 'ticket.seat', 'ticket.user', 'admin'])
            ->whereDate('scan_time', $today)
            ->orderBy('scan_time', 'desc')
            ->take(10)
            ->get();

        return view('kasir.dashboard', compact(
            'todaySchedules',
            'totalSchedulesToday',
            'totalTicketsToday',
            'totalBoardedToday',
            'recentBoardings'
        ));
    }
}

