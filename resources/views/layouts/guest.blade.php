<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Jarak scroll agar konten tidak tertutup sticky navbar */
            section[id], main[id], .scroll-mt-fix {
                scroll-margin-top: 100px;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-white">
        
        {{-- 1. Navbar diletakkan di sini tanpa pembungkus container agar FULL WIDTH --}}
        @include('partials.navbar')

        {{-- 2. Konten utama ($slot) --}}
        <div class="min-h-screen">
            <main>
                {{ $slot }}
            </main>
        </div>
        
    </body>
</html>