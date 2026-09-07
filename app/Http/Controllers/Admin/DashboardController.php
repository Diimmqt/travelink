<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Route;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        // Query Successful Transactions
        $txQuery = Transaction::where('status', 'success');

        if ($startDate) {
            $txQuery->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $txQuery->where('created_at', '<=', $endDate);
        }

        $totalTransactions = (clone $txQuery)->count();
        $totalRevenue = (clone $txQuery)->sum('amount');

        // Revenue Breakdown per Route
        $routes = Route::with(['schedules.tickets' => function($q) use ($startDate, $endDate) {
            $q->whereIn('status', ['paid', 'boarded']);
            if ($startDate) $q->where('created_at', '>=', $startDate);
            if ($endDate) $q->where('created_at', '<=', $endDate);
        }, 'schedules.tickets.transaction'])->get();

        $routeBreakdown = $routes->map(function ($route) {
            $ticketCount = 0;
            $revenue = 0;

            foreach ($route->schedules as $schedule) {
                foreach ($schedule->tickets as $ticket) {
                    $ticketCount++;
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
        $chartStart = $startDate ?? now()->subDays(6)->startOfDay();
        $chartEnd = $endDate ?? now()->endOfDay();

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

        $period = new \DatePeriod(
            $chartStart,
            new \DateInterval('P1D'),
            $chartEnd->copy()->addDay()
        );

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
