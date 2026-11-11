<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware untuk memastikan hanya pengguna dengan peran (role) 'admin' yang boleh mengakses route tertentu
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // Memeriksa hak akses admin pada setiap request yang masuk; menerima $request dan callback $next, lalu mengembalikan Response
    public function handle(Request $request, Closure $next): Response
    {
        // Tolak akses dengan kode HTTP 403 jika pengguna belum login atau rolenya bukan 'admin'
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator.');
        }

        // Lolos pemeriksaan: teruskan request ke controller atau middleware berikutnya
        return $next($request);
    }
}
