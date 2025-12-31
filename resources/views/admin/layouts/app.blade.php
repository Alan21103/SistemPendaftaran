<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard | @yield('title', 'Sistem Pendaftaran')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/logout.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100">


    <main>
        @yield('content')
    </main>

    @auth('admin')
        <script>
            const BASE_URL = '{{ url('/') }}'; 
        </script>
        {{-- Pastikan pendaftaran-status.js hanya dimuat jika admin login --}}
        @vite(['resources/js/pendaftaran-status.js'])
    @endauth
</body>
</html>