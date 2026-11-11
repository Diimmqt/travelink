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
use App\Http\Controllers\Admin\KasirManagementController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\ManifestController as KasirManifestController;
use App\Http\Controllers\Auth\GoogleAuthController;

// Rute halaman utama (beranda) yang menampilkan daftar rute, titik jemput per kota, dan statistik singkat
Route::get('/', function () {
    // Mengambil semua rute beserta jumlah jadwal yang berstatus 'scheduled'
    $routes = \App\Models\Route::withCount(['schedules' => function ($q) {
        $q->where('status', 'scheduled');
    }])->get();

    // Mengambil titik penjemputan ('jemput' atau 'keduanya') lalu mengelompokkannya berdasarkan nama kota
    $pickupPointsByCity = \App\Models\PickupPoint::whereIn('tipe', ['jemput', 'keduanya'])
        ->get()
        ->groupBy(function ($point) {
            // Menggunakan kolom kota titik; jika kosong, ambil kota asal dari rute terkait atau isi 'Lainnya'
            return $point->kota ?? ($point->route->kota_asal ?? 'Lainnya');
        });

    // Menghitung ringkasan jumlah rute, armada, titik jemput, dan jadwal aktif untuk ditampilkan di beranda
    $stats = [
        'routes_count' => \App\Models\Route::count(),
        'vehicles_count' => \App\Models\Vehicle::count(),
        'points_count' => \App\Models\PickupPoint::whereIn('tipe', ['jemput', 'keduanya'])->count(),
        'schedules_count' => \App\Models\Schedule::where('status', 'scheduled')->count(),
    ];

    return view('welcome', compact('routes', 'pickupPointsByCity', 'stats'));
})->name('home');

// Rute dashboard yang mengarahkan pengguna ke halaman sesuai perannya (admin, kasir, atau penumpang biasa)
Route::get('dashboard', function () {
    if (auth()->check()) {
        // Jika pengguna adalah super admin, arahkan ke dashboard admin
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        // Jika pengguna adalah kasir, arahkan ke dashboard kasir
        if (auth()->user()->isKasir()) {
            return redirect()->route('kasir.dashboard');
        }
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute halaman profil pengguna yang hanya dapat diakses setelah login
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Buyer Authenticated Routes
// Grup rute khusus pembeli/penumpang yang sudah login
Route::middleware(['auth'])->group(function () {
    // Pencarian & Pemilihan Jadwal
    // Halaman pencarian jadwal travel menggunakan komponen Livewire ScheduleSearch
    Route::get('/schedules', \App\Livewire\ScheduleSearch::class)->name('schedules.search');
    // Halaman detail jadwal dan pemilihan kursi menggunakan komponen Livewire ScheduleDetail
    Route::get('/schedules/{schedule}', \App\Livewire\ScheduleDetail::class)->name('schedules.detail');

    // Booking Flow
    // Halaman pengisian data penumpang dan titik jemput/turun menggunakan Livewire PassengerForm
    Route::get('/booking/passenger', \App\Livewire\PassengerForm::class)->name('booking.passenger');
    // Halaman ringkasan checkout sebelum melakukan pembayaran
    Route::get('/booking/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    // Memproses data checkout dan membuat transaksi pembayaran
    Route::post('/booking/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // E-Tiket & Riwayat & Refund Request
    // Menampilkan daftar riwayat tiket milik pengguna yang sedang login
    Route::get('/tickets', [TicketController::class, 'history'])->name('tickets.history');
    // Menampilkan detail e-tiket tertentu beserta QR code
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    // Mengirimkan permintaan pengembalian dana (refund) untuk tiket tertentu
    Route::post('/tickets/{ticket}/refund', [TicketController::class, 'requestRefund'])->name('tickets.refund');
});

// Super Admin Panel Routes (Protected by auth and role:admin)
// Grup rute panel Super Admin dengan awalan URL '/admin' dan nama rute 'admin.*'
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Laporan Penjualan
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data CRUD
    // Rute CRUD lengkap (index, create, store, show, edit, update, destroy) untuk rute, titik jemput, kendaraan, dan jadwal
    Route::resource('routes', RouteController::class);
    Route::resource('pickup-points', PickupPointController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('schedules', ScheduleController::class);

    // Keuangan: Refund & Reschedule Approval
    // Menampilkan daftar pengajuan refund dari pembeli
    Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
    // Menyetujui pengajuan refund tiket tertentu
    Route::post('/refunds/{ticket}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
    // Menolak pengajuan refund tiket tertentu
    Route::post('/refunds/{ticket}/reject', [RefundController::class, 'reject'])->name('refunds.reject');

    // FITUR BARU: Manajemen Akun Kasir (CRUD Akun Kasir Khusus Super Admin)
    Route::resource('kasir', KasirManagementController::class)->names('kasir');
});

// Kasir Panel Routes (Protected by auth and role:kasir)
// Grup rute panel Kasir dengan awalan URL '/kasir' dan nama rute 'kasir.*'
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    // Dashboard Operasional Kasir Loket
    Route::get('/', [KasirDashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');

    // Operasional: Verifikasi / Check-in Tiket Penumpang (Scan QR / Input Kode)
    // Halaman pemindaian/validasi check-in penumpang
    Route::get('/boarding', [BoardingController::class, 'index'])->name('boarding.index');
    // Memvalidasi token QR tiket penumpang saat proses boarding
    Route::post('/boarding/validate', [BoardingController::class, 'validateToken'])->name('boarding.validate');

    // Operasional: Jadwal Keberangkatan Hari Ini & Ketersediaan Kursi
    Route::get('/schedules', [KasirManifestController::class, 'index'])->name('schedules.index');

    // Operasional: Cetak / Lihat Manifest Penumpang untuk Sopir
    Route::get('/manifest/{schedule}', [KasirManifestController::class, 'show'])->name('manifest.show');
});

// Webhook Midtrans (exclude CSRF, throttle to prevent abuse)
// Endpoint penerima notifikasi status pembayaran dari Midtrans; dikecualikan dari CSRF dan dibatasi maksimal 60 permintaan per menit
Route::post('/payment/callback', [PaymentCallbackController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->middleware(['throttle:60,1'])
    ->name('payment.callback');

// Google OAuth
// Mengarahkan pengguna ke halaman login akun Google
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
// Menerima respons callback dari Google setelah pengguna menyetujui akses login
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Memuat definisi rute autentikasi bawaan (login, register, verifikasi email, reset password) dari file routes/auth.php
require __DIR__.'/auth.php';

// Logout route
// Memproses keluar (logout) pengguna: mengakhiri sesi, menghapus data sesi lama, dan membuat ulang token CSRF demi keamanan
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->middleware('auth')->name('logout');
