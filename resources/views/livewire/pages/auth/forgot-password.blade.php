<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-white">
    <!-- Left Column: Travel Visual Panel (Hidden on mobile) -->
    <div class="hidden lg:flex lg:col-span-7 bg-brand-navy-950 text-white relative overflow-hidden flex-col justify-between p-16">
        <!-- Abstract glowing gradient orbs -->
        <div class="absolute inset-0 bg-gradient-to-tr from-brand-navy-950 via-brand-navy-900 to-indigo-950"></div>
        <div class="absolute top-1/4 -left-20 w-80 h-80 bg-brand-amber-500 rounded-full mix-blend-screen filter blur-[120px] opacity-10"></div>
        <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-indigo-500 rounded-full mix-blend-screen filter blur-[120px] opacity-15"></div>

        <div class="relative z-10 flex items-center gap-3">
            <x-application-logo class="w-auto h-10 rounded-xl shadow-md" />
        </div>

        <div class="relative z-10 max-w-xl my-auto">
            <h1 class="font-heading text-5xl font-extrabold leading-tight tracking-tight text-white mb-6">
                Aman, Cepat &<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-amber-400 to-brand-amber-300">Kembali Menikmati Perjalanan.</span>
            </h1>
            <p class="text-brand-navy-300 text-lg leading-relaxed">
                Kami siap membantu Anda memulihkan akses ke akun Travelink. Atur ulang kata sandi dengan aman dan lanjutkan perjalanan Anda.
            </p>

            <!-- Mini Security Illustration -->
            <div class="mt-12 flex items-center gap-4 text-sm text-brand-navy-300 bg-brand-navy-900/50 backdrop-blur border border-brand-navy-800 p-4 rounded-2xl w-fit">
                <div class="w-8 h-8 rounded-xl bg-brand-amber-500/20 text-brand-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white">Pemulihan Akun Terenkripsi</p>
                    <p class="text-xs text-brand-navy-400">Tautan reset hanya dikirim ke email terdaftar Anda</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-sm text-brand-navy-400">
            &copy; {{ date('Y') }} Travelink. All rights reserved.
        </div>
    </div>

    <!-- Right Column: Minimalist Clean Forgot Password Form -->
    <div class="lg:col-span-5 flex flex-col justify-center px-8 sm:px-16 py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <div class="flex items-center gap-2 lg:hidden mb-8">
                <x-application-logo class="w-auto h-8" />
            </div>

            <h2 class="font-heading text-3xl font-bold text-brand-navy-950 mb-2">Lupa Password?</h2>
            <p class="text-brand-navy-500 text-sm mb-8 leading-relaxed">
                Tidak masalah. Masukkan alamat email akun Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form wire:submit="sendPasswordResetLink" class="space-y-5">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Email</label>
                    <input wire:model="email" id="email" type="email" name="email" required autofocus autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="nama@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 px-4 bg-brand-navy-950 hover:bg-brand-navy-900 active:bg-brand-navy-800 text-white font-bold rounded-xl shadow-lg shadow-brand-navy-950/10 hover:shadow-xl transition duration-150 flex items-center justify-center gap-2 group cursor-pointer">
                    <span wire:loading.remove wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-amber-400 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Kirim Tautan Reset Password</span>
                    </span>
                    <span wire:loading wire:target="sendPasswordResetLink" class="flex items-center gap-2 text-white">
                        <svg class="animate-spin h-5 w-5 text-brand-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengirim Tautan...</span>
                    </span>
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-brand-navy-500">
                Ingat kata sandi Anda? 
                <a href="{{ route('login') }}" class="font-bold text-brand-amber-600 hover:text-brand-amber-700 transition" wire:navigate>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
