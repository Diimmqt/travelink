<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

/**
 * Controller untuk menangani proses autentikasi (login dan registrasi otomatis) menggunakan akun Google (OAuth2) via Laravel Socialite.
 */
class GoogleAuthController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman persetujuan login Google OAuth.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback dari Google setelah pengguna berhasil login, lalu mencari/membuat akun user
     * dan memulai sesi login di aplikasi.
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP callback dari Google
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        try {
            // Ambil profil pengguna dari Google berdasarkan kode otorisasi callback
            $googleUser = Socialite::driver('google')->user();

            // Jalankan pencarian/pembuatan user dalam transaksi database agar konsisten
            $user = DB::transaction(function () use ($googleUser) {
                // Cari user berdasarkan google_id atau email yang sama dengan akun Google
                $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

                if ($user) {
                    // KONDISI A (User Lama Ditemukan - Pernah daftar manual sebelum ada fitur Google Login)
                    if (empty($user->google_id)) {
                        $user->update([
                            'google_id' => $googleUser->getId(),
                            'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                            'email_verified_at' => $user->email_verified_at ?? now(),
                        ]);
                    }
                } else {
                    // KONDISI B (User Baru - Email belum ada di database)
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                        'password' => null,
                    ]);
                }

                return $user;
            });

            // Login-kan user ke sesi Laravel (remember = true) dan regenerasi ID session untuk keamanan session fixation
            Auth::login($user, true);
            $request->session()->regenerate();

            // Arahkan ke halaman tujuan awal sebelum login, atau ke /dashboard sebagai default
            return redirect()->intended('/dashboard');
        } catch (\Throwable $e) {
            // Catat detail error ke log jika proses OAuth Google gagal atau dibatalkan pengguna
            Log::error('Google Login Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return redirect('/login')->with('error', 'Gagal login dengan Google, silakan coba lagi.');
        }
    }
}

