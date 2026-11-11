<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

// Komponen Volt untuk formulir pembaruan password pada halaman profil
new class extends Component
{
    // Properti input password lama, password baru, dan konfirmasi password baru
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     *
     * Memvalidasi password saat ini dan password baru, meng-hash password baru,
     * menyimpannya ke database, mereset form, lalu mengirim event 'password-updated'.
     */
    public function updatePassword(): void
    {
        try {
            // Validasi password lama harus cocok dengan user saat ini, dan password baru harus terkonfirmasi
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            // Jika validasi gagal, kosongkan semua input password lalu lempar kembali exception agar error tampil
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        // Perbarui kolom password di database dengan nilai yang sudah di-hash
        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Kosongkan kembali seluruh input form setelah berhasil
        $this->reset('current_password', 'password', 'password_confirmation');

        // Kirim event Livewire untuk memicu pesan konfirmasi "Tersimpan." di komponen x-action-message
        $this->dispatch('password-updated');
    }
}; ?>

{{-- Seksi formulir pembaruan password --}}
<section>
    <header>
        <h2 class="text-base font-medium" style="color: var(--ds-snow); letter-spacing: -0.02em;">
            {{ __('Perbarui Password') }}
        </h2>

        <p class="mt-1 text-xs" style="color: var(--ds-fog);">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    {{-- Form memanggil method updatePassword() saat disubmit --}}
    <form wire:submit="updatePassword" class="mt-6 space-y-5">
        {{-- Input password saat ini --}}
        <div>
            <label for="update_password_current_password" class="block text-xs font-medium uppercase tracking-widest mb-1.5" style="color: var(--ds-steel); font-size: 0.65rem; letter-spacing: 0.1em;">
                {{ __('Password Saat Ini') }}
            </label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="ds-input" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2 text-xs text-red-400" />
        </div>

        {{-- Input password baru --}}
        <div>
            <label for="update_password_password" class="block text-xs font-medium uppercase tracking-widest mb-1.5" style="color: var(--ds-steel); font-size: 0.65rem; letter-spacing: 0.1em;">
                {{ __('Password Baru') }}
            </label>
            <input wire:model="password" id="update_password_password" name="password" type="password" class="ds-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400" />
        </div>

        {{-- Input konfirmasi password baru --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-medium uppercase tracking-widest mb-1.5" style="color: var(--ds-steel); font-size: 0.65rem; letter-spacing: 0.1em;">
                {{ __('Konfirmasi Password') }}
            </label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="ds-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-400" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="ds-btn-primary text-xs px-5 py-2.5">
                {{ __('Simpan Password') }}
            </button>

            {{-- Pesan sementara yang muncul saat event 'password-updated' diterima --}}
            <x-action-message class="text-xs text-green-400" on="password-updated">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>
