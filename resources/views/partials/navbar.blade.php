{{-- Navigation Bar --}}
<header class="w-full bg-white sticky top-0 z-50 px-6 lg:px-16 py-4 flex justify-between items-center transition-all duration-300 shadow-sm">

    {{-- 1. Logo --}}
    <div class="flex-shrink-0">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('storage/logosd.png') }}" alt="Logo SD" class="h-10 lg:h-12 w-auto transition-transform duration-300">
        </a>
    </div>

    {{-- 2. Menu Links (Centered) --}}
    <nav id="navbar-menu" class="hidden md:flex items-center space-x-8 lg:space-x-10">
        <a href="{{ url('/') }}#beranda" class="nav-link text-[#002060] font-medium text-sm lg:text-base transition-all duration-300 hover:text-blue-800">Beranda</a>
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

    {{-- 3. Auth Buttons --}}
    <div class="flex items-center space-x-3 flex-shrink-0">
        @if (Route::has('login'))
            @auth
                <a href="#" id="logoutButton" class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all active:scale-95 shadow-sm">
                    Logout
                </a>
            @else
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all shadow-sm active:scale-95">
                        Sign Up
                    </a>
                @endif
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

        /**
         * Fungsi untuk memperbarui status 'active' pada navbar
         * Berdasarkan Hash (#) di URL atau Pathname
         */
        function updateActiveNavbar() {
            const currentPath = window.location.pathname;
            const currentHash = window.location.hash;

            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                
                // Reset state ke default
                link.classList.remove('nav-link-active');
                link.classList.add('font-medium');

                // 1. Logika untuk Hash (Anchor Link di halaman yang sama)
                if (currentHash && linkHref.includes(currentHash)) {
                    link.classList.add('nav-link-active');
                    link.classList.remove('font-medium');
                }
                // 2. Logika untuk Halaman Utama tanpa Hash (Default ke Beranda)
                else if (!currentHash && currentPath === '/' && linkHref.includes('#beranda')) {
                    link.classList.add('nav-link-active');
                    link.classList.remove('font-medium');
                }
                // 3. Logika untuk Halaman Route (seperti /pendaftaran/create)
                else if (!currentHash && linkHref.includes(currentPath) && currentPath !== '/') {
                    link.classList.add('nav-link-active');
                    link.classList.remove('font-medium');
                }
            });
        }

        // Jalankan saat halaman pertama kali dimuat
        updateActiveNavbar();

        // Jalankan setiap kali ada perubahan Hash (misal: klik tombol Informasi PPDB)
        window.addEventListener('hashchange', updateActiveNavbar);

        // Tambahan: Event click untuk navigasi instan
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Beri jeda sangat singkat agar hash terupdate di URL sebelum fungsi dipanggil
                setTimeout(updateActiveNavbar, 10);
            });
        });
    });
</script>