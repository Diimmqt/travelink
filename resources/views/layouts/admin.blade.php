<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Travelink</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="bg-brex-fog font-sans antialiased text-brex-graphite min-h-screen flex">

    <!-- Sidebar Kiri -->
    <aside class="w-64 bg-brex-paper border-r border-brex-mist flex flex-col fixed inset-y-0 z-30">
        <!-- Logo Header -->
        <div class="h-16 px-6 border-b border-brex-mist flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-brex-chip bg-brex-ember flex items-center justify-center text-white font-bold text-lg">
                    T
                </div>
                <div>
                    <span class="text-lg font-semibold text-brex-ink tracking-brex-24 block leading-tight">Travelink</span>
                    <span class="text-[10px] uppercase font-semibold text-brex-pewter tracking-wider">Admin Panel</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard Laporan</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-brex-pewter uppercase tracking-wider">
                Data Master
            </div>

            <!-- Routes -->
            <a href="{{ route('admin.routes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.routes.*') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.routes.*') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span>Rute Perjalanan</span>
            </a>

            <!-- Pickup Points -->
            <a href="{{ route('admin.pickup-points.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.pickup-points.*') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.pickup-points.*') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Titik Jemput/Turun</span>
            </a>

            <!-- Vehicles -->
            <a href="{{ route('admin.vehicles.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.vehicles.*') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.vehicles.*') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span>Armada (Vehicles)</span>
            </a>

            <!-- Schedules -->
            <a href="{{ route('admin.schedules.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.schedules.*') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.schedules.*') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Jadwal (Schedules)</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-brex-pewter uppercase tracking-wider">
                Operasional
            </div>

            <!-- Boarding Validation -->
            <a href="{{ route('admin.boarding.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.boarding.*') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.boarding.*') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Validasi Boarding</span>
            </a>

            <!-- Approve Refund -->
            <a href="{{ route('admin.refunds.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-brex text-sm font-medium transition-colors border-l-4 {{ request()->routeIs('admin.refunds.*') ? 'border-brex-ember bg-brex-fog text-brex-ink' : 'border-transparent text-brex-graphite hover:bg-brex-fog hover:text-brex-ink' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.refunds.*') ? 'text-brex-ember' : 'text-brex-steel' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                <span>Refund & Reschedule</span>
            </a>
        </nav>

        <!-- User Profile & Home Link -->
        <div class="p-4 border-t border-brex-mist space-y-2 bg-brex-paper">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium text-brex-pewter hover:text-brex-ember px-2 py-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Lihat Website Publik</span>
            </a>
            <div class="flex items-center justify-between pt-2 border-t border-brex-mist/50 px-2">
                <div class="truncate">
                    <p class="text-sm font-semibold text-brex-ink truncate">{{ auth()->user()->nama }}</p>
                    <p class="text-[11px] text-brex-pewter truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="text-brex-steel hover:text-red-600 p-1.5 rounded-lg hover:bg-brex-fog transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <main class="flex-1 ml-64 p-8 min-h-screen">
        <!-- Notification Alerts -->
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-brex p-4 text-sm flex items-center justify-between shadow-none">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-brex p-4 text-sm flex items-center justify-between shadow-none">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
