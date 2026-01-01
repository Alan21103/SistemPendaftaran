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
         @media (max-width: 768px) {
            /* 1. Mengurangi padding utama agar konten lebih lebar */
            main {
                padding: 1rem !important;
            }

            /* 2. Header Halaman */
            h1.text-3xl {
                font-size: 1.5rem !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        {{-- Anggap nama filenya sidebar.blade.php di folder components --}}
        @include('components.sidebar')

        <div class="flex flex-col flex-1 w-full overflow-hidden">
            
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-100 lg:hidden shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="p-2 text-gray-500 hover:bg-gray-50 rounded-lg focus:outline-none ring-1 ring-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <span class="font-bold text-gray-800 tracking-tight">Dashoard Admin</span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto min-h-0">
                @yield('content')
            </main>
        </div>

        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            x-cloak
            class="fixed inset-0 z-40 bg-gray-900/40 backdrop-blur-sm lg:hidden transition-opacity"
            x-transition:enter="transition opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
        </div>
    </div>

    @auth('admin')
        <script>
            const BASE_URL = '{{ url('/') }}'; 
        </script>
        @vite(['resources/js/pendaftaran-status.js'])
    @endauth

    @stack('scripts')
</body>
</html>