{{-- Komponen untuk menampilkan pesan status autentikasi dari session (misal link reset password terkirim) --}}
@props(['status'])

{{-- Render kotak pesan hanya jika variabel $status memiliki nilai --}}
@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
