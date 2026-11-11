<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware untuk membatasi akses route berdasarkan satu atau beberapa peran (role) pengguna yang diizinkan
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    // Memeriksa login dan kecocokan role pengguna; menerima $request, $next, dan daftar ...$roles yang diizinkan, lalu mengembalikan Response
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika pengguna belum login, arahkan ke halaman login terlebih dahulu
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Ambil role milik pengguna yang sedang login
        $userRole = auth()->user()->role;

        // Support both variadic role:admin,kasir and multiple parameters
        // Kumpulkan seluruh role yang diizinkan dengan memecah string yang dipisah koma dan menghapus spasi berlebih
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                // Hanya masukkan ke daftar jika nilainya tidak kosong
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // Jika role pengguna tidak termasuk dalam daftar role yang diizinkan (perbandingan ketat), hentikan dengan error 403
        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Akses Ditolak');
        }

        // Role sesuai: lanjutkan request ke tahap pemrosesan berikutnya
        return $next($request);
    }
}

