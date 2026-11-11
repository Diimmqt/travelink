<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

// Komponen Volt untuk formulir pembaruan nama dan email pada halaman profil
new class extends Component
{
    // Properti yang menyimpan nilai nama dan email pengguna
    public string $nama = '';
    public string $email = '';

    /**
     * Mount the component.
     *
     * Mengisi nilai awal form dengan nama dan email pengguna yang sedang login.
     */
    public function mount(): void
    {
        $this->nama = Auth::user()->nama;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     *
     * Memvalidasi nama & email, mereset status verifikasi email jika alamat email berubah,
     * menyimpan perubahan ke database, lalu mengirim event 'profile-updated'.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        // Validasi nama wajib diisi dan email harus unik kecuali milik user ini sendiri
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        // Jika email diubah, hapus timestamp verifikasi agar pengguna perlu memverifikasi email baru
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Kirim event agar navbar dan pesan "Tersimpan." ikut diperbarui secara reaktif
        $this->dispatch('profile-updated', name: $user->nama, nama: $user->nama);
    }

    /**
     * Send an email verification notification to the current user.
     *
     * Mengirim ulang email verifikasi jika alamat email pengguna belum terverifikasi.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        // Jika email ternyata sudah terverifikasi, arahkan ke beranda
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

{{-- Seksi formulir informasi profil pengguna --}}
<section>
    <header>
        <h2 class="text-base font-medium" style="color: var(--ds-snow); letter-spacing: -0.02em;">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-xs" style="color: var(--ds-fog);">
            {{ __('Perbarui data akun dan alamat email terdaftar Anda.') }}
        </p>
    </header>

    {{-- Form memanggil method updateProfileInformation() saat disubmit --}}
    <form wire:submit="updateProfileInformation" class="mt-6 space-y-5">
        {{-- Input nama lengkap --}}
        <div>
            <label for="nama" class="block text-xs font-medium uppercase tracking-widest mb-1.5" style="color: var(--ds-steel); font-size: 0.65rem; letter-spacing: 0.1em;">
                {{ __('Nama Lengkap') }}
            </label>
            <input wire:model="nama" id="nama" name="nama" type="text" class="ds-input" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-xs text-red-400" :messages="$errors->get('nama')" />
        </div>

        {{-- Input alamat email beserta status verifikasi email --}}
        <div>
            <label for="email" class="block text-xs font-medium uppercase tracking-widest mb-1.5" style="color: var(--ds-steel); font-size: 0.65rem; letter-spacing: 0.1em;">
                {{ __('Alamat Email') }}
            </label>
            <input wire:model="email" id="email" name="email" type="email" class="ds-input" required autocomplete="username" />
            <x-input-error class="mt-2 text-xs text-red-400" :messages="$errors->get('email')" />

            {{-- Jika model User mengimplementasikan MustVerifyEmail dan email belum terverifikasi, tampilkan peringatan & tombol kirim ulang --}}
            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-xs mt-2 text-yellow-400">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-xs text-blue-400 hover:text-blue-300 ml-1">
                            {{ __('Kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-xs text-green-400">
                            {{ __('Tautan verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="ds-btn-primary text-xs px-5 py-2.5">
                {{ __('Simpan Perubahan') }}
            </button>

            {{-- Pesan konfirmasi sementara saat event 'profile-updated' diterima --}}
            <x-action-message class="text-xs text-green-400" on="profile-updated">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>
