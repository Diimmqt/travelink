<x-app-layout :hide-nav="true">
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Top Header Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-brex-mist print:hidden">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" wire:navigate>
                    <div class="w-8 h-8 rounded-brex bg-brex-ink flex items-center justify-center text-white font-bold text-base">
                        T
                    </div>
                    <span class="font-semibold text-xl tracking-brex-24 text-brex-ink">
                        Travelink
                    </span>
                </a>

            

            <!-- Alert Notification status -->
            @if($ticket->status === 'paid')
                <div class="p-4 rounded-brex text-center text-xs font-semibold flex items-center justify-between gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Pembayaran Berhasil! Tiket Anda aktif dan siap digunakan untuk boarding.</span>
                    </div>
                    <form action="{{ route('tickets.refund', $ticket) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengajukan refund/reschedule untuk tiket ini?')">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 rounded-brex text-xs font-medium transition-colors">
                            Ajukan Refund / Reschedule
                        </button>
                    </form>
                </div>
            @elseif($ticket->status === 'refund_requested')
                <div class="p-4 rounded-brex text-center text-xs font-semibold flex items-center justify-center gap-2 bg-amber-50 border border-amber-200 text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Permohonan Refund / Reschedule Anda telah diajukan dan sedang menunggu persetujuan Admin.
                </div>
            @elseif($ticket->status === 'pending')
                <div class="p-4 rounded-brex text-center text-xs font-semibold flex items-center justify-center gap-2 bg-amber-50 border border-amber-200 text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu konfirmasi pembayaran... Selesaikan di Midtrans.
                </div>
            @elseif(in_array($ticket->status, ['expired','refunded']))
                <div class="p-4 rounded-brex text-center text-xs font-semibold flex items-center justify-center gap-2 bg-red-50 border border-red-200 text-red-600">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Tiket tidak aktif (Status: {{ strtoupper($ticket->status) }}).
                </div>
            @endif

            <!-- Boarding Pass Card Wrapper -->
            <div class="brex-card overflow-hidden flex flex-col md:flex-row print:bg-white print:text-black">
                
                <!-- Left Panel: Trip Details -->
                <div class="md:w-2/3 p-6 md:p-8 space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs uppercase tracking-widest block font-semibold text-brex-pewter">
                                Boarding Pass
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-semibold text-brex-ink tracking-brex-24 mt-1">
                                {{ $ticket->schedule->route->kota_asal }} &rarr; {{ $ticket->schedule->route->kota_tujuan }}
                            </h2>
                            <p class="text-xs mt-1 text-brex-graphite font-medium">
                                {{ \Carbon\Carbon::parse($ticket->schedule->waktu_berangkat)->isoFormat('dddd, D MMMM Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wider rounded-brex-chip {{ $ticket->status === 'paid' || $ticket->status === 'boarded' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-amber-50 border border-amber-200 text-amber-800' }}">
                            {{ $ticket->status }}
                        </span>
                    </div>

                    <!-- Inner Grid Info -->
                    <div class="grid grid-cols-2 gap-y-5 gap-x-6 text-xs pt-4 border-t border-brex-mist">
                        <div>
                            <span class="block uppercase tracking-wider mb-1 text-brex-pewter font-medium">Nama Penumpang</span>
                            <span class="font-semibold text-sm text-brex-ink tracking-brex-24">{{ $ticket->nama_penumpang }}</span>
                        </div>
                        <div>
                            <span class="block uppercase tracking-wider mb-1 text-brex-pewter font-medium">Jam Berangkat</span>
                            <span class="font-semibold text-lg text-brex-ink tracking-brex-24">
                                {{ \Carbon\Carbon::parse($ticket->schedule->waktu_berangkat)->format('H:i') }} <span class="text-xs text-brex-pewter">WIB</span>
                            </span>
                        </div>
                        <div>
                            <span class="block uppercase tracking-wider mb-1 text-brex-pewter font-medium">Nomor Kursi</span>
                            <span class="text-2xl font-bold text-brex-ember tracking-brex-24">{{ $ticket->seat->nomor_kursi }}</span>
                        </div>
                        <div>
                            <span class="block uppercase tracking-wider mb-1 text-brex-pewter font-medium">Armada</span>
                            <span class="font-semibold text-brex-ink">{{ $ticket->schedule->vehicle->jenis }}</span>
                            <span class="block text-xs mt-0.5 text-brex-graphite">{{ $ticket->schedule->vehicle->plat_nomor }}</span>
                        </div>
                        <div class="col-span-2 pt-3 border-t border-brex-mist">
                            <span class="block uppercase tracking-wider mb-1 text-brex-pewter font-medium">Titik Penjemputan</span>
                            <span class="font-semibold text-brex-ink">{{ $ticket->pickupPoint->nama_titik }}</span>
                            <span class="block text-xs mt-0.5 leading-relaxed text-brex-graphite">{{ $ticket->pickupPoint->alamat }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block uppercase tracking-wider mb-1 text-brex-pewter font-medium">Titik Penurunan</span>
                            <span class="font-semibold text-brex-ink">{{ $ticket->dropoffPoint->nama_titik }}</span>
                            <span class="block text-xs mt-0.5 leading-relaxed text-brex-graphite">{{ $ticket->dropoffPoint->alamat }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: QR Code & Stub -->
                <div class="md:w-1/3 p-6 md:p-8 flex flex-col items-center justify-center text-center border-t md:border-t-0 md:border-l border-brex-mist bg-brex-fog">
                    <div class="p-3.5 bg-white rounded-brex border border-brex-mist shadow-sm inline-block">
                        {!! $qrCode !!}
                    </div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest mt-4 text-brex-ink">
                        Boarding QR Code
                    </h4>
                    <p class="text-xs mt-1 max-w-[170px] text-brex-graphite">
                        Pindai kode QR ini saat menaiki armada di titik jemput.
                    </p>
                    <span class="text-[10px] font-mono px-2.5 py-1 rounded-brex-chip mt-3 select-all bg-white border border-brex-mist text-brex-ink font-semibold">
                        {{ strtoupper(substr($ticket->qr_token, 0, 8)) }}
                    </span>
                </div>

            </div>

            <!-- Ticket Actions -->
            <div class="flex justify-between items-center pt-2 print:hidden">
                <a href="{{ route('tickets.history') }}" wire:navigate 
                    class="brex-btn-secondary text-xs px-4 py-2.5 font-medium">
                    &larr; Riwayat Pemesanan
                </a>
                <button onclick="window.print()" 
                    class="brex-btn-ember text-xs px-5 py-2.5 inline-flex items-center gap-2 cursor-pointer font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Boarding Pass</span>
                </button>
            </div>

        </div>
    </div>
</x-app-layout>
