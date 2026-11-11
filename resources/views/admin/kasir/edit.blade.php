{{-- Menggunakan layout utama admin --}}
@extends('layouts.admin')

{{-- Menentukan judul halaman edit akun kasir --}}
@section('title', 'Edit Akun Kasir')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Edit Akun Kasir</h1>
            {{-- Menampilkan nama akun kasir yang sedang diubah --}}
            <p class="text-sm text-brex-pewter mt-1">Perbarui data atau reset password staf loket: <strong class="text-brex-ink">{{ $kasir->nama }}</strong></p>
        </div>
        <a href="{{ route('admin.kasir.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 border border-brex-mist text-sm font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex p-6 sm:p-8 shadow-none">
        {{-- Mengirim data pembaruan ke route admin.kasir.update dengan metode PUT --}}
        <form action="{{ route('admin.kasir.update', $kasir->id) }}" method="POST" class="space-y-5">
            @csrf
            {{-- Spoofing HTTP method menjadi PUT sesuai standar RESTful update di Laravel --}}
            @method('PUT')

            <!-- Nama Kasir -->
            <div>
                <label for="name" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                    Nama Lengkap Staf Kasir <span class="text-rose-500">*</span>
                </label>
                {{-- Menggunakan old() dengan nilai fallback dari data $kasir->nama yang sudah tersimpan --}}
                <input type="text" name="name" id="name" value="{{ old('name', $kasir->nama) }}" required autofocus
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Kasir -->
            <div>
                <label for="email" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $kasir->email) }}" required
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor HP / WhatsApp -->
            <div>
                <label for="phone" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                    Nomor WhatsApp / HP
                </label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $kasir->phone) }}"
                    class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bagian opsional untuk mengganti password kasir; boleh dikosongkan jika tidak diubah --}}
            <div class="pt-4 border-t border-brex-mist/50">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold uppercase text-brex-pewter tracking-wider">Reset Password Kasir</p>
                    <span class="text-xs text-brex-pewter italic">Kosongkan jika tidak ingin mengganti password</span>
                </div>

                <!-- Password Baru -->
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                            Password Baru
                        </label>
                        <input type="password" name="password" id="password" autocomplete="new-password"
                            placeholder="Biarkan kosong jika tetap memakai sandi lama"
                            class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">
                            Konfirmasi Ulang Password Baru
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                            placeholder="Ulangi password baru jika diubah"
                            class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <a href="{{ route('admin.kasir.index') }}" class="px-4 py-2.5 border border-brex-mist rounded-brex text-sm font-medium text-brex-graphite hover:bg-brex-fog transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-brex-ember hover:bg-[#e04f00] text-white text-sm font-semibold rounded-brex transition duration-150 shadow-none">
                    Perbarui Akun Kasir
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

