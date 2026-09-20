@extends('layouts.admin')

@section('title', 'Validasi Boarding Tiket')

@section('content')
<div class="space-y-8 max-w-4xl">
    <div>
        <h1 class="text-3xl font-semibold text-brex-ink tracking-brex-36">Validasi Boarding Penumpang</h1>
        <p class="text-sm text-brex-pewter mt-1">Masukkan atau scan QR Token E-Ticket penumpang saat hendak naik armada.</p>
    </div>

    <!-- Scan / Input Form Card -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none">
        <form id="boarding-form" action="{{ route('admin.boarding.validate') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="qr_token" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider">
                        Kode Unik / QR Token E-Ticket
                    </label>
                    <button type="button" id="btn-toggle-camera"
                        class="text-xs font-semibold text-brex-ember hover:text-[#e04f00] flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span id="btn-camera-text">Scan dengan Kamera</span>
                    </button>
                </div>

                <!-- Interactive Camera Viewfinder Box -->
                <div id="camera-box" class="hidden mb-4 p-4 bg-brex-fog border border-dashed border-brex-mist rounded-brex text-center">
                    <div class="max-w-xs mx-auto overflow-hidden rounded-brex border border-brex-mist bg-black relative">
                        <div id="qr-reader" style="width: 100%;"></div>
                    </div>
                    <p class="text-xs text-brex-pewter mt-2">Arahkan kamera ke QR Code boarding pass tiket penumpang.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="qr_token" id="qr_token" required autofocus autocomplete="off"
                        placeholder="Contoh: 487C2BA8 atau scan QR Code..."
                        class="flex-1 bg-brex-paper border border-brex-mist rounded-brex px-4 py-3 text-brex-ink text-base focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel font-mono uppercase">
                    <button type="submit" class="px-8 py-3 bg-brex-ember text-white font-medium text-base rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none flex items-center justify-center gap-2 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Validasi</span>
                    </button>
                </div>

                <p class="text-xs text-brex-pewter mt-2.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-brex-steel shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Masukkan <strong>8 karakter kode unik</strong> yang ada di bawah QR tiket (contoh: <span class="font-mono text-brex-ink font-semibold">487C2BA8</span>), barcode scanner gun, atau klik tombol kamera.</span>
                </p>

                @error('qr_token')
                    <p class="text-xs text-rose-600 mt-2">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>

    <!-- Hasil Validasi (Conditional Result Card) -->
    @if(session('validation_result'))
        @php $res = session('validation_result'); @endphp
        <div class="rounded-brex p-6 border shadow-none transition-all {{ $res['success'] ? 'bg-emerald-50/50 border-emerald-300' : 'bg-rose-50/50 border-rose-300' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $res['success'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        @if($res['success'])
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-brex-chip {{ $res['success'] ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                {{ $res['title'] }}
                            </span>
                            <span class="text-xs text-brex-pewter font-mono">{{ now()->format('H:i:s WIB') }}</span>
                        </div>
                        <p class="text-lg font-semibold text-brex-ink mt-2">{{ $res['message'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Tiket Details jika ditemukan -->
            @if(isset($res['ticket']) && $res['ticket'])
                @php $t = $res['ticket']; @endphp
                <div class="mt-6 pt-6 border-t border-brex-mist/50 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-xs font-semibold text-brex-pewter uppercase block">Nama Penumpang</span>
                        <span class="font-semibold text-brex-ink text-base">{{ $t->nama_penumpang }}</span>
                        @if($t->nik)
                            <span class="text-xs text-brex-pewter font-mono block">NIK: {{ $t->nik }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-brex-pewter uppercase block">Rute Perjalanan</span>
                        <span class="font-semibold text-brex-ink">{{ $t->schedule->route->kota_asal ?? '' }} &rarr; {{ $t->schedule->route->kota_tujuan ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-brex-pewter uppercase block">Kursi / Armada</span>
                        <span class="font-semibold text-brex-ink">Kursi #{{ $t->seat->nomor_kursi ?? '-' }} ({{ $t->schedule->vehicle->plat_nomor ?? '' }})</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-brex-pewter uppercase block">Titik Jemput</span>
                        <span class="text-brex-graphite">{{ $t->pickupPoint->nama_titik ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-brex-pewter uppercase block">Titik Turun</span>
                        <span class="text-brex-graphite">{{ $t->dropoffPoint->nama_titik ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-brex-pewter uppercase block">Jam Keberangkatan</span>
                        <span class="text-brex-graphite">{{ \Carbon\Carbon::parse($t->schedule->waktu_berangkat)->format('d M Y, H:i') }} WIB</span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Riwayat Logs Validasi -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="p-6 border-b border-brex-mist">
            <h2 class="text-lg font-semibold text-brex-ink tracking-brex-24">Riwayat Boarding Terakhir</h2>
            <p class="text-xs text-brex-pewter mt-1">Daftar 15 pemindaian boarding terbaru oleh petugas.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Waktu Scan</th>
                        <th class="py-3.5 px-6">Nama Penumpang</th>
                        <th class="py-3.5 px-6">Rute & Kursi</th>
                        <th class="py-3.5 px-6">Petugas (Admin)</th>
                        <th class="py-3.5 px-6 text-right">Hasil Scan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    @forelse ($recentLogs as $log)
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 text-xs text-brex-pewter font-mono">
                                {{ \Carbon\Carbon::parse($log->scan_time)->format('d M H:i:s') }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-brex-ink">
                                {{ $log->ticket->nama_penumpang ?? '-' }}
                                @if(!empty($log->ticket->nik))
                                    <span class="text-[11px] text-brex-pewter font-mono block">NIK: {{ $log->ticket->nik }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-brex-graphite">
                                {{ $log->ticket->schedule->route->kota_asal ?? '' }} - {{ $log->ticket->schedule->route->kota_tujuan ?? '' }}
                                <span class="text-xs text-brex-pewter block">Kursi #{{ $log->ticket->seat->nomor_kursi ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-brex-graphite">
                                {{ $log->admin->nama ?? 'Admin' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($log->result === 'accepted')
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-brex-chip">
                                        Diterima
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase bg-rose-100 text-rose-800 border border-rose-300 rounded-brex-chip">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-brex-pewter">
                                Belum ada riwayat aktivitas boarding scan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;
    let cameraActive = false;
    const btnToggle = document.getElementById('btn-toggle-camera');
    const btnText = document.getElementById('btn-camera-text');
    const cameraBox = document.getElementById('camera-box');
    const qrInput = document.getElementById('qr_token');
    const form = document.getElementById('boarding-form');

    btnToggle.addEventListener('click', function () {
        if (!cameraActive) {
            startCamera();
        } else {
            stopCamera();
        }
    });

    function startCamera() {
        cameraBox.classList.remove('hidden');
        btnText.innerText = 'Tutup Kamera';
        cameraActive = true;

        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 220, height: 220 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                // Success scan
                qrInput.value = decodedText;
                stopCamera();
                form.submit();
            },
            (errorMessage) => {
                // Scanning frame parse error (ignored during scan)
            }
        ).catch((err) => {
            alert('Tidak dapat mengakses kamera: ' + err);
            stopCamera();
        });
    }

    function stopCamera() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(() => {});
        }
        cameraBox.classList.add('hidden');
        btnText.innerText = 'Scan dengan Kamera';
        cameraActive = false;
    }
</script>
@endsection
