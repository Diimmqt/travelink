<?php

// Mengembalikan konfigurasi layanan payment gateway Midtrans yang diambil dari file .env
return [
    // Kunci rahasia server Midtrans untuk komunikasi backend (membuat transaksi & verifikasi notifikasi)
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    // Kunci publik klien Midtrans yang digunakan di frontend saat menampilkan popup pembayaran Snap
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    // Penanda mode produksi (true = pembayaran asli/live, false = mode uji coba/sandbox)
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    // URL script Snap.js Midtrans yang otomatis menyesuaikan antara server produksi atau sandbox
    'snap_url'      => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
