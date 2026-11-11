{{-- Menggunakan kerangka layout admin --}}
@extends('layouts.admin')

{{-- Menentukan judul halaman jadwal dan ketersediaan kursi untuk kasir --}}
@section('title', 'Jadwal Hari Ini & Ketersediaan Kursi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Jadwal Keberangkatan & Ketersediaan Kursi</h1>
            <p class="text-sm text-brex-pewter mt-1">Cek keterisian kursi armada untuk jadwal operasional loket.</p>
        </div>

        {{-- Form filter metode GET untuk memilih tanggal keberangkatan yang ingin ditampilkan --}}
        <form method="GET" action="{{ route('kasir.schedules.index') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}"
                class="bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2 text-brex-ink text-sm focus:outline-none focus:border-brex-ember">
            <button type="submit" class="px-4 py-2 bg-brex-ember text-white font-medium text-sm rounded-brex hover:bg-[#e04f00] transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Waktu Berangkat</th>
                        <th class="py-3.5 px-6">Rute</th>
                        <th class="py-3.5 px-6">Armada</th>
                        <th class="py-3.5 px-6">Harga</th>
                        <th class="py-3.5 px-6">Status Kursi</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    {{-- Looping daftar jadwal pada tanggal terpilih --}}
                    @forelse ($schedules as $schedule)
                        @php
                            // Hitung total kursi armada, jumlah kursi yang sudah terisi/terkunci, dan sisa kursi kosong
                            $totalSeats = $schedule->seats->count();
                            $bookedSeats = $schedule->seats->filter(fn($s) => $s->effective_status !== 'available')->count();
                            $availableSeats = $totalSeats - $bookedSeats;
                        @endphp
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-brex-ink whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->format('d M Y, H:i') }} WIB
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
                            <td class="py-4 px-6 font-semibold text-brex-ink">
                                Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                {{-- Tampilkan badge hijau jika masih tersedia kursi, atau badge merah jika sudah penuh --}}
                                @if ($availableSeats > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-brex-chip">
                                        Tersedia {{ $availableSeats }} dari {{ $totalSeats }} Kursi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-brex-chip">
                                        Penuh ({{ $totalSeats }} Kursi)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                {{-- Tombol untuk membuka halaman pilih kursi (pemesanan tiket walk-in) dan lembar manifest --}}
                                <a href="{{ route('schedules.detail', $schedule->id) }}" class="inline-flex items-center px-3 py-1.5 border border-brex-mist text-xs font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
                                    Pilih Kursi
                                </a>
                                <a href="{{ route('kasir.manifest.show', $schedule->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brex-fog border border-brex-mist text-xs font-semibold text-brex-ink rounded-brex hover:border-brex-ink transition-colors">
                                    Manifest
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-brex-pewter">
                                Tidak ada jadwal keberangkatan pada tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Menampilkan paginasi jika jumlah jadwal pada tanggal tersebut melebihi batas per halaman --}}
        @if ($schedules->hasPages())
            <div class="p-4 border-t border-brex-mist">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

