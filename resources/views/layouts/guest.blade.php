{{-- Layout khusus untuk halaman tamu/autentikasi (seperti login, register, dan reset password) --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Token CSRF untuk melindungi pengiriman form autentikasi --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Travelink') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css?family=Outfit:400,500,600,700,800|Plus+Jakarta+Sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        {{-- Memuat CSS dan JS utama aplikasi melalui Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-brex-ink bg-brex-fog antialiased">
        {{-- Slot utama tempat tampilan form tamu/autentikasi dirender --}}
        <div class="min-h-screen">
            {{ $slot }}
        </div>
    </body>
</html>
