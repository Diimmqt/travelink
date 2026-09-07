@extends('layouts.admin')

@section('title', 'Tambah Armada Kendaraan')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center gap-1.5 text-xs text-brex-pewter hover:text-brex-ink transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Armada
        </a>
        <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Tambah Armada Kendaraan</h1>
        <p class="text-sm text-brex-pewter mt-1">Daftarkan jenis minibus, plat nomor resmi, dan kapasitas kursi.</p>
    </div>

    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none">
        <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Jenis Kendaraan -->
                <div>
                    <label for="jenis" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Jenis Kendaraan</label>
                    <select name="jenis" id="jenis" required class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                        <option value="Hiace" {{ old('jenis') == 'Hiace' ? 'selected' : '' }}>Toyota Hiace</option>
                        <option value="Minibus" {{ old('jenis') == 'Minibus' ? 'selected' : '' }}>Minibus Standard</option>
                    </select>
                    @error('jenis')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plat Nomor -->
                <div>
                    <label for="plat_nomor" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Plat Nomor</label>
                    <input type="text" name="plat_nomor" id="plat_nomor" value="{{ old('plat_nomor') }}" required placeholder="Contoh: D 7701 AB"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember uppercase placeholder-brex-steel">
                    @error('plat_nomor')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kapasitas Kursi -->
            <div>
                <label for="kapasitas_kursi" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Kapasitas Total Kursi</label>
                <input type="number" name="kapasitas_kursi" id="kapasitas_kursi" value="{{ old('kapasitas_kursi', 10) }}" min="1" max="50" required placeholder="10"
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                <p class="text-xs text-brex-pewter mt-1">Saat jadwal baru dibuat menggunakan armada ini, sistem akan otomatis generate nomor kursi sesuai jumlah ini.</p>
                @error('kapasitas_kursi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-brex-mist flex items-center justify-end gap-3">
                <a href="{{ route('admin.vehicles.index') }}" class="px-4 py-2.5 border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                    Simpan Armada
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
