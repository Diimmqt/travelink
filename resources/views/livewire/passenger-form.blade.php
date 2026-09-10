<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="brex-card p-6 md:p-8 max-w-2xl mx-auto space-y-6">
        <div class="pb-4 border-b border-brex-mist">
            <span class="text-xs font-semibold uppercase tracking-widest block mb-1 text-brex-ember">
                Langkah 2 dari 3
            </span>
            <h2 class="text-2xl font-semibold text-brex-ink tracking-brex-24">
                Informasi Penumpang
            </h2>
        </div>

        @if (session()->has('error'))
            <div class="p-4 rounded-brex flex items-center gap-3 text-xs font-medium bg-red-50 border border-red-200 text-red-600">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($schedule)
            <!-- Ticket Trip Summary card -->
            <div class="p-5 rounded-brex bg-brex-fog border border-brex-mist space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-brex-mist">
                    <h3 class="text-xs font-semibold uppercase tracking-widest flex items-center gap-2 text-brex-pewter">
                        <svg class="w-4 h-4 text-brex-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Ringkasan Perjalanan
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-brex-chip bg-white border border-brex-mist text-brex-ink">
                        Kursi {{ $seat->nomor_kursi }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-y-3.5 gap-x-4 text-xs">
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Rute</span>
                        <span class="font-semibold text-sm text-brex-ink tracking-brex-24">{{ $schedule->route->kota_asal }} &rarr; {{ $schedule->route->kota_tujuan }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Jadwal</span>
                        <span class="font-semibold text-sm text-brex-ink tracking-brex-24">{{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->isoFormat('D MMM, HH:mm') }} WIB</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Armada</span>
                        <span class="font-medium text-brex-graphite">{{ $schedule->vehicle->jenis }} ({{ $schedule->vehicle->plat_nomor }})</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Tarif</span>
                        <span class="font-semibold text-brex-ink">Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-span-2 pt-2 border-t border-brex-mist">
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Titik Jemput (Naik)</span>
                        <span class="font-semibold text-brex-ink">{{ $pickup->nama_titik ?? ('Pool ' . $schedule->route->kota_asal) }}</span>
                        <span class="block mt-0.5 text-xs text-brex-graphite">{{ $pickup->alamat ?? 'Lokasi pool utama kota asal' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Titik Turun</span>
                        <span class="font-semibold text-brex-ink">{{ $dropoff->nama_titik ?? ('Pool ' . $schedule->route->kota_tujuan) }}</span>
                        <span class="block mt-0.5 text-xs text-brex-graphite">{{ $dropoff->alamat ?? 'Lokasi pool utama kota tujuan' }}</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="submitForm" class="space-y-6 pt-2">
                <div>
                    <label for="nama_penumpang" class="block text-xs font-semibold uppercase tracking-widest text-brex-pewter mb-2">
                        Nama Lengkap Penumpang
                    </label>
                    <input type="text" wire:model="nama_penumpang" id="nama_penumpang"
                        class="brex-input"
                        placeholder="Tulis nama lengkap penumpang sesuai KTP/Identitas" />
                    @error('nama_penumpang')
                        <span class="text-xs text-red-600 mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-5 border-t border-brex-mist">
                    <a href="{{ route('schedules.detail', $schedule->id) }}" wire:navigate 
                        class="brex-btn-secondary text-xs px-3.5 py-2.5 font-medium">
                        &larr; Pilih Kursi Kembali
                    </a>
                    <button type="submit" class="brex-btn-ember text-xs px-5 py-2.5 font-semibold">
                        Lanjut ke Checkout &rarr;
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
