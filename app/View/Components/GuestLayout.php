<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

// Komponen Blade untuk layout halaman tamu/publik seperti halaman login dan registrasi (<x-guest-layout>)
class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    // Merender file view `layouts.guest` sebagai kerangka tampilan halaman tamu; mengembalikan objek View
    public function render(): View
    {
        return view('layouts.guest');
    }
}
