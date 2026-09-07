@props(['hideNav' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Travelink') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-white text-brex-ink antialiased min-h-screen flex flex-col selection:bg-brex-ember selection:text-white" style="font-feature-settings: 'calt' 0, 'liga' 0;">
        <div class="min-h-screen flex flex-col bg-white">
            @if(!$hideNav)
                <livewire:layout.navigation />

                <!-- Page Heading (Optional) -->
                @if (isset($header))
                    <header class="bg-white border-b border-brex-mist py-5">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h1 class="text-xl font-semibold text-brex-ink tracking-brex-24">
                                {{ $header }}
                            </h1>
                        </div>
                    </header>
                @endif
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
