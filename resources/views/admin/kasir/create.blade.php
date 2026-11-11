{{-- Menggunakan layout admin utama --}}
@extends('layouts.admin')

{{-- Menentukan judul halaman tambah akun kasir --}}
@section('title', 'Tambah Akun Kasir Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Tambah Akun Kasir</h1>
            <p class="text-sm text-brex-pewter mt-1">Buat akun akses loket baru untuk staf operasional travel.</p>
        </div>
        {{-- Tautan kembali ke halaman daftar akun kasir --}}
        <a href="{{ route('admin.kasir.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 border border-brex-mist text-sm font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Card Form -->
    {{-- Kartu berisi formulir pembuatan akun kasir baru --}}
    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 sm:p-8 shadow-none">
        {{-- Form dikirim dengan metode POST ke route admin.kasir.store --}}
        <form action="{{ route('admin.kasir.store') }}" method="POST" class="space-y-5">
            {{-- Token keamanan CSRF untuk melindungi pengiriman form --}}
            @csrf

            <!-- Nama Kasir -->
            <div>
                <label for="name" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                    Nama Lengkap Staf Kasir <span class="text-rose-500">*</span>
                </label>
                {{-- Fungsi old('name') menjaga input sebelumnya tetap terisi jika validasi gagal --}}
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    placeholder="Contoh: Budi Santoso"
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                {{-- Menampilkan pesan error validasi untuk field name --}}
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Kasir -->
            <div>
                <label for="email" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                    Alamat Email (Digunakan untuk Login) <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="kasir@travelink.id"
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor HP / WhatsApp -->
            <div>
                <label for="phone" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                    Nomor WhatsApp / HP (Opsional)
                </label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    placeholder="0812xxxxxxxx"
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bagian input password dan konfirmasi password akun kasir --}}
            <div class="pt-4 border-t border-brex-mist/50">
                <p class="text-xs font-semibold uppercase text-brex-pewter tracking-wider mb-4">Pengaturan Sandi Kasir</p>

                <!-- Password -->
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                            Konfirmasi Ulang Password <span class="text-rose-500">*</span>
                        </label>
                        {{-- Field password_confirmation dicocokkan oleh aturan validasi 'confirmed' di Laravel --}}
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                            placeholder="Ketik ulang password"
                            class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    </div>
                </div>
            </div>

            <!-- Role Notification -->
            {{-- Kotak informasi bahwa akun yang dibuat otomatis memiliki peran Kasir --}}
            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-brex text-xs text-blue-800 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Akun ini otomatis terdaftar dengan hak akses <strong>Kasir Loket</strong> (Verifikasi tiket, cetak manifest, dan booking offline).</span>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <a href="{{ route('admin.kasir.index') }}" class="px-4 py-2.5 border border-brex-mist rounded-brex text-sm font-medium text-brex-graphite hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-brex-ember hover:bg-[#e04f00] text-white text-sm font-semibold rounded-brex transition duration-150 shadow-none">
                    Simpan Akun Kasir
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

