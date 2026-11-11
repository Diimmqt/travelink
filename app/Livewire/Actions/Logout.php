<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Action class invokable Livewire untuk mengeluarkan (logout) pengguna dari sesi aplikasi dengan aman.
 */
class Logout
{
    /**
     * Log the current user out of the application.
     * Menghapus autentikasi guard web, membatalkan sesi lama, dan membuat ulang CSRF token.
     *
     * @return void
     */
    public function __invoke(): void
    {
        // Keluarkan user yang sedang aktif dari guard 'web'
        Auth::guard('web')->logout();

        // Hancurkan seluruh data sesi lama dan buat ulang token CSRF agar sesi tidak dapat dibajak ulang
        Session::invalidate();
        Session::regenerateToken();
    }
}
