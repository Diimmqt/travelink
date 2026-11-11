<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// Komponen Volt halaman konfirmasi password menggunakan layout tamu (layouts.guest)
new #[Layout('layouts.guest')] class extends Component
{
    // Properti untuk menampung input password dari pengguna
    public string $password = '';

    /**
     * Confirm the current user's password.
     *
     * Memvalidasi password pengguna saat ini sebelum mengizinkan akses ke halaman sensitif.
     */
    public function confirmPassword(): void
    {
        // Validasi bahwa field password wajib diisi dan bertipe string
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        // Cocokkan email pengguna yang sedang login dengan password yang dimasukkan
        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            // Jika tidak cocok, lempar error validasi pada field password
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Catat waktu konfirmasi password ke dalam session agar tidak diminta ulang sementara waktu
        session(['auth.password_confirmed_at' => time()]);

        // Arahkan pengguna ke halaman tujuan sebelumnya (atau beranda jika tidak ada)
        $this->redirectIntended(default: route('home', absolute: false), navigate: true);
    }
}; ?>

{{-- Tampilan halaman konfirmasi password --}}
<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    {{-- Formulir yang memanggil method confirmPassword saat disubmit --}}
    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />

            {{-- Menampilkan pesan error validasi password jika ada --}}
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</div>
