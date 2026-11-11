<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

// Service Provider untuk mengonfigurasi Livewire Volt (komponen Blade single-file)
class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    // Mendaftarkan layanan tambahan terkait Volt ke container (belum ada yang didaftarkan)
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    // Menentukan direktori tempat file komponen Livewire Volt berada saat aplikasi dijalankan
    public function boot(): void
    {
        // Mount folder views/livewire dan views/pages agar Volt dapat merender komponen di dalamnya
        Volt::mount([
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
        ]);
    }
}
