<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('home', absolute: false), navigate: true);
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
                Perjalanan Nyaman,<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-amber-400 to-brand-amber-300">Tanpa Batas Terminal.</span>
            </h1>
            <p class="text-brand-navy-300 text-lg leading-relaxed">
                Travelink menghubungkan Anda langsung dari titik jemput pilihan hingga destinasi tujuan dengan armada Hiace & Minibus premium kami.
            </p>

            <!-- Mini Route Illustration -->
            <div class="mt-12 flex items-center gap-4 text-sm text-brand-navy-300 bg-brand-navy-900/50 backdrop-blur border border-brand-navy-800 p-4 rounded-2xl w-fit">
                <span class="font-bold text-brand-amber-400">Bandung</span>
                <svg class="w-5 h-5 text-brand-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
                <span class="font-bold text-brand-amber-400">Jakarta</span>
                <span class="text-brand-navy-500">|</span>
                <span>Pilih titik jemput terdekat Anda!</span>
            </div>
        </div>

        <div class="relative z-10 text-sm text-brand-navy-400">
            &copy; {{ date('Y') }} Travelink. All rights reserved.
        </div>
    </div>

    <!-- Right Column: Minimalist Clean Login Form -->
    <div class="lg:col-span-5 flex flex-col justify-center px-8 sm:px-16 py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <div class="flex items-center gap-2 lg:hidden mb-8">
                <x-application-logo class="w-auto h-8" />
            </div>

            <h2 class="font-heading text-3xl font-bold text-brand-navy-950 mb-2">Selamat Datang</h2>
            <p class="text-brand-navy-500 text-sm mb-8">Silakan masuk ke akun Travelink Anda untuk memesan tiket.</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form wire:submit="login" class="space-y-5">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Email</label>
                    <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="nama@email.com" />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-brand-navy-700">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-brand-amber-600 hover:text-brand-amber-700 transition" href="{{ route('password.request') }}" wire:navigate>
                                Lupa Password?
                            </a>
                        @endif
                    </div>
                    <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center cursor-pointer select-none">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="w-4.5 h-4.5 rounded border-brand-stone-300 text-brand-amber-500 focus:ring-brand-amber-500 focus:ring-offset-0 transition duration-150" name="remember">
                        <span class="ms-2 text-sm text-brand-navy-600 font-medium">Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-brand-navy-950 hover:bg-brand-navy-900 text-white font-bold rounded-xl shadow-lg shadow-brand-navy-950/10 hover:shadow-xl transition duration-150">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-brand-navy-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-brand-amber-600 hover:text-brand-amber-700 transition" wire:navigate>
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
