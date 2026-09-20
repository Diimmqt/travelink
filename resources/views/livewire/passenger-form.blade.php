<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="brex-card p-6 md:p-8 max-w-2xl mx-auto space-y-6">
        <div class="pb-4 border-b border-brex-mist flex items-start justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest block mb-1 text-brex-ember">
                    Langkah 1 dari 3
                </span>
                <h2 class="text-2xl font-semibold text-brex-ink tracking-brex-24">
                    Data Penumpang
                </h2>
                <p class="text-xs text-brex-pewter mt-1">
                    Masukkan nama lengkap dan NIK sesuai kartu identitas (KTP) untuk setiap penumpang.
                </p>
            </div>
            <span class="px-3 py-1 rounded-brex-chip bg-brex-fog border border-brex-mist text-xs font-semibold text-brex-ink shrink-0">
                {{ count($passengers) }} Tiket
            </span>
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
            <!-- Schedule Summary Card -->
            <div class="p-5 rounded-brex bg-brex-fog border border-brex-mist space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-brex-mist">
                    <h3 class="text-xs font-semibold uppercase tracking-widest flex items-center gap-2 text-brex-pewter">
                        <svg class="w-4 h-4 text-brex-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Ringkasan Jadwal Terpilih
                    </h3>
                    <span class="text-xs font-semibold text-emerald-600">
                        Tersedia {{ $availableSeats }} Kursi
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Rute</span>
                        <span class="font-semibold text-sm text-brex-ink">{{ $schedule->route->kota_asal }} &rarr; {{ $schedule->route->kota_tujuan }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Keberangkatan</span>
                        <span class="font-semibold text-sm text-brex-ink">{{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->isoFormat('D MMM, HH:mm') }} WIB</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Armada</span>
                        <span class="font-medium text-brex-graphite">{{ $schedule->vehicle->jenis }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider mb-0.5 text-brex-pewter font-medium">Tarif Satuan</span>
                        <span class="font-semibold text-brex-ink">Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Form Penumpang -->
            <form wire:submit.prevent="submitForm" class="space-y-6 pt-2">
                
                <div class="space-y-4">
                    @foreach($passengers as $index => $passenger)
                        <div class="p-5 rounded-brex bg-white border border-brex-mist space-y-4 relative" wire:key="passenger-{{ $index }}">
                            <div class="flex items-center justify-between pb-2 border-b border-brex-mist/70">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-brex-ember text-white flex items-center justify-center text-xs font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-brex-ink">
                                        Penumpang {{ $index + 1 }} {{ $index === 0 ? '(Pemesan)' : '' }}
                                    </span>
                                </div>

                                @if(count($passengers) > 1)
                                    <button type="button" wire:click="removePassenger({{ $index }})"
                                            class="text-xs text-red-600 hover:text-red-700 font-medium inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-widest text-brex-pewter mb-1.5">
                                        Nama Lengkap
                                    </label>
                                    <input type="text" wire:model.defer="passengers.{{ $index }}.nama_lengkap"
                                        class="brex-input text-sm"
                                        placeholder="Contoh: Dimitar Bayanaka" />
                                    @error("passengers.{$index}.nama_lengkap")
                                        <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- NIK -->
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-widest text-brex-pewter mb-1.5">
                                        NIK (KTP)
                                    </label>
                                    <input type="text" wire:model.defer="passengers.{{ $index }}.nik"
                                        maxlength="16"
                                        class="brex-input text-sm font-mono"
                                        placeholder="16 Digit NIK KTP" />
                                    @error("passengers.{$index}.nik")
                                        <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($passengers) < min(6, $availableSeats))
                    <button type="button" wire:click="addPassenger"
                            class="w-full py-3 border-2 border-dashed border-brex-mist hover:border-brex-ember hover:bg-brex-ember/5 text-xs font-semibold text-brex-ink rounded-brex transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-brex-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Tambah Penumpang Lain (Beli {{ count($passengers) + 1 }} Tiket)</span>
                    </button>
                @endif

                <!-- Total Estimasi -->
                <div class="p-4 rounded-brex bg-brex-fog border border-brex-mist flex items-center justify-between">
                    <div>
                        <span class="text-xs text-brex-pewter uppercase tracking-wider font-medium block">Total Biaya Tiket</span>
                        <span class="text-xs text-brex-graphite">{{ count($passengers) }} x Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}</span>
                    </div>
                    <span class="text-xl font-bold text-brex-ink tracking-brex-24">
                        Rp {{ number_format($schedule->route->harga * count($passengers), 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-brex-mist">
                    <a href="{{ route('schedules.search') }}" wire:navigate 
                        class="brex-btn-secondary text-xs px-4 py-2.5 font-medium">
                        &larr; Ganti Jadwal
                    </a>
                    <button type="submit" class="brex-btn-ember text-xs px-6 py-2.5 font-semibold">
                        Lanjut Pilih Kursi ({{ count($passengers) }} Kursi) &rarr;
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
