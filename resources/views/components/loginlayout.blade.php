<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SD Muhammadiyah 2 Ambarketawang') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('storage/favicon.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('storage/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media (max-width: 768px) {

            /* LOGO KIRI ATAS */
            .absolute.top-0.left-0.p-6 {
                position: relative;
                padding: 1rem;
                display: flex;
                justify-content: center;
            }

            .absolute.top-0.left-0 img {
                height: 40px; /* lebih kecil di mobile */
                width: auto;
            }

            /* WRAPPER UTAMA */
            .min-h-screen {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }

            /* CARD LOGIN */
            .sm\:max-w-md {
                max-width: 100%;
            }

            .shadow-xl {
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            }

            .rounded-3xl {
                border-radius: 1.25rem;
            }

            .px-6 {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }

            .py-8 {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }
        }

        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
        {{-- BLOK BARU UNTUK LOGO DI POJOK KIRI ATAS --}}
        <div class="absolute top-0 left-0 p-6">
            <a href="/">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('storage/logosd.png') }}" alt="Logo" class="w-70 h-12">
                </div>
            </a>
        </div>
        {{-- AKHIR BLOK LOGO --}}
        
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden rounded-3xl">
                {{-- Konten dari halaman login akan dimasukkan di sini --}}
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
