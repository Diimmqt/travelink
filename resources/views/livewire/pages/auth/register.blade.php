<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $nama = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('schedules.search', absolute: false), navigate: true);
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
                Bergabunglah Bersama<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-amber-400 to-brand-amber-300">Ratusan Penumpang Pintar.</span>
            </h1>
            <p class="text-brand-navy-300 text-lg leading-relaxed">
                Nikmati kemudahan memesan travel, memilih kursi favorit Anda sendiri secara real-time, dan boarding lebih praktis tanpa antre di agen.
            </p>

            <!-- Perks Info -->
            <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="flex items-center gap-2.5 text-sm text-brand-navy-200">
                    <div class="w-5 h-5 rounded-full bg-brand-amber-500/20 text-brand-amber-400 flex items-center justify-center">✓</div>
                    <span>Pilihan Kursi Bebas</span>
                </div>
                <div class="flex items-center gap-2.5 text-sm text-brand-navy-200">
                    <div class="w-5 h-5 rounded-full bg-brand-amber-500/20 text-brand-amber-400 flex items-center justify-center">✓</div>
                    <span>Boarding via QR-Code</span>
                </div>
                <div class="flex items-center gap-2.5 text-sm text-brand-navy-200">
                    <div class="w-5 h-5 rounded-full bg-brand-amber-500/20 text-brand-amber-400 flex items-center justify-center">✓</div>
                    <span>Titik Naik/Turun Fleksibel</span>
                </div>
                <div class="flex items-center gap-2.5 text-sm text-brand-navy-200">
                    <div class="w-5 h-5 rounded-full bg-brand-amber-500/20 text-brand-amber-400 flex items-center justify-center">✓</div>
                    <span>Pembayaran Snap Midtrans</span>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-sm text-brand-navy-400">
            &copy; {{ date('Y') }} Travelink. All rights reserved.
        </div>
    </div>

    <!-- Right Column: Minimalist Clean Register Form -->
    <div class="lg:col-span-5 flex flex-col justify-center px-8 sm:px-16 py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <div class="flex items-center gap-2 lg:hidden mb-8">
                <x-application-logo class="w-auto h-8" />
            </div>

            <h2 class="font-heading text-3xl font-bold text-brand-navy-950 mb-2">Buat Akun Baru</h2>
            <p class="text-brand-navy-500 text-sm mb-8">Daftar sekarang untuk memulai perjalanan travel yang lebih mudah.</p>

            <form wire:submit="register" class="space-y-4">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Nama Lengkap</label>
                    <input wire:model="nama" id="nama" type="text" name="nama" required autofocus autocomplete="name"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="Nama Lengkap Anda" />
                    <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Email</label>
                    <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="nama@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Password</label>
                    <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="Minimal 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Konfirmasi Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="Ulangi password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-brand-navy-950 hover:bg-brand-navy-900 text-white font-bold rounded-xl shadow-lg shadow-brand-navy-950/10 hover:shadow-xl transition duration-150 mt-2">
                    Daftar Akun
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-brand-navy-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-bold text-brand-amber-600 hover:text-brand-amber-700 transition" wire:navigate>
                    Masuk Di Sini
                </a>
            </div>
        </div>
    </div>
</div>
