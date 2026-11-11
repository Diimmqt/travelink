{{-- Komponen dropdown interaktif dengan konfigurasi perataan (align), lebar (width), dan class konten --}}
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
// Menentukan kelas CSS perataan popup dropdown berdasarkan nilai prop $align
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

// Mengonversi nilai prop $width menjadi kelas lebar Tailwind (default w-48)
$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

{{-- State Alpine.js 'open' mengontrol buka/tutup menu; menutup otomatis saat klik di luar area --}}
<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    {{-- Area pemicu (trigger) untuk membuka atau menutup dropdown --}}
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    {{-- Kontainer isi menu dropdown dengan animasi transisi muncul dan menghilang --}}
    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
