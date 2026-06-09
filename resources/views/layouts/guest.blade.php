<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Perbesar semua input di halaman auth */
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                height: 52px !important;
                font-size: 15px !important;
                padding-left: 14px !important;
                padding-right: 14px !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">

        <div class="relative min-h-screen flex items-center justify-center">

            {{-- Background Image --}}
            <img src="https://api-www.louvre.fr/sites/default/files/2021-01/cour-napoleon-et-pyramide_1.jpg"
                 alt="Museum"
                 class="absolute inset-0 w-full h-full object-cover">

            {{-- Overlay Gelap --}}
            <div class="absolute inset-0 bg-black/60"></div>

            {{-- Form Card — putih solid --}}
            <div class="relative z-10 w-full max-w-lg px-10 py-12 bg-white shadow-2xl rounded-xl mx-4">

                {{-- Logo --}}
                <a href="/" class="block text-center mb-8">
                    <h1 class="text-3xl font-serif tracking-widest uppercase text-black">Alphaseum</h1>
                    <p class="text-xs tracking-[0.3em] text-gray-400 uppercase mt-1">Museum of Art</p>
                </a>

                {{ $slot }}

            </div>
        </div>

    </body>
</html>