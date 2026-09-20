<x-app-layout :hide-nav="true">
    <style>
        /* ── Page background ─────────────────────────────── */
        .ticket-page-bg {
            min-height: 100vh;
            background: linear-gradient(145deg, #f0f4ff 0%, #f8fafc 50%, #fff7f0 100%);
            display: flex;
            flex-direction: column;
        }

        /* ── Top navbar bar ──────────────────────────────── */
        .ticket-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(8px);
        }

        /* ── Boarding pass wrapper ───────────────────────── */
        .boarding-pass-wrapper {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem 3rem;
        }

        .boarding-pass-container {
            width: 100%;
            max-width: 760px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ── Status alert ────────────────────────────────── */
        .status-alert {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 500;
            letter-spacing: -0.01em;
        }
        .status-alert.paid    { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .status-alert.pending { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .status-alert.refund  { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .status-alert.expired { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .status-dot.green  { background: #22c55e; animation: pulse 2s infinite; }
        .status-dot.amber  { background: #f59e0b; animation: pulse 2s infinite; }
        .status-dot.red    { background: #ef4444; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* ── The boarding pass card ──────────────────────── */
        .pass-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
            display: grid;
            grid-template-columns: 1fr;
        }

        @media (min-width: 640px) {
            .pass-card {
                grid-template-columns: 1fr auto 240px;
            }
        }

        /* ── Left panel ──────────────────────────────────── */
        .pass-left {
            padding: 1.75rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .pass-label {
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .route-heading {
            font-size: 1.625rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.025em;
            line-height: 1.1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .route-arrow {
            font-size: 1rem;
            color: #ff5900;
            font-weight: 400;
        }

        .pass-date {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            margin-top: -0.5rem;
        }

        .pass-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem 1.25rem;
            padding-top: 1rem;
            border-top: 1px dashed #e2e8f0;
        }

        .pass-field {}
        .pass-field-label {
            font-size: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 0.25rem;
        }
        .pass-field-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }
        .pass-field-value.large {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ff5900;
            letter-spacing: -0.03em;
        }
        .pass-field-value.time {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
        }
        .pass-field-sub {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 400;
            margin-top: 0.125rem;
            line-height: 1.4;
        }
        .pass-field-wide {
            grid-column: span 2;
            padding-top: 0.75rem;
            border-top: 1px dashed #e2e8f0;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .chip-paid    { background: #dcfce7; color: #15803d; }
        .chip-pending { background: #fef9c3; color: #854d0e; }
        .chip-expired { background: #fee2e2; color: #991b1b; }
        .chip-boarded { background: #dbeafe; color: #1d4ed8; }

        /* ── Perforation divider ─────────────────────────── */
        .pass-divider {
            display: none;
            width: 1px;
            position: relative;
            background: transparent;
            margin: 1rem 0;
        }
        @media (min-width: 640px) {
            .pass-divider {
                display: flex;
                align-items: center;
                flex-direction: column;
                justify-content: center;
            }
        }
        .pass-divider::before,
        .pass-divider::after {
            content: '';
            width: 24px; height: 24px;
            border-radius: 50%;
            background: linear-gradient(145deg, #f0f4ff 0%, #f8fafc 50%, #fff7f0 100%);
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .pass-divider-line {
            flex: 1;
            border-left: 2px dashed #e2e8f0;
            margin: 4px 0;
        }
        .pass-horizontal-divider {
            display: flex;
            align-items: center;
            gap: 0;
            margin: 0 1.75rem;
        }
        @media (min-width: 640px) {
            .pass-horizontal-divider { display: none; }
        }
        .pass-horizontal-divider::before,
        .pass-horizontal-divider::after {
            content: '';
            width: 24px; height: 24px;
            border-radius: 50%;
            background: linear-gradient(145deg, #f0f4ff 0%, #f8fafc 50%, #fff7f0 100%);
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .pass-horizontal-line {
            flex: 1;
            border-top: 2px dashed #e2e8f0;
        }

        /* ── Right panel (QR stub) ───────────────────────── */
        .pass-right {
            padding: 1.75rem 1.25rem;
            background: #fafbff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 0.875rem;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        @media (min-width: 640px) {
            .pass-right {
                width: 240px;
            }
        }

        .qr-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 156px;
            height: 156px;
            box-sizing: border-box;
            overflow: hidden;
        }
        .qr-box svg, .qr-box img {
            width: 136px !important;
            height: 136px !important;
            max-width: 100% !important;
            max-height: 100% !important;
            display: block;
            margin: auto;
        }

        .qr-locked-box {
            width: 156px;
            height: 156px;
            border-radius: 14px;
            border: 2px dashed #fcd34d;
            background: #fffdf5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            box-sizing: border-box;
        }
        .qr-locked-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fef3c7;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-locked-badge {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #b45309;
        }

        .qr-title {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0f172a;
        }
        .qr-hint {
            font-size: 0.7rem;
            color: #64748b;
            line-height: 1.5;
            max-width: 160px;
        }
        .qr-token {
            font-family: 'SF Mono', 'Consolas', monospace;
            font-size: 0.7rem;
            font-weight: 600;
            color: #0f172a;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            letter-spacing: 0.12em;
            user-select: all;
        }

        /* ── Action bar ──────────────────────────────────── */
        .action-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.625rem 1rem;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 0.8125rem;
            font-weight: 500;
            letter-spacing: -0.01em;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .btn-back:hover {
            background: #f8fafc;
            border-color: #0f172a;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            background: #ff5900;
            color: #fff;
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            cursor: pointer;
            border: none;
            transition: background 0.15s ease, transform 0.1s ease;
        }
        .btn-print:hover {
            background: #e04e00;
            transform: translateY(-1px);
        }
        .btn-print:active {
            transform: translateY(0);
        }

        .btn-refund {
            padding: 0.375rem 0.75rem;
            background: #fff;
            border: 1px solid #86efac;
            border-radius: 8px;
            color: #15803d;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .btn-refund:hover { background: #f0fdf4; }

        /* ── Print styles ────────────────────────────────── */
        @media print {
            .ticket-topbar, .action-bar, .status-alert { display: none !important; }
            .ticket-page-bg { background: #fff !important; min-height: unset; }
            .boarding-pass-wrapper { padding: 1rem !important; }
            .pass-card { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>

    <div class="ticket-page-bg">

        {{-- ── Top navigation bar ── --}}
        <div class="ticket-topbar print:hidden">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5" wire:navigate>
                <div class="w-8 h-8 rounded-xl bg-black flex items-center justify-center text-white font-bold text-sm">
                    T
                </div>
                <span class="font-semibold text-lg tracking-tight text-black">Travelink</span>
            </a>
            <span class="text-xs text-slate-500 font-medium hidden sm:block">E-Ticket / Boarding Pass</span>
        </div>

        <div class="boarding-pass-wrapper">
            <div class="boarding-pass-container">

                {{-- ── Status alert ── --}}
                @if($ticket->status === 'paid')
                    <div class="status-alert paid print:hidden">
                        <div class="flex items-center gap-2">
                            <span class="status-dot green"></span>
                            <span>Pembayaran berhasil! Tiket Anda aktif dan siap digunakan untuk boarding.</span>
                        </div>
                        <form action="{{ route('tickets.refund', $ticket) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin mengajukan refund/reschedule untuk tiket ini?')">
                            @csrf
                            <button type="submit" class="btn-refund">Ajukan Refund</button>
                        </form>
                    </div>
                @elseif($ticket->status === 'refund_requested')
                    <div class="status-alert refund print:hidden">
                        <span class="status-dot amber"></span>
                        <span>Permohonan Refund / Reschedule Anda sedang menunggu persetujuan Admin.</span>
                    </div>
                @elseif($ticket->status === 'pending')
                    <div class="status-alert pending print:hidden">
                        <div class="flex items-center gap-2">
                            <span class="status-dot amber"></span>
                            <span>Menunggu konfirmasi pembayaran. Selesaikan pembayaran anda.</span>
                        </div>
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn-refund" style="border-color:#fcd34d; color:#b45309; background:#fff;">
                            Cek Status Pembayaran
                        </a>
                    </div>
                @elseif(in_array($ticket->status, ['expired','refunded']))
                    <div class="status-alert expired print:hidden">
                        <span class="status-dot red"></span>
                        <span>Tiket tidak aktif &mdash; Status: <strong>{{ strtoupper($ticket->status) }}</strong></span>
                    </div>
                @endif

                @if(isset($relatedTickets) && $relatedTickets->count() > 1)
                    <div class="bg-white border border-brex-mist rounded-brex p-3.5 flex items-center justify-between gap-3 overflow-x-auto print:hidden">
                        <div class="flex items-center gap-2 shrink-0">
                            <svg class="w-4 h-4 text-brex-ember shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-xs font-semibold text-brex-ink">Tiket Rombongan ({{ $relatedTickets->count() }} Penumpang):</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @foreach($relatedTickets as $idx => $relTicket)
                                <a href="{{ route('tickets.show', $relTicket->id) }}"
                                   class="px-3 py-1.5 rounded-brex text-xs font-semibold transition-all {{ $relTicket->id == $ticket->id ? 'bg-brex-ember text-white shadow-sm' : 'bg-brex-fog border border-brex-mist text-brex-ink hover:border-brex-ember' }}">
                                    {{ $idx + 1 }}. {{ $relTicket->nama_penumpang }} (Kursi {{ $relTicket->seat->nomor_kursi ?? '-' }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── Boarding pass card ── --}}
                <div class="pass-card">

                    {{-- ── LEFT: Trip details ── --}}
                    <div class="pass-left">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-1">
                                <span class="pass-label">Boarding Pass</span>
                                @php
                                    $chipClass = match($ticket->status) {
                                        'paid', 'boarded' => 'chip-paid',
                                        'pending'          => 'chip-pending',
                                        'boarded'          => 'chip-boarded',
                                        default            => 'chip-expired',
                                    };
                                @endphp
                                <span class="status-chip {{ $chipClass }}">{{ $ticket->status }}</span>
                            </div>
                            <div class="route-heading">
                                {{ $ticket->schedule->route->kota_asal }}
                                <span class="route-arrow">→</span>
                                {{ $ticket->schedule->route->kota_tujuan }}
                            </div>
                            <div class="pass-date">
                                {{ \Carbon\Carbon::parse($ticket->schedule->waktu_berangkat)->isoFormat('dddd, D MMMM Y') }}
                            </div>
                        </div>

                        <div class="pass-grid">
                            {{-- Passenger name & NIK --}}
                            <div class="pass-field">
                                <div class="pass-field-label">Nama Penumpang</div>
                                <div class="pass-field-value">{{ $ticket->nama_penumpang }}</div>
                                @if($ticket->nik)
                                    <div class="pass-field-sub" style="font-family: monospace; font-size: 0.75rem; color: #64748b; margin-top: 2px;">NIK: {{ $ticket->nik }}</div>
                                @endif
                            </div>

                            {{-- Departure time --}}
                            <div class="pass-field">
                                <div class="pass-field-label">Jam Berangkat</div>
                                <div class="pass-field-value time">
                                    {{ \Carbon\Carbon::parse($ticket->schedule->waktu_berangkat)->format('H:i') }}
                                    <span style="font-size:0.7rem;font-weight:500;color:#94a3b8;">WIB</span>
                                </div>
                            </div>

                            {{-- Seat number --}}
                            <div class="pass-field">
                                <div class="pass-field-label">Nomor Kursi</div>
                                <div class="pass-field-value large">{{ $ticket->seat->nomor_kursi }}</div>
                            </div>

                            {{-- Vehicle --}}
                            <div class="pass-field">
                                <div class="pass-field-label">Armada</div>
                                <div class="pass-field-value">{{ $ticket->schedule->vehicle->jenis }}</div>
                                <div class="pass-field-sub">{{ $ticket->schedule->vehicle->plat_nomor }}</div>
                            </div>

                            {{-- Pickup point --}}
                            <div class="pass-field pass-field-wide">
                                <div class="pass-field-label">Titik Penjemputan</div>
                                <div class="pass-field-value">{{ $ticket->pickupPoint->nama_titik }}</div>
                                <div class="pass-field-sub">{{ $ticket->pickupPoint->alamat }}</div>
                            </div>

                            {{-- Dropoff point --}}
                            <div class="pass-field pass-field-wide" style="border-top: none; padding-top: 0;">
                                <div class="pass-field-label">Titik Penurunan</div>
                                <div class="pass-field-value">{{ $ticket->dropoffPoint->nama_titik }}</div>
                                <div class="pass-field-sub">{{ $ticket->dropoffPoint->alamat }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Perforated divider — vertical on desktop, horizontal on mobile ── --}}
                    <div class="pass-divider">
                        <div class="pass-divider-line"></div>
                    </div>
                    <div class="pass-horizontal-divider">
                        <div class="pass-horizontal-line"></div>
                    </div>

                    {{-- ── RIGHT: QR stub ── --}}
                    <div class="pass-right">
                        @if(in_array($ticket->status, ['paid', 'boarded']) && $qrCode)
                            <div class="qr-box">
                                {!! $qrCode !!}
                            </div>
                            <div class="qr-title">Boarding QR Code</div>
                            <p class="qr-hint">Pindai kode QR ini saat menaiki armada di titik penjemputan.</p>
                            <span class="qr-token">{{ strtoupper(substr($ticket->qr_token, 0, 8)) }}</span>
                        @else
                            <div class="qr-locked-box">
                                <div class="qr-locked-icon">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="qr-locked-badge">QR Code Terkunci</span>
                            </div>
                            <div class="qr-title text-amber-800">Menunggu Pembayaran</div>
                            <p class="qr-hint">QR Code boarding pass akan otomatis muncul setelah pembayaran berhasil diverifikasi.</p>
                            <span class="qr-token" style="color: #94a3b8; background: #f8fafc; border-color: #e2e8f0; letter-spacing: 0.25em;">••••••••</span>
                        @endif
                    </div>

                </div>{{-- end .pass-card --}}

                {{-- ── Action bar ── --}}
                <div class="action-bar print:hidden">
                    <a href="{{ route('tickets.history') }}" wire:navigate class="btn-back">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Riwayat Pemesanan
                    </a>
                    @if(in_array($ticket->status, ['paid', 'boarded']))
                        <button onclick="window.print()" class="btn-print">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak Boarding Pass
                        </button>
                    @endif
                </div>

            </div>{{-- end .boarding-pass-container --}}
        </div>{{-- end .boarding-pass-wrapper --}}
    </div>{{-- end .ticket-page-bg --}}
</x-app-layout>
