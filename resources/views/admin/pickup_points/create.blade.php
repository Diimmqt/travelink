@extends('layouts.admin')

@section('title', 'Tambah Titik Lokasi')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.pickup-points.index') }}" class="inline-flex items-center gap-1.5 text-xs text-brex-pewter hover:text-brex-ink transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Titik Lokasi
        </a>
        <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Tambah Titik Jemput / Turun</h1>
        <p class="text-sm text-brex-pewter mt-1">Daftarkan lokasi titik kumpul atau penurunan untuk rute perjalanan tertentu.</p>
    </div>

    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none">
        <form action="{{ route('admin.pickup-points.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih Rute -->
            <div>
                <label for="route_id" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Rute Perjalanan</label>
                <select name="route_id" id="route_id" required class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    <option value="">-- Pilih Rute --</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}" {{ old('route_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->kota_asal }} - {{ $r->kota_tujuan }} (Rp {{ number_format($r->harga, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @error('route_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama Titik -->
                <div>
                    <label for="nama_titik" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Nama Titik / Pool</label>
                    <input type="text" name="nama_titik" id="nama_titik" value="{{ old('nama_titik') }}" required placeholder="Contoh: Pool Travelink Dipatiukur"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel">
                    @error('nama_titik')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe -->
                <div>
                    <label for="tipe" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Tipe Titik</label>
                    <select name="tipe" id="tipe" required class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                        <option value="jemput" {{ old('tipe') == 'jemput' ? 'selected' : '' }}>Titik Penjemputan (Pickup)</option>
                        <option value="turun" {{ old('tipe') == 'turun' ? 'selected' : '' }}>Titik Penurunan (Dropoff)</option>
                    </select>
                    @error('tipe')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="alamat" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Alamat Detail</label>
                <textarea name="alamat" id="alamat" rows="3" required placeholder="Masukkan alamat lengkap atau patokan lokasi..."
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-brex-mist flex items-center justify-end gap-3">
                <a href="{{ route('admin.pickup-points.index') }}" class="px-4 py-2.5 border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                    Simpan Titik Lokasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
