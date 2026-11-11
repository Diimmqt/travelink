<?php

// Mengembalikan konfigurasi sistem penyimpanan file (lokal maupun cloud) di Laravel.
return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    // Disk penyimpanan bawaan yang digunakan saat aplikasi menyimpan file tanpa menentukan nama disk.
    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    // Daftar disk penyimpanan file yang tersedia beserta lokasi/kredensialnya masing-masing.
    'disks' => [

        // Disk lokal privat: menyimpan file di storage/app yang tidak bisa diakses langsung dari browser.
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        // Disk lokal publik: menyimpan file di storage/app/public agar bisa diakses lewat URL /storage (setelah storage:link).
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Disk cloud Amazon S3 (atau layanan kompatibel S3 lainnya seperti MinIO) menggunakan kredensial AWS dari .env.
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    // Pemetaan symbolic link (shortcut folder) yang dibuat oleh perintah `php artisan storage:link`.
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
