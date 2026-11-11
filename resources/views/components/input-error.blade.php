{{-- Komponen untuk menampilkan daftar pesan error validasi di bawah field input --}}
@props(['messages'])

{{-- Tampilkan daftar <ul> hanya jika terdapat pesan error --}}
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
