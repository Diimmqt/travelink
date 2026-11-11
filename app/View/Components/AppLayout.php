<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

// Komponen Blade untuk layout utama halaman aplikasi bagi pengguna yang sudah login (<x-app-layout>)
class AppLayout extends Component
{
    // Konstruktor komponen; parameter $hideNav (default false) menentukan apakah bilah navigasi disembunyikan
    public function __construct(
        public bool $hideNav = false
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    // Merender file view `layouts.app` sambil mengirimkan status visibilitas navigasi; mengembalikan objek View
    public function render(): View
    {
        return view('layouts.app', [
            'hideNav' => $this->hideNav,
        ]);
    }
}
