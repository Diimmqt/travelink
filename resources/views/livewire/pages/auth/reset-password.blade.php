<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
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
                Password Baru,<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-amber-400 to-brand-amber-300">Siap Melanjutkan Perjalanan.</span>
            </h1>
            <p class="text-brand-navy-300 text-lg leading-relaxed">
                Buat kata sandi baru yang kuat untuk menjaga keamanan akun Travelink Anda.
            </p>
        </div>

        <div class="relative z-10 text-sm text-brand-navy-400">
            &copy; {{ date('Y') }} Travelink. All rights reserved.
        </div>
    </div>

    <!-- Right Column: Reset Password Form -->
    <div class="lg:col-span-5 flex flex-col justify-center px-8 sm:px-16 py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <div class="flex items-center gap-2 lg:hidden mb-8">
                <x-application-logo class="w-auto h-8" />
            </div>

            <h2 class="font-heading text-3xl font-bold text-brand-navy-950 mb-2">Reset Password</h2>
            <p class="text-brand-navy-500 text-sm mb-8">Silakan buat kata sandi baru untuk akun Anda.</p>

            <form wire:submit="resetPassword" class="space-y-5">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Email</label>
                    <input wire:model="email" id="email" type="email" name="email" required autofocus autocomplete="username"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="nama@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Password Baru</label>
                    <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-brand-navy-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-xl border border-brand-stone-300 text-brand-navy-900 bg-white placeholder-brand-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-amber-500/20 focus:border-brand-amber-500 transition duration-200"
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-brand-navy-950 hover:bg-brand-navy-900 text-white font-bold rounded-xl shadow-lg shadow-brand-navy-950/10 hover:shadow-xl transition duration-150">
                    Perbarui Password
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-brand-navy-500">
                <a href="{{ route('login') }}" class="font-bold text-brand-amber-600 hover:text-brand-amber-700 transition" wire:navigate>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
