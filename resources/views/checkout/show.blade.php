<x-app-layout>
    <x-slot name="header">
        {{ __('Checkout Pemesanan') }}
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

            <!-- Alert Error Box -->
            <div id="payment-error" class="hidden mb-6 p-4 rounded-brex flex items-center gap-3 text-xs font-medium bg-red-50 border border-red-200 text-red-600">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span id="payment-error-text"></span>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-brex flex items-center gap-3 text-xs font-medium bg-red-50 border border-red-200 text-red-600">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="brex-card overflow-hidden">
                <!-- Header Tiket -->
                <div class="p-6 md:p-8 border-b border-brex-mist bg-brex-fog">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-brex-chip text-xs font-semibold uppercase tracking-wider mb-3 bg-white border border-brex-mist text-brex-ink">
                        <span class="w-1.5 h-1.5 rounded-full bg-brex-ember"></span>
                        Konfirmasi Pembayaran
                    </span>
                    
                    <h1 class="text-2xl sm:text-3xl font-semibold text-brex-ink tracking-brex-24">
                        {{ $schedule->route->kota_asal }} &rarr; {{ $schedule->route->kota_tujuan }}
                    </h1>
                    <p class="text-xs mt-1.5 font-medium text-brex-graphite">
                        {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>

                <!-- Detail Content -->
                <div class="p-6 md:p-8 space-y-6">
                    <!-- Jam & Kendaraan -->
                    <div class="grid grid-cols-2 gap-6 pb-6 border-b border-brex-mist">
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Jam Berangkat</span>
                            <span class="text-xl font-semibold text-brex-ink tracking-brex-24">
                                {{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->format('H:i') }} <span class="text-xs text-brex-pewter">WIB</span>
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Armada</span>
                            <span class="text-sm font-semibold text-brex-ink">
                                {{ $schedule->vehicle->jenis }}
                            </span>
                            <span class="block text-xs mt-0.5 text-brex-graphite">{{ $schedule->vehicle->plat_nomor }}</span>
                        </div>
                    </div>

                    <!-- Lokasi Naik/Turun -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-brex-mist">
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Titik Jemput (Naik)</span>
                            <span class="text-sm font-semibold text-brex-ink">{{ $pickup->nama_titik }}</span>
                            <span class="block text-xs mt-1 leading-relaxed text-brex-graphite">{{ $pickup->alamat }}</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Titik Turun</span>
                            <span class="text-sm font-semibold text-brex-ink">{{ $dropoff->nama_titik }}</span>
                            <span class="block text-xs mt-1 leading-relaxed text-brex-graphite">{{ $dropoff->alamat }}</span>
                        </div>
                    </div>

                    <!-- Kursi & Nama Penumpang -->
                    <div class="grid grid-cols-2 gap-6 pb-6 border-b border-brex-mist">
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Nomor Kursi</span>
                            <span class="text-2xl font-bold text-brex-ember tracking-brex-24">{{ $seat->nomor_kursi }}</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Nama Penumpang</span>
                            <span class="text-base font-semibold text-brex-ink tracking-brex-24">{{ $booking['nama_penumpang'] }}</span>
                        </div>
                    </div>

                    <!-- Harga Total -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Total Pembayaran</span>
                            <span class="text-3xl font-bold text-brex-ink tracking-brex-36">
                                Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold px-3.5 py-2 rounded-brex-chip bg-brex-fog border border-brex-mist text-brex-graphite">
                            <span class="w-2 h-2 rounded-full bg-brex-ember animate-pulse"></span>
                            <span>Sesi lock:</span>
                            <span class="text-brex-ink font-semibold">
                                {{ $seat->locked_until ? \Carbon\Carbon::parse($seat->locked_until)->diffForHumans(null, true) : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Button Footer -->
                <div class="p-6 md:p-8 border-t border-brex-mist flex items-center justify-between bg-brex-fog">
                    <a href="{{ route('booking.passenger') }}" wire:navigate 
                        class="brex-btn-secondary text-xs px-4 py-2.5 font-medium">
                        &larr; Kembali
                    </a>

                    <button type="button" id="btn-pay" class="brex-btn-ember text-xs px-6 py-2.5 font-semibold cursor-pointer">
                        <span id="btn-pay-content" class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Bayar Sekarang</span>
                        </span>
                        <span id="btn-pay-loading" class="hidden inline-flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Membuka Pembayaran...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const payBtn = document.getElementById('btn-pay');
                const btnContent = document.getElementById('btn-pay-content');
                const btnLoading = document.getElementById('btn-pay-loading');
                const errorBox = document.getElementById('payment-error');
                const errorText = document.getElementById('payment-error-text');

                function showError(msg) {
                    if (errorBox && errorText) {
                        errorText.textContent = msg;
                        errorBox.classList.remove('hidden');
                        errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        alert(msg);
                    }
                }

                function hideError() {
                    if (errorBox) {
                        errorBox.classList.add('hidden');
                    }
                }

                function setLoading(isLoading) {
                    if (isLoading) {
                        payBtn.disabled = true;
                        btnContent.classList.add('hidden');
                        btnLoading.classList.remove('hidden');
                    } else {
                        payBtn.disabled = false;
                        btnContent.classList.remove('hidden');
                        btnLoading.classList.add('hidden');
                    }
                }

                payBtn.addEventListener('click', async function () {
                    hideError();
                    setLoading(true);

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch('{{ route("checkout.process") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();

                        if (!data.success) {
                            setLoading(false);
                            showError(data.message || 'Gagal memproses pemesanan.');
                            if (data.redirect) {
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 2000);
                            }
                            return;
                        }

                        // Langsung buka popup Midtrans Snap
                        if (window.snap && typeof window.snap.pay === 'function') {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function (result) {
                                    const trxStatus = (result && result.transaction_status) ? result.transaction_status : 'settlement';
                                    const statusCode = (result && result.status_code) ? result.status_code : '200';
                                    window.location.href = '/tickets/' + data.ticket_id + '?transaction_status=' + encodeURIComponent(trxStatus) + '&status_code=' + encodeURIComponent(statusCode);
                                },
                                onPending: function (result) {
                                    window.location.href = '/tickets/' + data.ticket_id;
                                },
                                onError: function (result) {
                                    setLoading(false);
                                    showError('Pembayaran gagal atau dibatalkan. Silakan coba metode lain.');
                                },
                                onClose: function () {
                                    setLoading(false);
                                    window.location.href = '{{ route('checkout.show') }}';
                                }
                            });
                        } else {
                            setLoading(false);
                            showError('Komponen Midtrans Snap belum termuat. Mohon refresh halaman dan coba kembali.');
                        }

                    } catch (err) {
                        setLoading(false);
                        showError('Terjadi kesalahan jaringan: ' + err.message);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
