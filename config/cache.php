<?php

// Mengimpor helper Str dari Laravel untuk mengubah nama aplikasi menjadi format slug pada prefix cache.
use Illuminate\Support\Str;

// Mengembalikan konfigurasi penyimpanan cache aplikasi.
return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    // Driver cache bawaan yang digunakan jika tidak disebutkan secara khusus (bawaan: 'database').
    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "octane", "null"
    |
    */

    // Daftar pilihan media penyimpanan (store) cache yang tersedia di aplikasi.
    'stores' => [

        // Menyimpan cache hanya di memori sementara selama satu request (cocok untuk pengujian/testing).
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        // Menyimpan data cache di dalam tabel database (bawaan tabel 'cache').
        'database' => [
            'driver' => 'database',
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'connection' => env('DB_CACHE_CONNECTION', null),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION', null),
        ],

        // Menyimpan cache sebagai file fisik di folder storage/framework/cache/data.
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        // Menyimpan cache di server Memcached untuk performa cepat berbasis memori.
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        // Menyimpan cache di server Redis (cepat dan mendukung locking terdistribusi).
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        // Menyimpan cache menggunakan layanan cloud AWS DynamoDB.
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        // Store cache khusus saat aplikasi dijalankan menggunakan Laravel Octane.
        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, and DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    // Awalan (prefix) untuk setiap kunci cache agar tidak bentrok jika server cache dipakai oleh beberapa aplikasi sekaligus.
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

];
