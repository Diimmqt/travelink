<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\PickupPointController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\BoardingController;
use App\Http\Controllers\Admin\RefundController;

Route::get('/', function () {
    $routes = \App\Models\Route::withCount(['schedules' => function ($q) {
        $q->where('status', 'scheduled');
    }])->get();

    $pickupPointsByCity = \App\Models\PickupPoint::whereIn('tipe', ['jemput', 'keduanya'])
        ->get()
        ->groupBy(function ($point) {
            return $point->kota ?? ($point->route->kota_asal ?? 'Lainnya');
        });

    $stats = [
        'routes_count' => \App\Models\Route::count(),
        'vehicles_count' => \App\Models\Vehicle::count(),
        'points_count' => \App\Models\PickupPoint::whereIn('tipe', ['jemput', 'keduanya'])->count(),
        'schedules_count' => \App\Models\Schedule::where('status', 'scheduled')->count(),
    ];

    return view('welcome', compact('routes', 'pickupPointsByCity', 'stats'));
})->name('home');

Route::get('dashboard', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Buyer Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Pencarian & Pemilihan Jadwal
    Route::get('/schedules', \App\Livewire\ScheduleSearch::class)->name('schedules.search');
    Route::get('/schedules/{schedule}', \App\Livewire\ScheduleDetail::class)->name('schedules.detail');

    // Booking Flow
    Route::get('/booking/passenger', \App\Livewire\PassengerForm::class)->name('booking.passenger');
    Route::get('/booking/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/booking/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // E-Tiket & Riwayat & Refund Request
    Route::get('/tickets', [TicketController::class, 'history'])->name('tickets.history');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/refund', [TicketController::class, 'requestRefund'])->name('tickets.refund');
});

// Admin Panel Routes (Protected by auth and admin role middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Laporan Penjualan
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data CRUD
    Route::resource('routes', RouteController::class);
    Route::resource('pickup-points', PickupPointController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('schedules', ScheduleController::class);

    // Operasional: Validasi Boarding
    Route::get('/boarding', [BoardingController::class, 'index'])->name('boarding.index');
    Route::post('/boarding/validate', [BoardingController::class, 'validateToken'])->name('boarding.validate');

    // Operasional: Refund & Reschedule Approval
    Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::post('/refunds/{ticket}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
    Route::post('/refunds/{ticket}/reject', [RefundController::class, 'reject'])->name('refunds.reject');
});

// Webhook Midtrans (exclude CSRF)
Route::post('/payment/callback', [PaymentCallbackController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('payment.callback');

require __DIR__.'/auth.php';

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->middleware('auth')->name('logout');
