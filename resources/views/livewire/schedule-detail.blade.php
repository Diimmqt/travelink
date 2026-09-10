<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <!-- Back Navigation & Header -->
    <div class="mb-8">
        <a href="{{ route('schedules.search') }}" wire:navigate 
            class="brex-btn-secondary text-xs px-3.5 py-2 mb-4 inline-flex items-center gap-2 font-medium">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Pencarian</span>
        </a>

        <div class="brex-card p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-brex-chip text-xs font-semibold uppercase tracking-wider bg-brex-fog border border-brex-mist text-brex-graphite">
                    <span class="w-1.5 h-1.5 rounded-full bg-brex-ember"></span>
                    Detail Jadwal #{{ $schedule->id }}
                </span>
                <h2 class="text-2xl sm:text-3xl font-semibold text-brex-ink tracking-brex-24 mt-3">
                    {{ $schedule->route->kota_asal }} &rarr; {{ $schedule->route->kota_tujuan }}
                </h2>
                <div class="flex flex-wrap items-center gap-4 mt-2 text-xs font-medium text-brex-graphite">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-brex-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->isoFormat('dddd, D MMMM Y') }}
                    </span>
                    <span>·</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-brex-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->format('H:i') }} WIB
                    </span>
                </div>
            </div>
            <div class="text-left md:text-right">
                <span class="text-xs font-semibold uppercase tracking-wider block text-brex-pewter">Harga Tiket</span>
                <span class="text-3xl font-semibold block mt-0.5 text-brex-ink tracking-brex-36">
                    Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}
                </span>
                <span class="text-xs block mt-1 text-brex-pewter">
                    Armada: <span class="text-brex-ink font-medium">{{ $schedule->vehicle->jenis }} ({{ $schedule->vehicle->plat_nomor }})</span>
                </span>
            </div>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-brex flex items-center gap-3 text-xs font-medium bg-red-50 border border-red-200 text-red-600">
            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Interactive Stepper for Pickup & Dropoff -->
        <div class="lg:col-span-7 brex-card p-6 md:p-8 space-y-8">
            <div>
                <h3 class="text-lg font-semibold text-brex-ink tracking-brex-24">Rute &amp; Titik Singgah</h3>
                <p class="text-xs text-brex-graphite mt-1">Pilih lokasi penjemputan dan penurunan Anda dengan mengeklik titik di bawah.</p>
            </div>

            <!-- Stepper Timeline -->
            <div class="relative pl-7 space-y-10 before:absolute before:left-[9px] before:top-2 before:bottom-2 before:w-[1px] before:bg-brex-mist">
                
                <!-- Section 1: Pickup Points -->
                <div class="space-y-3 relative">
                    <!-- Dot Marker -->
                    <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-brex-ember ring-4 ring-brex-ember/20 z-10"></div>
                    <span class="text-xs font-semibold uppercase tracking-widest block text-brex-pewter">Titik Penjemputan (Naik)</span>
                    
                    <div class="grid grid-cols-1 gap-2.5">
                        @forelse($pickupPoints as $point)
                            @php $isSelected = $pickup_point_id == $point->id; @endphp
                            <div wire:click="$set('pickup_point_id', {{ $point->id }})"
                                class="cursor-pointer p-4 rounded-brex transition-all duration-150 flex items-start gap-3.5 group {{ $isSelected ? 'bg-brex-fog border-2 border-brex-ink' : 'bg-white border border-brex-mist hover:border-brex-graphite' }}">
                                <div class="w-4 h-4 rounded-full flex items-center justify-center mt-0.5 shrink-0 transition {{ $isSelected ? 'bg-brex-ink border-brex-ink' : 'border border-brex-mist bg-white' }}">
                                    @if($isSelected)
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-brex-ink tracking-brex-24">{{ $point->nama_titik }}</h4>
                                    <p class="text-xs mt-0.5 leading-relaxed text-brex-graphite">{{ $point->alamat }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-3.5 rounded-brex bg-brex-fog border border-brex-mist text-xs text-brex-graphite">
                                Pool Keberangkatan: <strong class="text-brex-ink">{{ $schedule->route->kota_asal }}</strong>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 2: Dropoff Points -->
                <div class="space-y-3 relative">
                    <!-- Dot Marker -->
                    <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-emerald-600 ring-4 ring-emerald-600/20 z-10"></div>
                    <span class="text-xs font-semibold uppercase tracking-widest block text-brex-pewter">Titik Penurunan (Turun)</span>

                    <div class="grid grid-cols-1 gap-2.5">
                        @forelse($dropoffPoints as $point)
                            @php $isSelected = $dropoff_point_id == $point->id; @endphp
                            <div wire:click="$set('dropoff_point_id', {{ $point->id }})"
                                class="cursor-pointer p-4 rounded-brex transition-all duration-150 flex items-start gap-3.5 group {{ $isSelected ? 'bg-brex-fog border-2 border-brex-ink' : 'bg-white border border-brex-mist hover:border-brex-graphite' }}">
                                <div class="w-4 h-4 rounded-full flex items-center justify-center mt-0.5 shrink-0 transition {{ $isSelected ? 'bg-brex-ink border-brex-ink' : 'border border-brex-mist bg-white' }}">
                                    @if($isSelected)
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-brex-ink tracking-brex-24">{{ $point->nama_titik }}</h4>
                                    <p class="text-xs mt-0.5 leading-relaxed text-brex-graphite">{{ $point->alamat }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-3.5 rounded-brex bg-brex-fog border border-brex-mist text-xs text-brex-graphite">
                                Pool Tujuan: <strong class="text-brex-ink">{{ $schedule->route->kota_tujuan }}</strong>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <!-- Right: Seating Layout Map -->
        <div class="lg:col-span-5 brex-card p-6 md:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-brex-ink tracking-brex-24">Pilih Kursi</h3>
                <p class="text-xs text-brex-graphite mt-1">Silakan klik nomor kursi yang tersedia.</p>
            </div>

            <!-- Seat Legend -->
            <div class="flex justify-between items-center gap-3 text-xs font-semibold p-3.5 rounded-brex bg-brex-fog border border-brex-mist text-brex-graphite">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-brex-chip bg-white border border-brex-mist"></div>
                    <span>Tersedia</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-brex-chip bg-brex-fog border border-brex-mist"></div>
                    <span>Locked</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-brex-chip bg-red-100 border border-red-300"></div>
                    <span>Terisi</span>
                </div>
            </div>

            <!-- Seating Layout Box resembling Minibus cabin -->
            <div class="p-5 rounded-brex bg-brex-fog border border-brex-mist relative overflow-hidden">
                <!-- Driver Section -->
                <div class="flex justify-between items-center pb-4 border-b border-brex-mist mb-5">
                    <span class="text-xs uppercase tracking-widest font-semibold text-brex-pewter">
                        Kabin Depan
                    </span>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full border-2 border-brex-mist flex items-center justify-center text-xs font-semibold text-brex-graphite">
                            <span class="text-[9px]">setir</span>
                        </div>
                    </div>
                </div>

                <!-- Programmatic seating map grid -->
                @php
                $findSeat = function($num) use ($seats) {
                    return $seats->first(function($s) use ($num) {
                        return $s->nomor_kursi === 'A' . $num || $s->nomor_kursi === (string)$num;
                    });
                };

                $rows = [];
                $count = $seats->count();

                if ($count <= 15) {
                    $rows = [
                        ['type' => 'front', 'left' => $findSeat(1), 'right' => 'supir'],
                        ['type' => 'row', 'left' => array_filter([$findSeat(2), $findSeat(3)]), 'right' => array_filter([$findSeat(4)])],
                        ['type' => 'row', 'left' => array_filter([$findSeat(5), $findSeat(6)]), 'right' => array_filter([$findSeat(7)])],
                        ['type' => 'row', 'left' => array_filter([$findSeat(8), $findSeat(9)]), 'right' => array_filter([$findSeat(10)])],
                        ['type' => 'back', 'seats' => array_filter([$findSeat(11), $findSeat(12), $findSeat(13), $findSeat(14), $findSeat(15)])]
                    ];
                } elseif ($count <= 19) {
                    $rows = [
                        ['type' => 'front', 'left' => $findSeat(1), 'right' => 'supir'],
                        ['type' => 'row', 'left' => array_filter([$findSeat(2), $findSeat(3)]), 'right' => array_filter([$findSeat(4)])],
                        ['type' => 'row', 'left' => array_filter([$findSeat(5), $findSeat(6)]), 'right' => array_filter([$findSeat(7)])],
                        ['type' => 'row', 'left' => array_filter([$findSeat(8), $findSeat(9)]), 'right' => array_filter([$findSeat(10)])],
                        ['type' => 'row', 'left' => array_filter([$findSeat(11), $findSeat(12)]), 'right' => array_filter([$findSeat(13)])],
                        ['type' => 'row', 'left' => array_filter([$findSeat(14), $findSeat(15)]), 'right' => array_filter([$findSeat(16)])],
                        ['type' => 'back', 'seats' => array_filter([$findSeat(17), $findSeat(18), $findSeat(19)])]
                    ];
                } else {
                    $rows = [
                        ['type' => 'front', 'left' => $findSeat(1), 'right' => 'supir']
                    ];
                    $current = 2;
                    while ($current <= $count - 4) {
                        $rows[] = [
                            'type' => 'row',
                            'left' => array_filter([$findSeat($current), $findSeat($current + 1)]),
                            'right' => array_filter([$findSeat($current + 2)])
                        ];
                        $current += 3;
                    }
                    $backSeats = [];
                    for ($k = $current; $k <= $count; $k++) {
                        $s = $findSeat($k);
                        if ($s) $backSeats[] = $s;
                    }
                    if (!empty($backSeats)) {
                        $rows[] = ['type' => 'back', 'seats' => $backSeats];
                    }
                }
                @endphp

                <!-- Grid Render -->
                <div class="space-y-3">
                    @foreach($rows as $index => $row)
                        @if($row['type'] == 'front')
                            <!-- Front row -->
                            <div class="grid grid-cols-4 gap-2.5 items-center">
                                <div class="col-span-2">
                                    @if(!empty($row['left']))
                                        @include('livewire.partials.seat-button', ['seat' => $row['left']])
                                    @endif
                                </div>
                                <div class="col-span-1"></div>
                                <div class="col-span-1 flex justify-center">
                                    <div class="w-full h-11 sm:h-12 rounded-brex flex items-center justify-center text-xs font-semibold bg-white border border-brex-mist text-brex-steel select-none">
                                        Supir
                                    </div>
                                </div>
                            </div>
                        @elseif($row['type'] == 'row')
                            <!-- Middle rows (Left double, aisle, Right single) -->
                            <div class="grid grid-cols-4 gap-2.5 items-center">
                                <div class="col-span-2 grid grid-cols-2 gap-2.5">
                                    @foreach($row['left'] as $seatObj)
                                        @if($seatObj)
                                            @include('livewire.partials.seat-button', ['seat' => $seatObj])
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-span-1 flex justify-center">
                                    <span class="text-[9px] font-semibold uppercase tracking-widest rotate-90 text-brex-steel select-none">Gang</span>
                                </div>
                                <div class="col-span-1">
                                    @foreach($row['right'] as $seatObj)
                                        @if($seatObj)
                                            @include('livewire.partials.seat-button', ['seat' => $seatObj])
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @elseif($row['type'] == 'back')
                            <!-- Back row -->
                            <div class="border-t border-brex-mist pt-3 mt-1">
                                <div class="grid grid-cols-5 gap-2">
                                    @foreach($row['seats'] as $seatObj)
                                        @if($seatObj)
                                            @include('livewire.partials.seat-button', ['seat' => $seatObj])
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
