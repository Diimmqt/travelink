<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// Komponen Volt untuk halaman login dengan layout tamu (layouts.guest)
new #[Layout('layouts.guest')] class extends Component
{
    // Form object Livewire yang menyimpan email, password, remember, dan aturan validasinya
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     *
     * Memvalidasi kredensial pengguna, melakukan autentikasi, meregenerasi session,
     * lalu mengarahkan pengguna ke dashboard sesuai role masing-masing.
     */
    public function login(): void
    {
        // Validasi input pada LoginForm
        $this->validate();

        // Jalankan proses autentikasi melalui LoginForm
        $this->form->authenticate();

        // Regenerasi ID session untuk mencegah serangan session fixation
        Session::regenerate();

        // Redirect berdasarkan role (full page redirect tanpa wire:navigate agar layout admin termuat sempurna)
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $this->redirect(route('admin.dashboard', absolute: false));
        } elseif ($user->isKasir()) {
            $this->redirect(route('kasir.dashboard', absolute: false));
        } else {
            // Pengguna biasa diarahkan ke halaman yang ingin diakses sebelumnya atau dashboard default
            $this->redirectIntended(default: route('dashboard', absolute: false));
        }
    }
}; ?>

{{-- Kontainer halaman login berada di tengah layar --}}
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
        {{-- Kartu utama formulir masuk --}}
        <div class="bg-brex-paper border border-brex-mist rounded-brex p-8 shadow-none">
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24 mb-1">Selamat Datang</h1>
            <p class="text-sm text-brex-pewter mb-7">Masuk ke akun Travelink Anda untuk memesan tiket.</p>

            <!-- Session Status -->
            {{-- Menampilkan status sukses dari session (misalnya setelah reset password) --}}
            <x-auth-session-status class="mb-5" :status="session('status')" />

            {{-- Menampilkan pesan error dari session jika autentikasi/login Google gagal --}}
            @if (session('error'))
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-brex flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Formulir login Livewire yang memanggil method login() saat disubmit --}}
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
                        {{-- Tampilkan link Lupa Password jika rute password.request tersedia --}}
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
                {{-- Checkbox agar sesi login tetap aktif lebih lama --}}
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

            <!-- Divider -->
            <div class="relative my-6" style="margin-top: 24px; margin-bottom: 24px;">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-brex-mist"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-brex-paper px-3 text-brex-pewter font-medium tracking-wider text-[11px]">Atau lanjutkan dengan</span>
                </div>
            </div>

            <!-- Tombol Login Google -->
            {{-- Tautan OAuth untuk masuk menggunakan akun Google --}}
            <div style="margin-top: 20px; margin-bottom: 8px;">
                <a href="{{ route('auth.google.redirect') }}"
                   class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-white hover:bg-gray-50 border border-brex-mist rounded-brex text-brex-ink text-sm font-semibold shadow-none transition duration-150"
                   style="display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>
            </div>
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
