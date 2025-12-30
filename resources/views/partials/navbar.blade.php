{{-- Navigation Bar --}}
<header class="w-full bg-white sticky top-0 z-50 px-6 lg:px-16 py-4 flex justify-between items-center transition-all duration-300">

    {{-- 1. Logo --}}
    <div class="flex-shrink-0">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('storage/logosd.png') }}" alt="Logo SD" class="h-10 lg:h-12 w-auto transform hover:scale-105 transition-transform duration-300">
        </a>
    </div>

    {{-- 2. Menu Links (Centered) --}}
    <nav id="navbar-menu" class="hidden md:flex items-center space-x-8 lg:space-x-10">
        <a href="{{ url('/') }}#beranda" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Beranda</a>
        <a href="{{ url('/') }}#tentang-sekolah" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Tentang Kami</a>
        <a href="{{ url('/') }}#ekstrakurikuler" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Ekstrakurikuler</a>
        <a href="{{ url('/') }}#tenaga-pengajar" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Tenaga Pengajar</a>
        
        {{-- LOGIKA DINAMIS PENDAFTARAN --}}
        @auth
            @if(auth()->user()->pendaftaran)
                <a href="{{ route('pendaftaran.index') }}" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Status Pendaftaran</a>
            @else
                <a href="{{ route('pendaftaran.create') }}" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Formulir Daftar</a>
            @endif
        @else
            <a href="{{ url('/') }}#ppdb" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Pendaftaran</a>
        @endauth
    </nav>

    {{-- 3. Auth Buttons (Sign Up & Sign In) --}}
    <div class="flex items-center space-x-3 flex-shrink-0">
        @if (Route::has('login'))
            @auth
                {{-- Tombol Logout untuk yang sudah Login --}}
                <a href="#" id="logoutButton" class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all active:scale-95 shadow-sm">
                    Logout
                </a>
            @else
                {{-- Tombol Sign Up --}}
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all shadow-sm active:scale-95">
                        Sign Up
                    </a>
                @endif

                {{-- Tombol Sign In --}}
                <a href="{{ route('login') }}" class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all shadow-sm active:scale-95">
                    Sign In
                </a>
            @endauth
        @endif
    </div>

    @auth
        @vite('resources/js/logout.js')
    @endauth
</header>

<style>
    /* Class untuk link aktif (Bold & Timbul) */
    .nav-link-active {
        font-weight: 800 !important; 
        color: #002060 !important;
        transform: scale(1.15); 
        display: inline-block;
    }
    
    .nav-link {
        display: inline-block;
        transition: all 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        const currentUrl = window.location.href;

        function removeActiveClasses() {
            navLinks.forEach(link => {
                link.classList.remove('nav-link-active');
                link.classList.add('font-medium');
            });
        }

        navLinks.forEach(link => {
            // Efek saat diklik
            link.addEventListener('click', function() {
                removeActiveClasses();
                this.classList.add('nav-link-active');
                this.classList.remove('font-medium');
            });

            // Deteksi otomatis link aktif berdasarkan URL
            if (currentUrl.includes(link.getAttribute('href'))) {
                link.classList.add('nav-link-active');
                link.classList.remove('font-medium');
            }
        });

        // Default: Beranda aktif jika di halaman utama tanpa hash
        if (window.location.pathname === '/' && !window.location.hash) {
            if (navLinks[0]) navLinks[0].classList.add('nav-link-active');
        }
    });
</script>