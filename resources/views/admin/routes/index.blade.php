@extends('layouts.admin')

@section('title', 'Data Rute Perjalanan')

@section('content')
<div class="space-y-6">
    <!-- Header Halaman & Tombol Tambah -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Kelola Rute Perjalanan</h1>
            <p class="text-sm text-brex-pewter mt-1">Daftar semua rute shuttle antar kota yang tersedia.</p>
        </div>
        <a href="{{ route('admin.routes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Data</span>
        </a>
    </div>

    <!-- Tabel Data Rute -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">ID</th>
                        <th class="py-3.5 px-6">Kota Asal</th>
                        <th class="py-3.5 px-6">Kota Tujuan</th>
                        <th class="py-3.5 px-6">Harga Tiket</th>
                        <th class="py-3.5 px-6">Estimasi Durasi</th>
                        <th class="py-3.5 px-6">Jumlah Titik Jemput</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    @forelse ($routes as $route)
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-brex-ink">#{{ $route->id }}</td>
                            <td class="py-4 px-6 font-medium text-brex-ink">{{ $route->kota_asal }}</td>
                            <td class="py-4 px-6 font-medium text-brex-ink">{{ $route->kota_tujuan }}</td>
                            <td class="py-4 px-6 text-brex-ink font-semibold">Rp {{ number_format($route->harga, 0, ',', '.') }}</td>
                            <td class="py-4 px-6">{{ $route->estimasi_durasi_menit }} menit</td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 text-xs font-medium bg-brex-fog text-brex-ink border border-brex-mist rounded-brex-chip">
                                    {{ $route->pickup_points_count }} Titik
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.routes.edit', $route) }}" class="inline-flex items-center px-3 py-1.5 border border-brex-mist text-xs font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus rute ini?')">
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
                            <td colspan="7" class="py-12 text-center text-brex-pewter">
                                Belum ada data rute perjalanan. Klik "Tambah Data" untuk membuat rute baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($routes->hasPages())
            <div class="p-4 border-t border-brex-mist bg-brex-fog/30">
                {{ $routes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
