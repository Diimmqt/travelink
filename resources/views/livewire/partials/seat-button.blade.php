@php
    $effectiveStatus = $seat->effective_status;
    $isSelected = in_array($seat->id, $selectedSeats ?? []);
    $isAvailable = $effectiveStatus === 'available';
    $isLocked = $effectiveStatus === 'locked' && !$isSelected;
    $isBooked = $effectiveStatus === 'booked';
@endphp

@if($isSelected)
    <button type="button" wire:click="selectSeat({{ $seat->id }})"
        class="w-full h-11 sm:h-12 text-xs font-bold rounded-brex transition-all duration-150 flex items-center justify-center gap-1 focus:outline-none cursor-pointer bg-brex-ember text-white border-2 border-brex-ember shadow-sm scale-105"
        title="Kursi {{ $seat->nomor_kursi }} (Dipilih)">
        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ $seat->nomor_kursi }}</span>
    </button>
@elseif($isAvailable)
    <button type="button" wire:click="selectSeat({{ $seat->id }})"
        class="w-full h-11 sm:h-12 text-sm font-semibold rounded-brex transition-all duration-150 flex items-center justify-center gap-1.5 focus:outline-none cursor-pointer bg-white border border-brex-mist text-brex-ink hover:border-brex-ember hover:text-brex-ember hover:bg-brex-fog shadow-none"
        title="Kursi {{ $seat->nomor_kursi }} Tersedia">
        <span class="text-xs font-semibold tracking-wider">{{ $seat->nomor_kursi }}</span>
    </button>
@elseif($isLocked)
    <div class="w-full h-11 sm:h-12 rounded-brex flex items-center justify-center gap-1 cursor-not-allowed select-none bg-brex-fog border border-brex-mist text-brex-steel"
        title="Sedang Dipilih Penumpang Lain / Proses Pembayaran">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <span class="text-xs font-medium">{{ $seat->nomor_kursi }}</span>
    </div>
@elseif($isBooked)
    <div class="w-full h-11 sm:h-12 rounded-brex flex items-center justify-center gap-1 cursor-not-allowed select-none bg-red-50 border border-red-200 text-red-600"
        title="Kursi Terisi / Lunas">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span class="text-xs font-medium">{{ $seat->nomor_kursi }}</span>
    </div>
@endif
