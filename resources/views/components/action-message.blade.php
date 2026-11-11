{{-- Komponen Blade untuk menampilkan pesan notifikasi sementara saat event Livewire diterima --}}
{{-- Properti 'on': nama event Livewire yang dipantau untuk memunculkan pesan --}}
@props(['on'])

{{-- Menggunakan Alpine.js untuk menampilkan pesan selama 2 detik lalu menyembunyikannya kembali --}}
<div x-data="{ shown: false, timeout: null }"
     x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
     x-show.transition.out.opacity.duration.1500ms="shown"
     x-transition:leave.opacity.duration.1500ms
     style="display: none;"
    {{ $attributes->merge(['class' => 'text-sm text-gray-600']) }}>
    {{-- Tampilkan isi slot jika ada, atau teks default 'Saved.' jika slot kosong --}}
    {{ $slot->isEmpty() ? __('Saved.') : $slot }}
</div>
