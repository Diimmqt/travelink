<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | Travelink</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brex-fog font-sans antialiased text-brex-graphite min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-brex-paper border border-brex-mist rounded-brex p-8 text-center shadow-none">
        <div class="w-16 h-16 bg-brex-fog border border-brex-mist rounded-full flex items-center justify-center mx-auto mb-6 text-brex-ember">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-semibold text-brex-ink mb-2 tracking-brex-24">403</h1>
        <h2 class="text-xl font-semibold text-brex-ink mb-3 tracking-brex-24">Akses Ditolak</h2>
        <p class="text-sm text-brex-graphite mb-8 leading-relaxed">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman administrator ini.' }}
        </p>
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-brex-ember text-white font-medium rounded-brex hover:bg-[#e04f00] transition duration-150 ease-in-out shadow-none">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
