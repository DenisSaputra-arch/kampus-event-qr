<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F5F5F5] text-charcoal-900 flex flex-col min-h-screen">
        <div class="flex-1">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- University Editorial Footer -->
        <footer class="bg-charcoal-900 text-gray-400 text-xs py-8 border-t border-neutral-800 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
                <div>
                    <p class="font-black text-white text-sm tracking-wide">KAMPUS<span class="text-gold-500">EVENT</span></p>
                    <p class="text-gray-400 mt-1">Portal Pendaftaran & Verifikasi Presensi Acara Kampus dengan E-Tiket QR Code.</p>
                </div>
                <div class="text-neutral-500 text-[11px]">
                    &copy; {{ date('Y') }} Kampus Event QR. Hak Cipta Dilindungi.
                </div>
            </div>
        </footer>
    </body>
</html>
