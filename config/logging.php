<?php

// Mengimpor handler dan processor bawaan dari library Monolog untuk mengelola tujuan dan format pencatatan log.
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

// Mengembalikan konfigurasi saluran pencatatan log (logging) aplikasi.
return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    // Channel log utama yang dipakai aplikasi (bawaan: 'stack' untuk menggabungkan beberapa channel sekaligus).
    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    // Channel khusus untuk mencatat peringatan fitur PHP/library yang sudah usang (deprecated) dan opsi pelacakan stack trace-nya.
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    // Daftar lengkap channel log yang bisa dipilih beserta pengaturan driver, lokasi file, dan level minimum log-nya.
    'channels' => [

        // Menggabungkan beberapa channel log (dipisah koma di LOG_STACK) agar satu log bisa dikirim ke banyak tujuan sekaligus.
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        // Menulis semua catatan log ke dalam satu file tunggal di storage/logs/laravel.log.
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // Membuat file log baru setiap hari dan otomatis menghapus file log lama setelah jumlah hari tertentu (bawaan 14 hari).
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        // Mengirim notifikasi log berlevel kritis (atau sesuai LOG_LEVEL) ke Slack menggunakan webhook URL.
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        // Mengirim log ke layanan eksternal Papertrail melalui koneksi Syslog UDP/TLS.
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        // Menulis log ke aliran error standar (php://stderr), berguna saat aplikasi berjalan di container/Docker.
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        // Mengirim log ke sistem syslog bawaan server/OS.
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        // Mengirim log ke fungsi error_log bawaan PHP.
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // Channel pembuangan yang mengabaikan semua log (tidak mencatat apa pun).
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        // Channel darurat yang dipakai Laravel jika channel utama gagal dijalankan.
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
