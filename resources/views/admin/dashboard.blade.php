@extends('layouts.admin')

@section('title', 'Dashboard Laporan Penjualan')

@section('content')
<div class="space-y-8">
    <!-- Header & Date Filter Form -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-brex-ink tracking-brex-36">Dashboard Laporan Penjualan</h1>
            <p class="text-sm text-brex-pewter mt-1">Ringkasan performa transaksi dan pendapatan tiket travel shuttle.</p>
        </div>

        <!-- Filter Tanggal -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="bg-brex-paper border border-brex-mist rounded-brex p-3 flex items-center gap-3 shadow-none">
            <div>
                <label class="block text-[10px] font-semibold text-brex-pewter uppercase tracking-wider">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', optional($startDate)->format('Y-m-d')) }}"
                    class="bg-transparent border-0 p-0 text-xs font-semibold text-brex-ink focus:ring-0">
            </div>
            <span class="text-brex-mist">-</span>
            <div>
                <label class="block text-[10px] font-semibold text-brex-pewter uppercase tracking-wider">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', optional($endDate)->format('Y-m-d')) }}"
                    class="bg-transparent border-0 p-0 text-xs font-semibold text-brex-ink focus:ring-0">
            </div>
            <button type="submit" class="px-3 py-2 bg-brex-ember text-white text-xs font-medium rounded-brex hover:bg-[#e04f00] transition-colors shadow-none">
                Filter
            </button>
            @if(request('start_date') || request('end_date'))
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-brex-pewter hover:text-brex-ink px-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Transaksi -->
        <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-brex-pewter uppercase tracking-wider">Total Transaksi Berhasil</p>
                <h2 class="text-3xl md:text-4xl font-semibold text-brex-ink tracking-brex-36 mt-2">
                    {{ number_format($totalTransactions, 0, ',', '.') }}
                </h2>
                <p class="text-xs text-brex-pewter mt-2">Transaksi berstatus pembayaran sukses</p>
            </div>
            <div class="w-12 h-12 bg-brex-fog rounded-brex border border-brex-mist flex items-center justify-center text-brex-ember">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-brex-pewter uppercase tracking-wider">Total Pendapatan</p>
                <h2 class="text-3xl md:text-4xl font-semibold text-brex-ink tracking-brex-36 mt-2">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h2>
                <p class="text-xs text-brex-pewter mt-2">Akumulasi pendapatan bersih tiket</p>
            </div>
            <div class="w-12 h-12 bg-brex-fog rounded-brex border border-brex-mist flex items-center justify-center text-brex-ember">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Chart Penjualan -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 shadow-none space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-brex-ink tracking-brex-24">Grafik Pendapatan Harian</h3>
            <span class="text-xs text-brex-pewter">Warna Aksen Brex Ember (#ff5900)</span>
        </div>
        <div class="h-64 relative">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Breakdown Pendapatan Per Rute -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden space-y-4">
        <div class="p-6 border-b border-brex-mist pb-4">
            <h3 class="text-base font-semibold text-brex-ink tracking-brex-24">Breakdown Pendapatan Per Rute</h3>
            <p class="text-xs text-brex-pewter mt-1">Perincian tiket terjual dan total omset untuk setiap rute perjalanan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Rute Perjalanan</th>
                        <th class="py-3.5 px-6">Harga Dasar Tiket</th>
                        <th class="py-3.5 px-6">Tiket Terjual</th>
                        <th class="py-3.5 px-6 text-right">Total Pendapatan Rute</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    @forelse ($routeBreakdown as $rb)
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-brex-ink">
                                {{ $rb['kota_asal'] }} &rarr; {{ $rb['kota_tujuan'] }}
                            </td>
                            <td class="py-4 px-6 text-brex-ink font-medium">
                                Rp {{ number_format($rb['harga'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 font-medium text-brex-ink">
                                <span class="px-2.5 py-1 text-xs font-semibold bg-brex-fog text-brex-ink border border-brex-mist rounded-brex-chip">
                                    {{ $rb['total_tickets'] }} Tiket
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right font-semibold text-brex-ink">
                                Rp {{ number_format($rb['total_revenue'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-brex-pewter">
                                Belum ada data transaksi rute.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const values = {!! json_encode($chartValues) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: values,
                    borderColor: '#ff5900', // STRICT BREX EMBER
                    backgroundColor: 'rgba(255, 89, 0, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#ff5900',
                    pointBorderColor: '#ffffff',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f3f3f7' },
                        ticks: { color: '#6f737b', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: '#b9bbc6', borderDash: [2, 4] },
                        ticks: {
                            color: '#6f737b',
                            font: { family: 'Inter', size: 11 },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value/1000) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
