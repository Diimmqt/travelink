@extends('layouts.admin')

@section('title', 'Tambah Jadwal Keberangkatan')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center gap-1.5 text-xs text-brex-pewter hover:text-brex-ink transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Jadwal
        </a>
        <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Tambah Jadwal Keberangkatan</h1>
        <p class="text-sm text-brex-pewter mt-1">Pilih rute, tetapkan armada kendaraan, dan atur tanggal & jam keberangkatan.</p>
    </div>

    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none">
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-5">
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

            <!-- Pilih Armada -->
            <div>
                <label for="vehicle_id" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Armada Kendaraan</label>
                <select name="vehicle_id" id="vehicle_id" required class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    <option value="">-- Pilih Armada --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->jenis }} - {{ $v->plat_nomor }} (Kapasitas: {{ $v->kapasitas_kursi }} Kursi)
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-brex-pewter mt-1">Saat jadwal ini disimpan, {{ config('app.name') }} akan otomatis men-generate data kursi (seat 1 s/d N) sesuai kapasitas armada ini.</p>
                @error('vehicle_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Waktu Berangkat -->
                <div>
                    <label for="waktu_berangkat" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Waktu Keberangkatan</label>
                    <input type="datetime-local" name="waktu_berangkat" id="waktu_berangkat" value="{{ old('waktu_berangkat') }}" required
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    @error('waktu_berangkat')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Status Jadwal</label>
                    <select name="status" id="status" required class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled (Terjadwal)</option>
                        <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing (Dalam Perjalanan)</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-brex-mist flex items-center justify-end gap-3">
                <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2.5 border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                    Simpan & Generate Kursi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
