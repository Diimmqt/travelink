{{-- Komponen input teks reusable; mendukung prop 'disabled' dan penggabungan atribut/kelas Tailwind --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
