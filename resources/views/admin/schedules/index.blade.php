@extends('layouts.admin')

@section('title', 'Data Jadwal Keberangkatan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Kelola Jadwal Keberangkatan</h1>
            <p class="text-sm text-brex-pewter mt-1">Atur jadwal keberangkatan shuttle, penetapan armada, dan ketersediaan kursi.</p>
        </div>
        <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Data</span>
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex p-4 shadow-none">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label for="route_id" class="block text-[11px] font-semibold text-brex-pewter uppercase tracking-wider mb-1">Filter Rute</label>
                <select name="route_id" id="route_id" class="bg-brex-paper border border-brex-mist rounded-brex px-3 py-2 text-sm text-brex-ink focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    <option value="">Semua Rute</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->kota_asal }} - {{ $r->kota_tujuan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-[11px] font-semibold text-brex-pewter uppercase tracking-wider mb-1">Filter Status</label>
                <select name="status" id="status" class="bg-brex-paper border border-brex-mist rounded-brex px-3 py-2 text-sm text-brex-ink focus:border-brex-ember focus:ring-1 focus:ring-brex-ember">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end pt-5">
                <button type="submit" class="px-4 py-2 bg-brex-fog border border-brex-mist text-brex-ink text-sm font-medium rounded-brex hover:bg-brex-mist/20 transition-colors">
                    Filter Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Rute</th>
                        <th class="py-3.5 px-6">Waktu Berangkat</th>
                        <th class="py-3.5 px-6">Armada</th>
                        <th class="py-3.5 px-6">Ketersediaan Kursi</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    @forelse ($schedules as $schedule)
                        @php
                            $availableSeatsCount = $schedule->seats->filter(fn($s) => $s->effective_status === 'available')->count();
                            $totalSeatsCount = $schedule->seats->count();
                        @endphp
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-brex-ink">
                                {{ $schedule->route ? $schedule->route->kota_asal . ' - ' . $schedule->route->kota_tujuan : '-' }}
                            </td>
                            <td class="py-4 px-6 font-medium text-brex-ink">
                                <div>{{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-brex-pewter font-semibold">{{ \Carbon\Carbon::parse($schedule->waktu_berangkat)->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-4 px-6 text-brex-ink">
                                <span class="font-medium">{{ $schedule->vehicle->jenis ?? '-' }}</span>
                                <span class="text-xs text-brex-pewter block uppercase">{{ $schedule->vehicle->plat_nomor ?? '' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 text-xs font-semibold bg-brex-fog text-brex-ink border border-brex-mist rounded-brex-chip">
                                    {{ $availableSeatsCount }} / {{ $totalSeatsCount }} Kursi Tersedia
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($schedule->status === 'scheduled')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded-brex-chip">Scheduled</span>
                                @elseif($schedule->status === 'ongoing')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 rounded-brex-chip">Ongoing</span>
                                @elseif($schedule->status === 'completed')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-brex-chip">Completed</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 rounded-brex-chip">Cancelled</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="inline-flex items-center px-3 py-1.5 border border-brex-mist text-xs font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jadwal ini beserta kursi terkait?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 border border-rose-200 text-xs font-medium text-rose-600 rounded-brex hover:bg-rose-50 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-brex-pewter">
                                Belum ada jadwal keberangkatan. Klik "Tambah Data" untuk buat jadwal baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($schedules->hasPages())
            <div class="p-4 border-t border-brex-mist bg-brex-fog/30">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
