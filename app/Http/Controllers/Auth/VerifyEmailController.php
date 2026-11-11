<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Controller invokable untuk memverifikasi alamat email pengguna saat mengklik tautan verifikasi.
 */
class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * Menandai alamat email user yang sedang login sebagai terverifikasi lalu mengarahkan ke dashboard.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request  Request tervalidasi berisi tanda tangan tautan verifikasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Jika email sudah pernah diverifikasi sebelumnya, langsung arahkan ke dashboard dengan parameter verified=1
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        // Tandai email telah terverifikasi di database dan picu event Verified bawaan Laravel
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
