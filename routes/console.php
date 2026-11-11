<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Mendaftarkan perintah Artisan 'inspire' untuk menampilkan kutipan inspiratif di terminal setiap jam
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;
// Menjadwalkan perintah 'seats:unlock' agar berjalan otomatis setiap menit untuk membuka kunci kursi yang kedaluwarsa
Schedule::command('seats:unlock')->everyMinute();
