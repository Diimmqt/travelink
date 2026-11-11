<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// Komponen Volt untuk halaman verifikasi email dengan layout tamu (layouts.guest)
new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     *
     * Mengirim ulang email tautan verifikasi jika email pengguna belum terverifikasi.
     */
    public function sendVerification(): void
    {
        // Jika email sudah terverifikasi sebelumnya, langsung arahkan ke beranda
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);

            return;
        }

        // Kirim notifikasi email verifikasi ke alamat email pengguna saat ini
        Auth::user()->sendEmailVerificationNotification();

        // Simpan status di session flash untuk memunculkan pesan konfirmasi pengiriman ulang
        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     *
     * Mengeluarkan pengguna dari sesi login jika ingin mengganti akun.
     *
     * @param  Logout  $logout  Instance action class untuk logout.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

{{-- Tampilan halaman pemberitahuan verifikasi email --}}
<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    {{-- Menampilkan pesan hijau jika tautan verifikasi baru saja dikirim ulang --}}
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        {{-- Tombol kirim ulang email verifikasi --}}
        <x-primary-button wire:click="sendVerification">
            {{ __('Resend Verification Email') }}
        </x-primary-button>

        {{-- Tombol logout jika pengguna ingin keluar dari akun saat ini --}}
        <button wire:click="logout" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Log Out') }}
        </button>
    </div>
</div>
