<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Service Provider utama aplikasi Laravel untuk mendaftarkan dan menginisialisasi layanan global
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // Digunakan untuk mendaftarkan binding service ke dalam container Laravel (saat ini belum ada konfigurasi khusus)
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // Dijalankan setelah seluruh service terdaftar untuk inisialisasi awal aplikasi (saat ini masih kosong)
    public function boot(): void
    {
        //
    }
}
