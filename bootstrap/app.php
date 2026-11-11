<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Mengonfigurasi dan menginisialisasi instance aplikasi Laravel dengan menentukan direktori root proyek
return Application::configure(basePath: dirname(__DIR__))
    // Menentukan lokasi file routing web, perintah konsol (Artisan), serta URL health check '/up'
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Mendaftarkan alias middleware kustom agar bisa dipakai di file rute untuk membatasi akses berdasarkan peran (role)
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    // Tempat untuk mengatur penanganan error atau exception global pada aplikasi
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
