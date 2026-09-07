<x-app-layout :hide-nav="true">
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Title Header -->
            <div class="flex items-center justify-between pb-4 border-b border-brex-mist">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Riwayat Pemesanan
                </h1>
                <a href="{{ route('home') }}" wire:navigate 
                   class="brex-btn-secondary text-xs px-3.5 py-2 font-medium">
                    &larr; Beranda
                </a>
            </div>

            @if($tickets->isEmpty())
                <div class="bg-white border border-brex-mist rounded-[16px] p-16 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 flex items-center justify-center rounded-brex bg-brex-fog border border-brex-mist text-brex-ink font-bold text-lg">
                        🎟️
                    </div>
                    <h3 class="text-lg font-semibold text-brex-ink tracking-brex-24">Belum Ada Pemesanan</h3>
                    <p class="text-xs mt-1 max-w-sm mx-auto text-brex-graphite">Tiket perjalanan yang telah Anda pesan akan muncul di sini.</p>
                    <a href="{{ route('home') }}" wire:navigate 
                       class="brex-btn-ember text-xs px-5 py-2.5 mt-5 inline-flex items-center gap-2 font-semibold">
                        <span>Cari Jadwal Sekarang</span>
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($tickets as $ticket)
                        @php
                            $isPaid = in_array($ticket->status, ['paid', 'boarded']);
                            $isPending = $ticket->status === 'pending';
                            $isExpired = in_array($ticket->status, ['expired', 'refunded']);
                        @endphp

                        <div class="bg-white border border-gray-300/80 rounded-[16px] px-6 py-5 hover:border-gray-400 transition-all duration-150">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1.5">
                                    <a href="{{ $isPaid ? route('tickets.show', $ticket->id) : '#' }}" 
                                       class="flex items-center gap-2 text-base font-bold text-gray-900 tracking-tight hover:text-brex-ember transition-colors">
                                        <span>{{ $ticket->schedule->route->kota_asal }}</span>
                                        <span class="text-gray-600 font-normal">&rarr;</span>
                                        <span>{{ $ticket->schedule->route->kota_tujuan }}</span>
                                    </a>

                                    <div class="flex flex-wrap items-center gap-2.5 text-xs text-gray-400 font-normal">
                                        <span>{{ \Carbon\Carbon::parse($ticket->schedule->waktu_berangkat)->format('d M Y, H:i') }} WIB</span>
                                        <span>·</span>
                                        <span>Kursi: <strong class="text-gray-900 font-bold">{{ $ticket->seat->nomor_kursi }}</strong></span>
                                        <span>·</span>
                                        <span>Penumpang: {{ $ticket->nama_penumpang }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if($isPaid)
                                        <a href="{{ route('tickets.show', $ticket->id) }}" wire:navigate
                                           class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-lg border bg-emerald-50/60 border-emerald-300 text-emerald-700 hover:bg-emerald-100 transition">
                                            PAID
                                        </a>
                                    @elseif($isPending)
                                        <a href="{{ route('checkout.show') }}" wire:navigate
                                           class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-lg border bg-amber-50/60 border-amber-300 text-amber-700 hover:bg-amber-100 transition">
                                            PENDING
                                        </a>
                                    @else
                                        <span class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-lg border bg-red-50/60 border-red-200 text-red-500">
                                            {{ strtoupper($ticket->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <div>
                        {{ $tickets->links() }}
                    </div>
                </div>

                <!-- Bottom Button to return to Beranda -->
                <div class="pt-6 border-t border-brex-mist flex justify-center">
                    <a href="{{ route('home') }}" wire:navigate
                       class="brex-btn-secondary px-6 py-3 text-sm font-semibold inline-flex items-center gap-2">
                        <span>&larr;</span>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
