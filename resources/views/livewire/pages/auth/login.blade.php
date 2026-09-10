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

        // Redirect berdasarkan role
        $user = auth()->user();
        if ($user->role === 'admin') {
            $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);
        }
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
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24 mb-1">Selamat Datang</h1>
            <p class="text-sm text-brex-pewter mb-7">Masuk ke akun Travelink Anda untuk memesan tiket.</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-5" :status="session('status')" />

            <form wire:submit="login" class="space-y-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider mb-2">Email</label>
                    <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                        placeholder="nama@email.com"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    <x-input-error :messages="$errors->get('form.email')" class="mt-1 text-xs text-rose-600" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-brex-ink uppercase tracking-wider">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-medium text-brex-ember hover:text-[#e04f00] transition" href="{{ route('password.request') }}" wire:navigate>
                                Lupa Password?
                            </a>
                        @endif
                    </div>
                    <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full bg-brex-paper border border-brex-mist rounded-brex px-3.5 py-2.5 text-brex-ink text-sm focus:outline-none focus:border-brex-ember focus:ring-1 focus:ring-brex-ember placeholder-brex-steel transition">
                    <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-xs text-rose-600" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-brex-mist text-brex-ember focus:ring-brex-ember focus:ring-offset-0">
                    <label for="remember" class="text-sm text-brex-graphite cursor-pointer">Ingat Saya</label>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3 px-4 bg-brex-ember hover:bg-[#e04f00] text-white font-semibold text-sm rounded-brex transition duration-150 shadow-none">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <!-- Link Register -->
        <p class="mt-5 text-center text-sm text-brex-pewter">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-brex-ember hover:text-[#e04f00] transition" wire:navigate>
                Daftar Sekarang
            </a>
        </p>

        <p class="mt-4 text-center text-xs text-brex-steel">&copy; {{ date('Y') }} Travelink. All rights reserved.</p>
    </div>
</div>
