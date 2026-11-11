<?php

// Mengembalikan konfigurasi pengiriman email (mailer bawaan, daftar transport, dan alamat pengirim global).
return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    // Mailer bawaan yang dipakai saat mengirim email (bawaan: 'log', yaitu menulis isi email ke file log untuk pengembangan lokal).
    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "log", "array", "failover", "roundrobin"
    |
    */

    // Daftar konfigurasi berbagai layanan/metode pengiriman email yang didukung aplikasi.
    'mailers' => [

        // Mengirim email melalui server SMTP menggunakan host, port, enkripsi, dan akun dari file .env.
        'smtp' => [
            'transport' => 'smtp',
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        // Mengirim email melalui layanan Amazon SES.
        'ses' => [
            'transport' => 'ses',
        ],

        // Mengirim email melalui layanan Postmark.
        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        // Mengirim email menggunakan program sendmail yang terpasang di server Linux/Unix.
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        // Tidak mengirim email sungguhan, melainkan mencatat isi email ke file log Laravel (aman untuk development).
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        // Menyimpan email hanya di array memori sementara (khusus untuk automated testing).
        'array' => [
            'transport' => 'array',
        ],

        // Mode cadangan otomatis: mencoba kirim lewat 'smtp' dulu, jika gagal beralih ke 'log'.
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    // Alamat email dan nama pengirim bawaan untuk semua email keluar jika tidak diatur secara khusus.
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];
