@extends('layouts.admin')

@section('title', 'Data Titik Jemput / Turun')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Button -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Kelola Titik Jemput & Turun</h1>
            <p class="text-sm text-brex-pewter mt-1">Daftar lokasi penjemputan dan penurunan penumpang per kota.</p>
        </div>
        <a href="{{ route('admin.pickup-points.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Data</span>
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex p-4 shadow-none">
        <form method="GET" action="{{ route('admin.pickup-points.index') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label for="kota" class="block text-[11px] font-semibold text-brex-pewter uppercase tracking-wider mb-1">Filter Kota</label>
                <select name="kota" id="kota" class="bg-brex-paper border border-brex-mist rounded-brex px-3 py-2 text-sm text-brex-ink focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    <option value="">Semua Kota</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('kota') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tipe" class="block text-[11px] font-semibold text-brex-pewter uppercase tracking-wider mb-1">Filter Tipe</label>
                <select name="tipe" id="tipe" class="bg-brex-paper border border-brex-mist rounded-brex px-3 py-2 text-sm text-brex-ink focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    <option value="">Semua Tipe</option>
                    <option value="jemput" {{ request('tipe') == 'jemput' ? 'selected' : '' }}>Titik Jemput</option>
                    <option value="turun" {{ request('tipe') == 'turun' ? 'selected' : '' }}>Titik Turun</option>
                </select>
            </div>

            <div class="flex items-end pt-5">
                <button type="submit" class="px-4 py-2 bg-brex-fog border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-mist/20 transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Kota</th>
                        <th class="py-3.5 px-6">Nama Titik / Lokasi</th>
                        <th class="py-3.5 px-6">Alamat Lengkap</th>
                        <th class="py-3.5 px-6">Tipe</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    @forelse ($pickupPoints as $point)
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-brex-ink">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-brex-fog text-xs font-semibold text-brex-ink rounded-brex-chip border border-brex-mist">
                                    <svg class="w-3.5 h-3.5 text-brex-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $point->kota ?? ($point->route->kota_asal ?? 'Umum') }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-brex-ink">{{ $point->nama_titik }}</td>
                            <td class="py-4 px-6 text-xs text-brex-graphite max-w-xs truncate">{{ $point->alamat }}</td>
                            <td class="py-4 px-6">
                                @if ($point->tipe === 'jemput')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-brex-chip">
                                        Titik Jemput
                                    </span>
                                @elseif ($point->tipe === 'turun')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded-brex-chip">
                                        Titik Turun
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 rounded-brex-chip">
                                        Jemput & Turun
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.pickup-points.edit', $point) }}" class="inline-flex items-center px-3 py-1.5 border border-brex-mist text-xs font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.pickup-points.destroy', $point) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus titik ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 border border-rose-200 text-xs font-medium text-rose-600 rounded-brex hover:bg-rose-50 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-brex-pewter">
                                Belum ada titik penjemputan/penurunan. Klik "Tambah Data" untuk menambah titik lokasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pickupPoints->hasPages())
            <div class="p-4 border-t border-brex-mist bg-brex-fog/30">
                {{ $pickupPoints->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
