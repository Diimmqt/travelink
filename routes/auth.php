<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Grup rute untuk pengguna yang belum login (tamu / guest)
Route::middleware('guest')->group(function () {
    // Halaman pendaftaran akun baru menggunakan komponen Livewire Volt
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    // Halaman masuk (login) pengguna menggunakan komponen Livewire Volt
    Volt::route('login', 'pages.auth.login')
        ->name('login');

    // Halaman permintaan reset kata sandi ketika pengguna lupa password
    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    // Halaman pengisian kata sandi baru berdasarkan token reset yang dikirim ke email
    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

// Grup rute untuk pengguna yang sudah login (terautentikasi)
Route::middleware('auth')->group(function () {
    // Halaman pemberitahuan untuk memverifikasi alamat email pengguna
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    // Rute pemrosesan tautan verifikasi email; dilindungi tanda tangan URL (signed) dan pembatasan akses maksimal 6 kali per menit
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Halaman konfirmasi ulang password sebelum melakukan tindakan sensitif
    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
