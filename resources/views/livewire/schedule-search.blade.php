<div class="space-y-6">
    <!-- Search Form Panel -->
    <form wire:submit.prevent="search">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

            <!-- Kota Asal -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-brex-pewter mb-1.5">
                    Kota Asal
                </label>
                <div class="relative">
                    <select wire:model="kota_asal"
                            class="brex-input pl-9 pr-4 py-2.5 appearance-none text-sm font-medium">
                        <option value="">Pilih Kota Asal</option>
                        @foreach($availableAsal as $asal)
                            <option value="{{ $asal }}">{{ $asal }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-brex-steel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                @error('kota_asal')
                    <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kota Tujuan -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-brex-pewter mb-1.5">
                    Kota Tujuan
                </label>
                <div class="relative">
                    <select wire:model="kota_tujuan"
                            class="brex-input pl-9 pr-4 py-2.5 appearance-none text-sm font-medium">
                        <option value="">Pilih Kota Tujuan</option>
                        @foreach($availableTujuan as $tujuan)
                            <option value="{{ $tujuan }}">{{ $tujuan }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-brex-steel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                @error('kota_tujuan')
                    <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-widest text-brex-pewter mb-1.5">
                    Tanggal Keberangkatan
                </label>
                <div class="relative">
                    <input type="date" wire:model="tanggal"
                           class="brex-input pl-9 pr-4 py-2.5 text-sm font-medium">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-brex-steel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                @error('tanggal')
                    <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Submit button — Ember filled button -->
        <button type="submit"
                wire:loading.attr="disabled"
                class="brex-btn-ember w-full h-11 text-base font-semibold"
                wire:loading.class="opacity-70 cursor-not-allowed">
            <span wire:loading.remove wire:target="search" class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Cari Jadwal</span>
            </span>
            <span wire:loading wire:target="search" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Mencari...</span>
            </span>
        </button>

        <!-- Button Tiket Saya -->
        <a href="{{ route('tickets.history') }}" wire:navigate
           class="brex-btn-secondary w-full h-11 mt-3 text-sm flex items-center justify-center gap-2 font-medium">
            <svg class="w-4 h-4 text-brex-graphite" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <span>Tiket Saya</span>
        </a>
    </form>

    <!-- Results / States -->
    <div>
        @if(!$hasSearched)
            <!-- Initial state -->
            <div class="text-center py-8 px-4 border-t border-brex-mist">
                <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center rounded-brex bg-brex-fog border border-brex-mist text-brex-graphite">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-brex-ink mb-1">Mulai Pencarian</p>
                <p class="text-xs text-brex-graphite max-w-xs mx-auto">
                    Pilih kota asal, tujuan, dan tanggal, lalu klik <strong class="text-brex-ink">Cari Jadwal</strong>.
                </p>
            </div>

        @elseif($schedules->isEmpty())
            <!-- Empty state -->
            <div class="text-center py-8 px-4 border-t border-brex-mist">
                <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center rounded-brex bg-red-50 border border-red-200 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-brex-ink mb-1">Jadwal Tidak Ditemukan</p>
                <p class="text-xs text-brex-graphite max-w-xs mx-auto">
                    Tidak ada jadwal untuk <strong class="text-brex-ink">{{ $kota_asal }} → {{ $kota_tujuan }}</strong> pada tanggal tersebut.
                </p>
            </div>

        @else
            <!-- Results -->
            <div class="border-t border-brex-mist pt-5 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-brex-pewter uppercase tracking-widest">Hasil Pencarian</p>
                    <span class="text-xs px-2.5 py-1 rounded-brex-chip bg-brex-fog border border-brex-mist text-brex-ink font-semibold">
                        {{ $schedules->count() }} jadwal
                    </span>
                </div>

                @foreach($schedules as $schedule)
                <div class="brex-card p-4 hover:border-brex-ink transition-colors duration-150">
                    <div class="flex items-start justify-between gap-4">

                        <!-- Time + route info -->
                        <div class="flex-1 min-w-0 space-y-2">
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-semibold text-brex-ink tracking-brex-24">
                                    {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->format('H:i') }}
                                </span>
                                <span class="text-xs font-medium text-brex-pewter">WIB</span>
                            </div>

                            <div class="flex items-center gap-2 text-sm font-medium text-brex-ink">
                                <span>{{ $schedule->route->kota_asal ?? '-' }}</span>
                                <svg class="w-3.5 h-3.5 text-brex-graphite shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                                <span>{{ $schedule->route->kota_tujuan ?? '-' }}</span>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-brex-pewter">
                                <span>±{{ $schedule->route->estimasi_durasi_menit ?? '120' }} mnt</span>
                                <span>·</span>
                                <span>{{ $schedule->vehicle->jenis ?? 'Shuttle' }}</span>
                                <span>·</span>
                                <span class="{{ ($schedule->sisa_kursi ?? 0) <= 3 ? 'text-red-600 font-semibold' : 'text-emerald-600 font-semibold' }}">
                                    Sisa {{ $schedule->sisa_kursi ?? 0 }} kursi
                                </span>
                            </div>
                        </div>

                        <!-- Price + CTA -->
                        <div class="shrink-0 flex flex-col items-end gap-3">
                            <div class="text-right">
                                <span class="text-xs text-brex-pewter block">per kursi</span>
                                <span class="text-base font-semibold text-brex-ink tracking-brex-24">
                                    Rp {{ number_format($schedule->route->harga ?? 0, 0, ',', '.') }}
                                </span>
                            </div>

                            @if($schedule->sisa_kursi > 0)
                                <a href="{{ route('schedules.detail', $schedule->id) }}" wire:navigate
                                   class="brex-btn-ember text-xs px-3.5 py-1.5 font-semibold">
                                    Pilih Kursi
                                </a>
                            @else
                                <span class="text-xs px-3.5 py-1.5 rounded-brex bg-brex-fog border border-brex-mist text-brex-steel cursor-not-allowed font-medium">
                                    Penuh
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
