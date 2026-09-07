@extends('layouts.admin')

@section('title', 'Edit Rute Perjalanan')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.routes.index') }}" class="inline-flex items-center gap-1.5 text-xs text-brex-pewter hover:text-brex-ink transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Rute
        </a>
        <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Edit Rute Perjalanan</h1>
        <p class="text-sm text-brex-pewter mt-1">Perbarui informasi rute {{ $route->kota_asal }} - {{ $route->kota_tujuan }}.</p>
    </div>

    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none">
        <form action="{{ route('admin.routes.update', $route) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Kota Asal -->
                <div>
                    <label for="kota_asal" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Kota Asal</label>
                    <input type="text" name="kota_asal" id="kota_asal" value="{{ old('kota_asal', $route->kota_asal) }}" required
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    @error('kota_asal')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kota Tujuan -->
                <div>
                    <label for="kota_tujuan" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Kota Tujuan</label>
                    <input type="text" name="kota_tujuan" id="kota_tujuan" value="{{ old('kota_tujuan', $route->kota_tujuan) }}" required
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    @error('kota_tujuan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Harga Tiket -->
                <div>
                    <label for="harga" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Harga Tiket (Rp)</label>
                    <input type="number" name="harga" id="harga" value="{{ old('harga', $route->harga) }}" required step="1000"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    @error('harga')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimasi Durasi (Menit) -->
                <div>
                    <label for="estimasi_durasi_menit" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Estimasi Durasi (Menit)</label>
                    <input type="number" name="estimasi_durasi_menit" id="estimasi_durasi_menit" value="{{ old('estimasi_durasi_menit', $route->estimasi_durasi_menit) }}" required
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    @error('estimasi_durasi_menit')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-brex-mist flex items-center justify-end gap-3">
                <a href="{{ route('admin.routes.index') }}" class="px-4 py-2.5 border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                    Update Rute
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
