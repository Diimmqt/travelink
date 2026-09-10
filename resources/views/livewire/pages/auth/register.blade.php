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

<div class="min-h-screen bg-brex-fog flex items-center justify-center px-4 py-12 font-sans">
    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="flex items-center justify-center gap-3 mb-8">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-brex-chip bg-brex-ember flex items-center justify-center text-white font-bold text-lg">T</div>
                <span class="text-xl font-semibold text-brex-ink tracking-brex-24">Travelink</span>
            </a>
        </div>

        <!-- Card -->
        <div class="bg-brex-paper border border-brex-mist rounded-brex p-8 shadow-none">
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24 mb-1">Buat Akun Baru</h1>
            <p class="text-sm text-brex-pewter mb-7">Daftar sekarang untuk mulai memesan tiket travel shuttle.</p>

            <form wire:submit="register" class="space-y-5">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input wire:model="nama" id="nama" type="text" name="nama" required autofocus autocomplete="name"
                        placeholder="Nama Lengkap Anda"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    <x-input-error :messages="$errors->get('nama')" class="mt-1 text-xs text-rose-600" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Email</label>
                    <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                        placeholder="nama@email.com"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Password</label>
                    <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-600" />
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3 px-4 bg-brex-ember hover:bg-[#e04f00] text-white font-semibold text-sm rounded-brex transition duration-150 shadow-none mt-2">
                    Daftar Sekarang
                </button>
            </form>
        </div>

        <!-- Link Login -->
        <p class="mt-5 text-center text-sm text-brex-pewter">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-brex-ember hover:text-[#e04f00] transition" wire:navigate>
                Masuk Di Sini
            </a>
        </p>

        <p class="mt-4 text-center text-xs text-brex-steel">&copy; {{ date('Y') }} Travelink. All rights reserved.</p>
    </div>
</div>
