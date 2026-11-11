<?php

// Mengembalikan daftar Service Provider yang akan dimuat otomatis saat aplikasi Laravel dijalankan
return [
    // Service provider utama aplikasi untuk mendaftarkan layanan dan konfigurasi umum
    App\Providers\AppServiceProvider::class,
    // Service provider untuk mengaktifkan fitur Livewire Volt (komponen interaktif satu file)
    App\Providers\VoltServiceProvider::class,
    // Service provider dari Laravel Socialite untuk mendukung fitur login pihak ketiga (seperti Google OAuth)
    Laravel\Socialite\SocialiteServiceProvider::class,
];
