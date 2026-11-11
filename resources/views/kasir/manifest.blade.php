{{-- Menggunakan layout admin sebagai kerangka halaman --}}
@extends('layouts.admin')

{{-- Menentukan judul halaman manifest dengan menyertakan nama kendaraan --}}
@section('title', 'Manifest Penumpang - ' . $schedule->vehicle->nama)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Non-printable Header Navigation -->
    {{-- Header tombol navigasi dan cetak; kelas 'print:hidden' menyembunyikannya saat halaman dicetak --}}
    <div class="flex items-center justify-between print:hidden">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Manifest Penumpang</h1>
            <p class="text-sm text-brex-pewter mt-0.5">Lembar serah terima manifest penumpang untuk sopir armada travel.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kasir.dashboard') }}" class="px-3.5 py-2 border border-brex-mist text-sm font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
                &larr; Kembali
            </a>
            {{-- Memanggil fungsi browser window.print() untuk mencetak lembar manifest --}}
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-brex-ember text-white text-sm font-semibold rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Manifest</span>
            </button>
        </div>
    </div>

    <!-- Printable Manifest Sheet -->
    {{-- Area dokumen manifest yang akan ikut dicetak dengan penyesuaian padding/border saat mode print --}}
    <div class="bg-white border border-brex-mist rounded-brex p-8 shadow-none print:border-none print:p-0 print:m-0">
        <!-- Manifest Header -->
        <div class="border-b-2 border-black pb-5 mb-6 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-black uppercase tracking-wider">TRAVELINK SHUTTLE</span>
                    <span class="text-xs px-2 py-0.5 bg-black text-white font-semibold rounded">MANIFEST SOPIR</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">Dokumen Resmi Manifest Penumpang Armada Travel</p>
            </div>
            <div class="text-right text-xs">
                <p class="font-bold text-black">JADWAL #{{ $schedule->id }}</p>
                <p class="text-gray-600">Dicetak: {{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
                <p class="text-gray-600">Petugas Loket: {{ auth()->user()->nama }}</p>
            </div>
        </div>

        <!-- Schedule & Vehicle Details -->
        {{-- Ringkasan informasi rute, waktu keberangkatan, nama armada, dan plat nomor --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg text-xs mb-6">
            <div>
                <p class="text-gray-500 uppercase font-semibold text-[10px]">Rute Perjalanan</p>
                <p class="font-bold text-black text-sm mt-0.5">
                    {{ $schedule->route->kota_asal }} &rarr; {{ $schedule->route->kota_tujuan }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 uppercase font-semibold text-[10px]">Waktu Keberangkatan</p>
                <p class="font-bold text-black text-sm mt-0.5">
                    {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->translatedFormat('d M Y, H:i') }} WIB
                </p>
            </div>
            <div>
                <p class="text-gray-500 uppercase font-semibold text-[10px]">Armada & Tipe</p>
                <p class="font-bold text-black text-sm mt-0.5">{{ $schedule->vehicle->nama }}</p>
            </div>
            <div>
                <p class="text-gray-500 uppercase font-semibold text-[10px]">Plat Nomor Kendaraan</p>
                <p class="font-mono font-bold text-black text-sm mt-0.5">{{ $schedule->vehicle->plat_nomor }}</p>
            </div>
        </div>

        <!-- Passenger Table -->
        {{-- Tabel daftar penumpang beserta nomor kursi, titik jemput/turun, kode tiket, dan kolom paraf --}}
        <table class="w-full text-left border-collapse text-xs mb-8">
            <thead>
                <tr class="bg-gray-100 border-y border-black font-semibold text-black uppercase text-[11px]">
                    <th class="py-2.5 px-3 w-16 text-center">Kursi</th>
                    <th class="py-2.5 px-3">Nama Penumpang</th>
                    <th class="py-2.5 px-3">Titik Jemput</th>
                    <th class="py-2.5 px-3">Titik Turun</th>
                    <th class="py-2.5 px-3">Kode Tiket</th>
                    <th class="py-2.5 px-3 text-center">Status</th>
                    <th class="py-2.5 px-3 text-center w-20">Paraf</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                {{-- Looping setiap tiket penumpang yang terdaftar pada jadwal ini --}}
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="py-3 px-3 text-center font-bold text-black text-sm">
                            {{ $ticket->seat->nomor_kursi ?? '-' }}
                        </td>
                        <td class="py-3 px-3">
                            <p class="font-semibold text-black">{{ $ticket->user->nama ?? 'Penumpang' }}</p>
                            @if ($ticket->nik)
                                <p class="text-[10px] text-gray-500 font-mono">NIK: {{ $ticket->nik }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-medium text-black">{{ $ticket->pickupPoint->nama_titik ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-medium text-black">{{ $ticket->dropoffPoint->nama_titik ?? '-' }}</span>
                        </td>
                        {{-- Hilangkan tanda hubung UUID pada qr_token lalu ambil 8 karakter pertama sebagai kode singkat --}}
                        <td class="py-3 px-3 font-mono font-semibold uppercase text-gray-700">
                            {{ substr(str_replace('-', '', $ticket->qr_token), 0, 8) }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            {{-- Tandai apakah penumpang sudah melakukan boarding atau baru berstatus lunas --}}
                            @if ($ticket->status === 'boarded')
                                <span class="font-bold text-emerald-700 uppercase text-[10px]">Boarded</span>
                            @else
                                <span class="font-semibold text-amber-700 uppercase text-[10px]">Lunas (Belum Naik)</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 border-l border-gray-200"></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500 italic">
                            Belum ada penumpang terdaftar pada jadwal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary & Signatures -->
        {{-- Area tanda tangan petugas loket, sopir armada, dan pihak operasional --}}
        <div class="border-t border-gray-300 pt-6 grid grid-cols-3 gap-6 text-xs text-center">
            <div>
                <p class="text-gray-600 mb-14">Petugas Loket (Kasir)</p>
                <p class="font-bold text-black border-t border-gray-400 pt-1 inline-block min-w-[140px]">
                    {{ auth()->user()->nama }}
                </p>
            </div>
            <div>
                <p class="text-gray-600 mb-14">Sopir / Driver Armada</p>
                <p class="font-bold text-black border-t border-gray-400 pt-1 inline-block min-w-[140px]">
                    ( .................................... )
                </p>
            </div>
            <div>
                <p class="text-gray-600 mb-14">Mengetahui (Operasional)</p>
                <p class="font-bold text-black border-t border-gray-400 pt-1 inline-block min-w-[140px]">
                    ( .................................... )
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

