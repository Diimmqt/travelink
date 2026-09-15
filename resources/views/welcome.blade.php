<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Travelink') }} - Shuttle Travel Antar Kota Nyaman &amp; Cepat</title>
    <meta name="description" content="Pesan tiket shuttle travel antar kota secara cepat,tepat dan amanah.Booking mudah di Travelink.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white text-brex-ink antialiased selection:bg-brex-ember selection:text-white" style="font-feature-settings: 'calt' 0, 'liga' 0;">

    <!-- ========================================================================= -->
    <!-- 1. NAVBAR (Sticky Header) — BREX LIGHT THEME                              -->
    <!-- ========================================================================= -->
    <header x-data="{ mobileOpen: false }"
            class="sticky top-0 z-50 bg-white border-b border-brex-mist">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Left: Logo & Wordmark Travelink -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-brex bg-brex-ink flex items-center justify-center text-white font-bold text-lg">
                            T
                        </div>
                        <span class="font-semibold text-2xl tracking-brex-24 text-brex-ink">
                            Travelink
                        </span>
                    </a>

                    <!-- Center Navigation Links -->
                    <nav class="hidden md:flex items-center gap-1 lg:gap-2 ml-4">
                        <a href="#beranda" class="px-3.5 py-2 text-sm font-medium text-brex-ink hover:bg-brex-fog rounded-brex transition duration-150 tracking-brex-24">
                            Beranda
                        </a>
                        <a href="#lokasi-pool" class="px-3.5 py-2 text-sm font-medium text-brex-ink hover:bg-brex-fog rounded-brex transition duration-150 tracking-brex-24">
                            Lokasi Pool
                        </a>
                        <a href="#layanan" class="px-3.5 py-2 text-sm font-medium text-brex-ink hover:bg-brex-fog rounded-brex transition duration-150 tracking-brex-24">
                            Layanan
                        </a>
                        <a href="#tentang" class="px-3.5 py-2 text-sm font-medium text-brex-ink hover:bg-brex-fog rounded-brex transition duration-150 tracking-brex-24">
                            Tentang
                        </a>
                    </nav>
                </div>

                <!-- Right: Action Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('tickets.history') }}" class="text-sm font-medium text-brex-graphite hover:text-brex-ink tracking-brex-24 transition">
                            Tiket Saya
                        </a>
                        <span class="text-sm text-brex-pewter font-normal tracking-brex-24">
                            Halo, <span class="text-brex-ink font-medium">{{ auth()->user()->nama ?? auth()->user()->name }}</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                               class="brex-btn-secondary py-2 px-4 text-xs font-semibold">
                                Keluar
                            </button>
                        </form>
                    @else
                        <!-- Plain text link for "Masuk" -->
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-brex-ink hover:text-brex-ember transition tracking-brex-24 px-2">
                            Masuk
                        </a>
                        <!-- Ember filled button for "Daftar" -->
                        <a href="{{ route('register') }}"
                           class="brex-btn-ember px-4 py-2 text-sm font-semibold">
                            Daftar
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button @click="mobileOpen = !mobileOpen"
                            type="button"
                            class="p-2 text-brex-ink hover:bg-brex-fog rounded-brex transition focus:outline-none"
                            aria-label="Toggle Menu">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Navigation -->
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="md:hidden bg-white border-b border-brex-mist px-4 pt-2 pb-6 space-y-3">
            <a @click="mobileOpen = false" href="#beranda" class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                Beranda
            </a>
            <a @click="mobileOpen = false" href="#lokasi-pool" class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                Lokasi Pool
            </a>
            <a @click="mobileOpen = false" href="#layanan" class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                Layanan
            </a>
            <a @click="mobileOpen = false" href="#tentang" class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                Tentang
            </a>

            <div class="pt-4 border-t border-brex-mist flex flex-col gap-3">
                @auth
                    <a href="{{ route('tickets.history') }}" class="w-full py-2.5 text-center brex-btn-secondary">
                        Tiket Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2.5 text-center border border-red-200 text-red-600 font-semibold rounded-brex hover:bg-red-50">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full py-2.5 text-center brex-btn-secondary">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="w-full py-2.5 text-center brex-btn-ember">
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- 2. HERO SECTION — BREX LIGHT THEME                                        -->
    <!-- ========================================================================= -->
    <section id="beranda" class="bg-white py-16 lg:py-24 border-b border-brex-mist">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">

                <!-- Left Column: Asymmetric split headline + subtext + search form -->
                <div class="lg:col-span-7 space-y-8">
                    <!-- Eyebrow Chip -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-brex-chip bg-brex-fog border border-brex-mist">
                        <span class="w-2 h-2 rounded-full bg-brex-ember"></span>
                        <span class="text-xs font-semibold text-brex-graphite tracking-brex-24 uppercase">
                            Shuttle Travel Eksekutif
                        </span>
                    </div>

                    <!-- Hero Headline: weight 600, up to 72px, line-height 1.0, tracking -0.03em -->
                    <h1 class="text-4xl sm:text-5xl lg:text-[72px] font-semibold text-brex-ink leading-none tracking-brex-72">
                        Perjalanan Nyaman Antar Kota,<br>
                        <span class="text-brex-graphite">Booking Instan.</span>
                    </h1>

                    <!-- Paragraph body text: weight 400, 16px, line-height 1.5, left-aligned, max-w ~640px -->
                    <p class="brex-body text-lg text-brex-graphite">
                        Tinggalkan antrean terminal. Pilih lokasi pool terdekat, tentukan nomor kursi favorit Anda, dan nikmati armada Toyota Hiace eksekutif tepat waktu.
                    </p>



                    <!-- Highlights feature list -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-brex-mist">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-brex-ember shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-brex-ink tracking-brex-24">Denah Kursi Real-Time</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-brex-ember shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-brex-ink tracking-brex-24">Pool Pusat Kota Strategis</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-brex-ember shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-brex-ink tracking-brex-24">E-Tiket &amp; Scan QR Instant</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-brex-ember shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-brex-ink tracking-brex-24">Midtrans QRIS / VA / E-Wallet</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Ticket Search Card Form -->
                <div id="booking-panel" class="lg:col-span-5">
                    <div class="brex-card p-6 sm:p-8">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-brex-ink tracking-brex-24">
                                Cari Jadwal Shuttle
                            </h2>
                            <p class="text-sm text-brex-graphite mt-1">
                                Tentukan rute dan tanggal keberangkatan Anda
                            </p>
                        </div>
                        <livewire:schedule-search />
                    </div>
                </div>

            </div>

            <!-- Popular Routes Strip -->
            @if($routes->isNotEmpty())
            <div class="mt-20 pt-12 border-t border-brex-mist">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-widest text-brex-ember">Rute Favorit</span>
                        <h3 class="text-2xl font-semibold text-brex-ink tracking-brex-24 mt-1">Jadwal Keberangkatan Populer</h3>
                    </div>
                    <a href="#booking-panel" class="brex-link-ember text-sm">
                        Lihat Semua Rute →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($routes as $route)
                    <div class="brex-card p-5 hover:border-brex-ink transition-colors duration-150">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-base font-semibold text-brex-ink tracking-brex-24">
                                    <span>{{ $route->kota_asal }}</span>
                                    <svg class="w-4 h-4 text-brex-graphite" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                    <span>{{ $route->kota_tujuan }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-brex-pewter">
                                    <span>±{{ $route->estimasi_durasi_menit }} menit</span>
                                    <span>·</span>
                                    <span>{{ $route->schedules_count }} jadwal aktif</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs text-brex-pewter block">Mulai</span>
                                <span class="text-base font-semibold text-brex-ink tracking-brex-24">
                                    Rp {{ number_format($route->harga, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3. SECTION LOKASI POOL — FOG BACKGROUND                                   -->
    <!-- ========================================================================= -->
    <section id="lokasi-pool" class="bg-brex-fog py-20 lg:py-28 border-b border-brex-mist">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="max-w-2xl mb-14">
                <span class="text-xs font-semibold uppercase tracking-widest text-brex-ember">Jaringan Titik Jemput</span>
                <h2 class="text-3xl lg:text-4xl font-semibold text-brex-ink tracking-brex-36 mt-1">
                    Lokasi Pool &amp; Titik Penjemputan
                </h2>
                <p class="brex-body mt-3 text-brex-graphite">
                    Hadir di lokasi strategis pusat kota, dekat akses gerbang tol utama, dan pusat perbelanjaan untuk kemudahan perjalanan Anda.
                </p>
            </div>

            @if($pickupPointsByCity->isNotEmpty())
                <div class="space-y-12">
                    @foreach($pickupPointsByCity as $city => $points)
                    <div>
                        <!-- City Header -->
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-brex-chip bg-white border border-brex-mist flex items-center justify-center text-brex-ink font-semibold text-sm">
                                📍
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-brex-ink tracking-brex-24">{{ $city }}</h3>
                                <p class="text-xs text-brex-pewter">{{ $points->count() }} titik penjemputan aktif</p>
                            </div>
                            <div class="flex-1 ml-4 border-t border-brex-mist"></div>
                        </div>

                        <!-- Location Cards Grid: White bg, radius 12px, padding 24-32px, NO shadow -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($points as $point)
                            <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="text-base font-semibold text-brex-ink tracking-brex-24">
                                            {{ $point->nama_titik }}
                                        </h4>
                                        <span class="px-2 py-0.5 rounded-brex-chip bg-brex-fog border border-brex-mist text-[11px] font-semibold text-brex-graphite shrink-0">
                                            Pool Jemput
                                        </span>
                                    </div>
                                    <p class="text-sm text-brex-graphite leading-relaxed">
                                        {{ $point->alamat }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-brex-mist flex items-center justify-between text-xs text-brex-pewter">
                                    <span>Tersedia untuk rute kota ini</span>
                                    <span class="font-medium text-brex-ink">Utama</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="brex-card p-12 text-center">
                    <p class="text-sm text-brex-steel">Informasi pool titik penjemputan sedang diperbarui.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. SECTION LAYANAN — WHITE CANVAS                                         -->
    <!-- ========================================================================= -->
    <section id="layanan" class="bg-white py-20 lg:py-28 border-b border-brex-mist">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="max-w-2xl mb-14">
                <span class="text-xs font-semibold uppercase tracking-widest text-brex-ember">Keunggulan Layanan</span>
                <h2 class="text-3xl lg:text-4xl font-semibold text-brex-ink tracking-brex-36 mt-1">
                    Dirancang untuk Perjalanan yang Lebih Baik
                </h2>
                <p class="brex-body mt-3 text-brex-graphite">
                    Setiap detail dipikirkan agar perjalanan antar kota Anda terasa tenang, tepat waktu, dan tanpa keraguan.
                </p>
            </div>

            <!-- Feature Category Cards Grid: White bg, radius 12px, padding 24-32px -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Feature 1 -->
                <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4 hover:border-brex-ember transition-colors duration-150 group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-semibold">
                            📍
                        </div>
                        <h3 class="text-xl font-semibold text-brex-ink tracking-brex-24 group-hover:text-brex-ember transition-colors">
                            Titik Jemput Fleksibel
                        </h3>
                        <p class="text-sm text-brex-graphite leading-relaxed">
                            Pilih sendiri titik keberangkatan terdekat dari lokasi Anda di berbagai pool kota, tanpa perlu datang ke terminal bus.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4 hover:border-brex-ember transition-colors duration-150 group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-semibold">
                            💺
                        </div>
                        <h3 class="text-xl font-semibold text-brex-ink tracking-brex-24 group-hover:text-brex-ember transition-colors">
                            Pemilihan Kursi Real-Time
                        </h3>
                        <p class="text-sm text-brex-graphite leading-relaxed">
                            Sistem denah kabin interaktif. Pilih nomor kursi favorit Anda dengan jaminan kepastian posisi duduk secara instan.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4 hover:border-brex-ember transition-colors duration-150 group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-semibold">
                            💳
                        </div>
                        <h3 class="text-xl font-semibold text-brex-ink tracking-brex-24 group-hover:text-brex-ember transition-colors">
                            Pembayaran Instan &amp; Aman
                        </h3>
                        <p class="text-sm text-brex-graphite leading-relaxed">
                            QRIS, GoPay, ShopeePay, Virtual Account Bank, dan Kartu Debit — terhubung otomatis via gateway Midtrans.
                        </p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4 hover:border-brex-ember transition-colors duration-150 group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-semibold">
                            📲
                        </div>
                        <h3 class="text-xl font-semibold text-brex-ink tracking-brex-24 group-hover:text-brex-ember transition-colors">
                            E-Tiket &amp; QR Code
                        </h3>
                        <p class="text-sm text-brex-graphite leading-relaxed">
                            Tiket digital diterbitkan langsung ke akun Anda. Cukup tunjukkan QR Code saat pengemudi melakukan boarding.
                        </p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4 hover:border-brex-ember transition-colors duration-150 group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-semibold">
                            ⏱️
                        </div>
                        <h3 class="text-xl font-semibold text-brex-ink tracking-brex-24 group-hover:text-brex-ember transition-colors">
                            Jadwal Pasti Tepat Waktu
                        </h3>
                        <p class="text-sm text-brex-graphite leading-relaxed">
                            Manajemen armada disiplin dengan jaminan keberangkatan tepat waktu sesuai jam yang tertera pada e-tiket.
                        </p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="brex-card p-6 lg:p-8 flex flex-col justify-between space-y-4 hover:border-brex-ember transition-colors duration-150 group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-semibold">
                            🚐
                        </div>
                        <h3 class="text-xl font-semibold text-brex-ink tracking-brex-24 group-hover:text-brex-ember transition-colors">
                            Armada Toyota Hiace
                        </h3>
                        <p class="text-sm text-brex-graphite leading-relaxed">
                            Kabin bersih ber-AC dingin, kursi reclining ergonomis, port charger USB di tiap baris, serta bagasi yang aman.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 5. SECTION TENTANG — FOG BACKGROUND                                       -->
    <!-- ========================================================================= -->
    <section id="tentang" class="bg-brex-fog py-20 lg:py-28 border-b border-brex-mist">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- Left Column: Story -->
                <div class="lg:col-span-6 space-y-6">
                    <span class="text-xs font-semibold uppercase tracking-widest text-brex-ember">Tentang Travelink</span>
                    <h2 class="text-3xl lg:text-4xl font-semibold text-brex-ink tracking-brex-36">
                        Menghubungkan Kota,<br>Memudahkan Perjalanan Anda.
                    </h2>
                    <p class="brex-body text-brex-graphite">
                        Travelink adalah layanan shuttle travel modern yang dibangun untuk memberikan pengalaman perjalanan antar kota yang profesional, terprediksi, dan nyaman.
                    </p>
                    <p class="brex-body text-brex-graphite">
                        Dengan sistem tiket digital pintar dan armada Hiace yang selalu terawat, kami berkomitmen menjadi mitra perjalanan utama untuk bisnis, keluarga, maupun keperluan pribadi Anda.
                    </p>

                    <div class="pt-2">
                        <a href="#booking-panel" class="brex-link-ember text-base font-semibold">
                            Pesan Tiket Pertama Anda →
                        </a>
                    </div>
                </div>

                <!-- Right Column: Operational Stats Grid -->
                <div class="lg:col-span-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="brex-card p-6 text-left">
                            <span class="block text-4xl sm:text-5xl font-semibold text-brex-ink tracking-brex-72">
                                {{ $stats['routes_count'] }}+
                            </span>
                            <span class="block text-xs font-medium text-brex-pewter uppercase tracking-wider mt-2">
                                Rute Antar Kota
                            </span>
                        </div>

                        <div class="brex-card p-6 text-left">
                            <span class="block text-4xl sm:text-5xl font-semibold text-brex-ink tracking-brex-72">
                                {{ $stats['points_count'] }}+
                            </span>
                            <span class="block text-xs font-medium text-brex-pewter uppercase tracking-wider mt-2">
                                Pool Jemput
                            </span>
                        </div>

                        <div class="brex-card p-6 text-left">
                            <span class="block text-4xl sm:text-5xl font-semibold text-brex-ink tracking-brex-72">
                                {{ $stats['vehicles_count'] }}+
                            </span>
                            <span class="block text-xs font-medium text-brex-pewter uppercase tracking-wider mt-2">
                                Armada Eksekutif
                            </span>
                        </div>

                        <div class="brex-card p-6 text-left">
                            <span class="block text-4xl sm:text-5xl font-semibold text-brex-ink tracking-brex-72">
                                100%
                            </span>
                            <span class="block text-xs font-medium text-brex-pewter uppercase tracking-wider mt-2">
                                Jaminan Keberangkatan
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 6. FOOTER — ABYSS DARK FRAME                                              -->
    <!-- ========================================================================= -->
    <footer class="bg-brex-abyss text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Footer Columns -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pb-12 border-b border-white/10">

                <!-- Column 1: Brand Wordmark -->
                <div class="col-span-2 md:col-span-1 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-brex bg-white text-brex-ink flex items-center justify-center font-bold text-base">
                            T
                        </div>
                        <span class="text-xl font-semibold text-white tracking-brex-24">
                            Travelink
                        </span>
                    </div>
                    <p class="text-xs text-brex-mist leading-relaxed max-w-xs">
                        Layanan shuttle travel antar kota berbasis digital. Keberangkatan tepat waktu, armada Hiace eksekutif, dan pesan kursi secara instant.
                    </p>
                </div>

                <!-- Column 2: Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-white tracking-brex-24">
                        Navigasi
                    </h4>
                    <ul class="space-y-2 text-sm text-brex-mist">
                        <li><a href="#beranda" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#lokasi-pool" class="hover:text-white transition">Lokasi Pool</a></li>
                        <li><a href="#layanan" class="hover:text-white transition">Layanan</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <!-- Column 3: Access / User Links -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-white tracking-brex-24">
                        Akses Layanan
                    </h4>
                    <ul class="space-y-2 text-sm text-brex-mist">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk Akun</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Baru</a></li>
                        <li><a href="{{ route('tickets.history') }}" class="hover:text-white transition">Cek E-Tiket Saya</a></li>
                        <li><a href="{{ route('password.request') }}" class="hover:text-white transition">Bantuan Password</a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact info -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-white tracking-brex-24">
                        Hubungi Kami
                    </h4>
                    <ul class="space-y-2 text-sm text-brex-mist leading-relaxed">
                        <li>Jl. Pasteur No. 28, Bandung</li>
                        <li>CS Support: 0812-3456-7890</li>
                        <li>Email: support@travelink.id</li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-brex-mist">
                <p>&copy; {{ date('Y') }} Travelink Indonesia. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-white transition">Privasi</a>
                    <a href="#" class="hover:text-white transition">Syarat &amp; Ketentuan</a>
                    <a href="#" class="hover:text-white transition">Keamanan</a>
                </div>
            </div>

        </div>
    </footer>

    @livewireScripts
</body>
</html>
