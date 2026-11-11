{{-- Komponen label formulir: menampilkan teks dari prop $value, atau isi $slot jika $value tidak diberikan --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
