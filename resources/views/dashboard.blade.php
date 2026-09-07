<x-app-layout>
    <div class="bg-white border-b border-brex-mist py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
            <div class="max-w-2xl space-y-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-brex-chip bg-brex-fog border border-brex-mist text-xs font-semibold text-brex-graphite uppercase tracking-brex-24">
                    <span class="w-2 h-2 rounded-full bg-brex-ember"></span>
                    Sistem Booking Tiket Travel
                </span>
                <h1 class="text-4xl sm:text-5xl font-semibold text-brex-ink tracking-brex-72 leading-none">
                    Shuttle Travel Antar Kota<br>
                    <span class="text-brex-graphite">Lebih Praktis &amp; Nyaman</span>
                </h1>
                <p class="brex-body text-brex-graphite">
                    Halo, <strong class="text-brex-ink font-semibold">{{ auth()->user()->nama }}</strong>! Mau bepergian hari ini? Temukan rute shuttle terbaik dengan titik jemput fleksibel pilihan Anda.
                </p>
            </div>

            <!-- Quick Action Button -->
            <div class="shrink-0 w-full md:w-auto">
                <a href="{{ route('tickets.history') }}" 
                    class="brex-card p-4 flex items-center gap-4 hover:border-brex-ink transition-colors duration-150">
                    <div class="w-10 h-10 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-center text-brex-ink font-bold">
                        🎟️
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-semibold uppercase tracking-wider text-brex-pewter">Tiket Saya</p>
                        <p class="text-sm font-semibold text-brex-ink tracking-brex-24">Lihat Riwayat &rarr;</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="brex-card p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-brex-ink tracking-brex-24 mb-4">Pencarian Jadwal</h2>
            <livewire:schedule-search />
        </div>
    </div>
</x-app-layout>
