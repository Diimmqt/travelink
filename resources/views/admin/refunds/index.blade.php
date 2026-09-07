@extends('layouts.admin')

@section('title', 'Persetujuan Refund & Reschedule')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-semibold text-brex-ink tracking-brex-36">Persetujuan Refund & Reschedule</h1>
        <p class="text-sm text-brex-pewter mt-1">Kelola permohonan pengembalian dana atau pembatalan tiket dari pembeli.</p>
    </div>

    <!-- Status Tabs / Filter -->
    <div class="flex items-center gap-2 border-b border-brex-mist pb-4">
        <a href="{{ route('admin.refunds.index', ['status' => 'refund_requested']) }}"
           class="px-4 py-2 rounded-brex text-xs font-semibold transition-colors {{ $status === 'refund_requested' ? 'bg-brex-ember text-white' : 'bg-brex-paper text-brex-graphite border border-brex-mist hover:bg-brex-fog' }}">
            Pengajuan Pending
        </a>
        <a href="{{ route('admin.refunds.index', ['status' => 'refunded']) }}"
           class="px-4 py-2 rounded-brex text-xs font-semibold transition-colors {{ $status === 'refunded' ? 'bg-brex-ember text-white' : 'bg-brex-paper text-brex-graphite border border-brex-mist hover:bg-brex-fog' }}">
            Riwayat Refunded
        </a>
        <a href="{{ route('admin.refunds.index', ['status' => 'all']) }}"
           class="px-4 py-2 rounded-brex text-xs font-semibold transition-colors {{ $status === 'all' ? 'bg-brex-ember text-white' : 'bg-brex-paper text-brex-graphite border border-brex-mist hover:bg-brex-fog' }}">
            Semua Data
        </a>
    </div>

    <!-- Table List Tiket Refund -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Tiket & Penumpang</th>
                        <th class="py-3.5 px-6">Rute & Waktu</th>
                        <th class="py-3.5 px-6">Kursi</th>
                        <th class="py-3.5 px-6">Nominal Transaksi</th>
                        <th class="py-3.5 px-6">Status Tiket</th>
                        <th class="py-3.5 px-6 text-right">Tindakan Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <!-- Tiket & Penumpang -->
                            <td class="py-4 px-6 font-medium text-brex-ink">
                                <div class="font-semibold">{{ $ticket->nama_penumpang }}</div>
                                <div class="text-xs text-brex-pewter font-mono mt-0.5">Token: {{ strtoupper(substr($ticket->qr_token, 0, 8)) }}</div>
                                <div class="text-[11px] text-brex-steel mt-0.5">{{ $ticket->user->email ?? '-' }}</div>
                            </td>

                            <!-- Rute & Waktu -->
                            <td class="py-4 px-6 text-brex-ink">
                                <div class="font-semibold">{{ $ticket->schedule->route->kota_asal ?? '' }} &rarr; {{ $ticket->schedule->route->kota_tujuan ?? '' }}</div>
                                <div class="text-xs text-brex-pewter">
                                    {{ \Carbon\Carbon::parse($ticket->schedule->waktu_berangkat)->format('d M Y, H:i') }} WIB
                                </div>
                            </td>

                            <!-- Kursi -->
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 text-xs font-bold bg-brex-fog text-brex-ember border border-brex-mist rounded-brex-chip">
                                    Kursi #{{ $ticket->seat->nomor_kursi ?? '-' }}
                                </span>
                            </td>

                            <!-- Nominal -->
                            <td class="py-4 px-6 font-semibold text-brex-ink">
                                Rp {{ number_format($ticket->transaction->amount ?? $ticket->schedule->route->harga ?? 0, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                @if($ticket->status === 'refund_requested')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 rounded-brex-chip">
                                        Refund Requested
                                    </span>
                                @elseif($ticket->status === 'refunded')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 rounded-brex-chip">
                                        Refunded
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-brex-chip">
                                        {{ strtoupper($ticket->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tindakan Persetujuan -->
                            <td class="py-4 px-6 text-right space-x-2">
                                @if($ticket->status === 'refund_requested')
                                    <form action="{{ route('admin.refunds.approve', $ticket) }}" method="POST" class="inline-block" onsubmit="return confirm('Setujui refund untuk tiket ini? Kursi akan kembali dibebaskan.')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-brex-ember text-white text-xs font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.refunds.reject', $ticket) }}" method="POST" class="inline-block" onsubmit="return confirm('Tolak permohonan refund? Status tiket akan dikembalikan ke LUNAS.')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 border border-brex-mist text-brex-ink text-xs font-medium rounded-brex hover:bg-brex-fog transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-brex-pewter italic">Sudah Diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-brex-pewter">
                                Tidak ada data permohonan refund/reschedule pada kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="p-4 border-t border-brex-mist bg-brex-fog/30">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
