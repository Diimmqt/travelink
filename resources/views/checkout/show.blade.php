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
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-brex-chip text-xs font-semibold uppercase tracking-wider bg-white border border-brex-mist text-brex-ink">
                            <span class="w-1.5 h-1.5 rounded-full bg-brex-ember"></span>
                            Konfirmasi Pemesanan &amp; Pembayaran
                        </span>
                        <span class="px-2.5 py-1 rounded-brex-chip bg-brex-ember/10 text-brex-ember border border-brex-ember/20 text-xs font-semibold">
                            Langkah 3 dari 3
                        </span>
                    </div>
                    
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
                            <span class="text-sm font-semibold text-brex-ink">{{ $pickup->nama_titik ?? 'Pool Utama' }}</span>
                            <span class="block text-xs mt-1 leading-relaxed text-brex-graphite">{{ $pickup->alamat ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-wider mb-1 text-brex-pewter font-medium">Titik Turun</span>
                            <span class="text-sm font-semibold text-brex-ink">{{ $dropoff->nama_titik ?? 'Pool Utama' }}</span>
                            <span class="block text-xs mt-1 leading-relaxed text-brex-graphite">{{ $dropoff->alamat ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Rincian Daftar Penumpang & Kursi -->
                    <div class="pb-6 border-b border-brex-mist space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="block text-xs uppercase tracking-wider text-brex-pewter font-semibold">
                                Daftar Penumpang &amp; Kursi ({{ count($booking['passengers']) }} Orang)
                            </span>
                        </div>

                        <div class="divide-y divide-brex-mist/70 border border-brex-mist rounded-brex overflow-hidden">
                            @foreach($booking['passengers'] as $idx => $passenger)
                                @php
                                    $seatId = $booking['seat_ids'][$idx] ?? null;
                                    $assignedSeat = $seats->firstWhere('id', $seatId);
                                @endphp
                                <div class="p-4 bg-white flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="w-6 h-6 rounded-full bg-brex-fog text-brex-ink border border-brex-mist flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-semibold text-brex-ink truncate">{{ $passenger['nama_lengkap'] }}</h4>
                                            <p class="text-xs text-brex-pewter font-mono">NIK: {{ $passenger['nik'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs text-brex-pewter block">Kursi</span>
                                        <span class="text-base font-bold text-brex-ember">{{ $assignedSeat->nomor_kursi ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Total Pembayaran Card -->
                    <div class="p-5 rounded-brex bg-brex-fog border border-brex-mist flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="block text-[11px] font-semibold uppercase tracking-wider text-brex-pewter mb-1">
                                Total Pembayaran
                            </span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-brex-ink tracking-tight">
                                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="block text-xs text-brex-graphite mt-0.5">
                                {{ count($booking['passengers']) }} tiket &times; Rp {{ number_format($schedule->route->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-brex-chip bg-white border border-brex-mist text-brex-graphite self-start sm:self-center shrink-0">
                            <span class="w-2 h-2 rounded-full bg-brex-ember animate-pulse"></span>
                            <span>Sesi lock: 10 Menit</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ route('schedules.detail', $schedule->id) }}"
                           class="brex-btn-secondary px-5 h-12 text-sm font-semibold flex items-center justify-center gap-2 shrink-0">
                            <svg class="w-4 h-4 text-brex-graphite shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Ubah Kursi</span>
                        </a>
                        <button id="pay-button" type="button"
                                class="brex-btn-ember flex-1 h-12 text-sm font-semibold flex items-center justify-center gap-2 shadow-sm cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span id="pay-button-text">Bayar Sekarang &mdash; Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </button>
                    </div>

                    <p class="text-center text-xs text-brex-pewter pt-1">
                        🔒 Pembayaran diproses secara aman menggunakan payment gateway Midtrans.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        const payButton = document.getElementById('pay-button');
        const payButtonText = document.getElementById('pay-button-text');
        const errorBox = document.getElementById('payment-error');
        const errorText = document.getElementById('payment-error-text');

        function showError(message) {
            errorText.textContent = message;
            errorBox.classList.remove('hidden');
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        payButton.addEventListener('click', function () {
            payButton.disabled = true;
            payButtonText.textContent = 'Menyiapkan Pembayaran...';
            errorBox.classList.add('hidden');

            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    showError(data.message || 'Terjadi kesalahan saat memproses pembayaran.');
                    payButton.disabled = false;
                    payButtonText.textContent = 'Bayar Sekarang — Rp {{ number_format($totalPrice, 0, ',', '.') }}';
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 2000);
                    }
                    return;
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        const trxStatus = (result && result.transaction_status) ? result.transaction_status : 'settlement';
                        const statusCode = (result && result.status_code) ? result.status_code : '200';
                        window.location.href = '/tickets/' + data.ticket_id + '?transaction_status=' + encodeURIComponent(trxStatus) + '&status_code=' + encodeURIComponent(statusCode);
                    },
                    onPending: function (result) {
                        window.location.href = '/tickets/' + data.ticket_id + '?transaction_status=pending';
                    },
                    onError: function (result) {
                        showError('Pembayaran gagal atau dibatalkan. Silakan coba kembali.');
                        payButton.disabled = false;
                        payButtonText.textContent = 'Coba Bayar Lagi';
                    },
                    onClose: function () {
                        payButton.disabled = false;
                        payButtonText.textContent = 'Bayar Sekarang — Rp {{ number_format($totalPrice, 0, ',', '.') }}';
                    }
                });
            })
            .catch(err => {
                showError('Koneksi terganggu. Silakan coba kembali.');
                payButton.disabled = false;
                payButtonText.textContent = 'Bayar Sekarang — Rp {{ number_format($totalPrice, 0, ',', '.') }}';
            });
        });
    </script>
</x-app-layout>
