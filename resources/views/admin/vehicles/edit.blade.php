@extends('layouts.admin')

@section('title', 'Edit Armada Kendaraan')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center gap-1.5 text-xs text-brex-pewter hover:text-brex-ink transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Armada
        </a>
        <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Edit Armada Kendaraan</h1>
        <p class="text-sm text-brex-pewter mt-1">Perbarui informasi plat nomor {{ $vehicle->plat_nomor }}.</p>
    </div>

    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none">
        <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Jenis Kendaraan -->
                <div>
                    <label for="jenis" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Jenis Kendaraan</label>
                    <input type="text" name="jenis" id="jenis" value="{{ old('jenis', $vehicle->jenis) }}" required placeholder="Contoh: Toyota Hiace, Isuzu Elf, dsb."
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel">
                    @error('jenis')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plat Nomor -->
                <div>
                    <label for="plat_nomor" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Plat Nomor</label>
                    <input type="text" name="plat_nomor" id="plat_nomor" value="{{ old('plat_nomor', $vehicle->plat_nomor) }}" required
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember uppercase">
                    @error('plat_nomor')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kapasitas Kursi -->
            <div>
                <label for="kapasitas_kursi" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Kapasitas Total Kursi</label>
                <input type="number" name="kapasitas_kursi" id="kapasitas_kursi" value="{{ old('kapasitas_kursi', $vehicle->kapasitas_kursi) }}" min="1" max="50" required
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                @error('kapasitas_kursi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-brex-mist flex items-center justify-end gap-3">
                <a href="{{ route('admin.vehicles.index') }}" class="px-4 py-2.5 border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                    Update Armada
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
