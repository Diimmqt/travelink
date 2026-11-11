<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Route;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller untuk menampilkan dashboard laporan transaksi, pendapatan per rute, dan grafik harian bagi Admin.
 */
class DashboardController extends Controller
{
    /**
     * Menghitung ringkasan transaksi sukses, rincian pendapatan tiap rute, dan data grafik pendapatan harian.
     * Mendukung filter rentang tanggal melalui parameter query start_date dan end_date.
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP yang dapat berisi start_date dan end_date
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Parsing tanggal awal (00:00:00) dan tanggal akhir (23:59:59) jika filter diisi oleh admin
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        // Query Successful Transactions
        // Siapkan query dasar untuk mengambil transaksi yang statusnya berhasil ('success')
        $txQuery = Transaction::where('status', 'success');

        // Terapkan filter batas tanggal awal dan akhir apabila tersedia
        if ($startDate) {
            $txQuery->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $txQuery->where('created_at', '<=', $endDate);
        }

        // Gunakan clone agar kondisi query tidak saling menimpa saat menghitung jumlah dan total nominal
        $totalTransactions = (clone $txQuery)->count();
        $totalRevenue = (clone $txQuery)->sum('amount');

        // Revenue Breakdown per Route
        // Ambil semua rute beserta jadwal dan tiket yang sudah lunas/boarded sesuai filter tanggal
        $routes = Route::with(['schedules.tickets' => function($q) use ($startDate, $endDate) {
            $q->whereIn('status', ['paid', 'boarded']);
            if ($startDate) $q->where('created_at', '>=', $startDate);
            if ($endDate) $q->where('created_at', '<=', $endDate);
        }, 'schedules.tickets.transaction'])->get();

        // Hitung jumlah tiket dan total pendapatan untuk masing-masing rute
        $routeBreakdown = $routes->map(function ($route) {
            $ticketCount = 0;
            $revenue = 0;

            foreach ($route->schedules as $schedule) {
                foreach ($schedule->tickets as $ticket) {
                    $ticketCount++;
                    // Gunakan nominal dari transaksi sukses jika tersedia, atau gunakan harga standar rute sebagai cadangan
                    if ($ticket->transaction && $ticket->transaction->status === 'success') {
                        $revenue += $ticket->transaction->amount;
                    } else {
                        $revenue += $route->harga;
                    }
                }
            }

            return [
                'id' => $route->id,
                'kota_asal' => $route->kota_asal,
                'kota_tujuan' => $route->kota_tujuan,
                'harga' => $route->harga,
                'total_tickets' => $ticketCount,
                'total_revenue' => $revenue,
            ];
        });

        // Daily Chart Data for the last 7 days (or selected range)
        // Tentukan rentang tanggal grafik: gunakan filter pengguna, atau default 7 hari terakhir
        $chartStart = $startDate ?? now()->subDays(6)->startOfDay();
        $chartEnd = $endDate ?? now()->endOfDay();

        // Kelompokkan pendapatan transaksi sukses per tanggal kalender dan jadikan tanggal sebagai key array
        $dailyData = Transaction::where('status', 'success')
            ->whereBetween('created_at', [$chartStart, $chartEnd])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as total_tx')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartValues = [];

        // Buat deretan hari dari chartStart hingga chartEnd agar tanggal yang kosong tetap tampil bernilai 0 di grafik
        $period = new \DatePeriod(
            $chartStart,
            new \DateInterval('P1D'),
            $chartEnd->copy()->addDay()
        );

        // Isi label tanggal (format 'd M') dan nilai pendapatan harian untuk dikirim ke library chart di view
        foreach ($period as $dt) {
            $dateStr = $dt->format('Y-m-d');
            $chartLabels[] = $dt->format('d M');
            $chartValues[] = isset($dailyData[$dateStr]) ? (float)$dailyData[$dateStr]->revenue : 0;
        }

        return view('admin.dashboard', compact(
            'totalTransactions',
            'totalRevenue',
            'routeBreakdown',
            'startDate',
            'endDate',
            'chartLabels',
            'chartValues'
        ));
    }
}
