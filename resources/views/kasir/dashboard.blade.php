{{-- Menggunakan layout admin yang juga menyediakan navigasi sidebar sesuai role kasir --}}
@extends('layouts.admin')

{{-- Menentukan judul halaman dashboard kasir --}}
@section('title', 'Dashboard Operasional Kasir')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Staf Kasir Loket
                </span>
                {{-- Menampilkan tanggal hari ini dalam format lokal Indonesia --}}
                <span class="text-xs text-brex-pewter">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            {{-- Menyapa pengguna kasir yang sedang login --}}
            <h1 class="text-3xl font-semibold text-brex-ink tracking-brex-36 mt-1">
                Halo, {{ auth()->user()->nama }} 👋
            </h1>
            <p class="text-sm text-brex-pewter mt-0.5">Pantau jadwal keberangkatan, verifikasi boarding pass, dan cetak manifest penumpang hari ini.</p>
        </div>
        {{-- Tombol aksi cepat menuju halaman validasi boarding dan pemesanan tiket walk-in --}}
        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <a href="{{ route('kasir.boarding.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brex-ember text-white text-sm font-semibold rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Validasi Boarding Tiket</span>
            </a>
            <a href="{{ route('schedules.search') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-fog transition-colors">
                <svg class="w-4 h-4 text-brex-steel" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <span>Pesan Tiket Walk-in</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    {{-- Tiga kartu statistik ringkas: jumlah jadwal, tiket terjual, dan penumpang sudah naik hari ini --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-brex-paper border border-brex-mist rounded-brex p-5 shadow-none">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-brex-pewter">Jadwal Aktif Hari Ini</p>
                <div class="w-8 h-8 rounded-brex bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                    🚌
                </div>
            </div>
            <p class="text-3xl font-semibold text-brex-ink tracking-brex-24 mt-2">{{ $totalSchedulesToday }}</p>
            <p class="text-xs text-brex-pewter mt-1">Armada siap diberangkatkan</p>
        </div>

        <div class="bg-brex-paper border border-brex-mist rounded-brex p-5 shadow-none">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-brex-pewter">Tiket Terjual Hari Ini</p>
                <div class="w-8 h-8 rounded-brex bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    🎟️
                </div>
            </div>
            <p class="text-3xl font-semibold text-brex-ink tracking-brex-24 mt-2">{{ $totalTicketsToday }}</p>
            <p class="text-xs text-brex-pewter mt-1">Penumpang terdaftar lunas</p>
        </div>

        <div class="bg-brex-paper border border-brex-mist rounded-brex p-5 shadow-none">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-brex-pewter">Sudah Boarding / Naik</p>
                <div class="w-8 h-8 rounded-brex bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    ✅
                </div>
            </div>
            <p class="text-3xl font-semibold text-brex-ink tracking-brex-24 mt-2">{{ $totalBoardedToday }}</p>
            <p class="text-xs text-brex-pewter mt-1">Dari {{ $totalTicketsToday }} tiket yang telah terverifikasi</p>
        </div>
    </div>

    <!-- Jadwal Keberangkatan Hari Ini & Ketersediaan Kursi -->
    {{-- Tabel jadwal keberangkatan hari ini beserta sisa kursi dan tombol cetak manifest --}}
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="p-6 border-b border-brex-mist flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-brex-ink tracking-brex-24">Jadwal Keberangkatan Hari Ini</h2>
                <p class="text-xs text-brex-pewter mt-0.5">Status armada dan ketersediaan kursi terkini.</p>
            </div>
            <a href="{{ route('kasir.schedules.index') }}" class="text-xs font-semibold text-brex-ember hover:underline">
                Lihat Semua Jadwal &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Waktu</th>
                        <th class="py-3.5 px-6">Rute Perjalanan</th>
                        <th class="py-3.5 px-6">Armada</th>
                        <th class="py-3.5 px-6">Kapasitas Kursi</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    {{-- Looping setiap jadwal keberangkatan hari ini --}}
                    @forelse ($todaySchedules as $schedule)
                        @php
                            // Hitung total kursi, kursi yang sudah dipesan/dikunci, dan sisa kursi tersedia
                            $totalSeats = $schedule->seats->count();
                            $bookedSeats = $schedule->seats->filter(fn($s) => $s->effective_status !== 'available')->count();
                            $availableSeats = $totalSeats - $bookedSeats;
                        @endphp
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-brex-ink whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->format('H:i') }} WIB
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-brex-ink">{{ $schedule->route->kota_asal }}</span>
                                <span class="text-brex-pewter mx-1">&rarr;</span>
                                <span class="font-medium text-brex-ink">{{ $schedule->route->kota_tujuan }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-medium text-brex-ink">{{ $schedule->vehicle->nama }}</p>
                                <p class="text-xs text-brex-pewter font-mono">{{ $schedule->vehicle->plat_nomor }}</p>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                {{-- Tampilkan badge hijau jika masih ada kursi tersedia, atau badge merah jika penuh --}}
                                @if ($availableSeats > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-brex-chip">
                                        Sisa {{ $availableSeats }} dari {{ $totalSeats }} Kursi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-brex-chip">
                                        Penuh ({{ $totalSeats }} Kursi)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <a href="{{ route('kasir.manifest.show', $schedule->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brex-fog border border-brex-mist text-xs font-semibold text-brex-ink rounded-brex hover:border-brex-ink transition-colors">
                                    <svg class="w-3.5 h-3.5 text-brex-steel" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Manifest Sopir</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-brex-pewter text-sm">
                                Tidak ada jadwal keberangkatan aktif yang terjadwal untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

