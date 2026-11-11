<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * Form object Livewire untuk menangani validasi input login, autentikasi kredensial,
 * serta pembatasan percobaan login gagal (rate limiting) agar aman dari brute-force.
 */
class LoginForm extends Form
{
    // Alamat email pengguna (wajib diisi dan berformat email valid)
    #[Validate('required|string|email')]
    public string $email = '';

    // Kata sandi pengguna (wajib diisi)
    #[Validate('required|string')]
    public string $password = '';

    // Opsi "Ingat saya" agar sesi login tetap aktif lebih lama
    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     * Mencoba memverifikasi kecocokan email dan password pengguna dengan perlindungan rate limit.
     *
     * @throws ValidationException  Jika kredensial salah atau batas percobaan login terlampaui
     * @return void
     */
    public function authenticate(): void
    {
        // Cek apakah IP & email ini sedang diblokir sementara karena terlalu sering gagal login
        $this->ensureIsNotRateLimited();

        // Coba login menggunakan guard Laravel; jika gagal, catat hit percobaan gagal dan lempar error validasi
        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        // Jika login berhasil, bersihkan hitungan percobaan gagal pada RateLimiter
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     * Memastikan pengguna tidak melebihi batas maksimal 5 kali percobaan login gagal berturut-turut.
     *
     * @throws ValidationException  Jika percobaan melebihi batas 5 kali
     * @return void
     */
    protected function ensureIsNotRateLimited(): void
    {
        // Izinkan selama total percobaan gagal belum mencapai 5 kali
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Picu event Lockout dan hitung sisa waktu tunggu sebelum pengguna boleh mencoba lagi
        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     * Membentuk kunci unik pembatas login berdasarkan kombinasi email (huruf kecil) dan alamat IP pengguna.
     *
     * @return string
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
