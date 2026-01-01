{{-- Navigation Bar --}}
{{-- Tambahkan x-data untuk kontrol menu mobile --}}
<header x-data="{ mobileMenuOpen: false }" class="w-full bg-white sticky top-0 z-[100] px-6 lg:px-16 py-4 flex justify-between items-center shadow-sm transition-all duration-300">

    {{-- 1. Logo --}}
    <div class="flex-shrink-0 relative z-[110]">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('storage/logosd.png') }}" alt="Logo SD" class="h-10 lg:h-12 w-auto transition-transform duration-300">
        </a>
    </div>

    {{-- 2. Menu Links (Desktop) --}}
    <nav class="hidden md:flex items-center space-x-8 lg:space-x-10">
        @include('partials.nav-links') {{-- Pisahkan link agar tidak nulis 2x --}}
    </nav>

    {{-- 3. Auth Buttons (Desktop) --}}
    <div class="hidden md:flex items-center space-x-3 flex-shrink-0">
        @include('partials.auth-buttons')
    </div>

    {{-- 4. Mobile Toggle Button (Hanya Muncul di Mobile) --}}
    <div class="flex md:hidden items-center z-[110]">
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-[#002060] focus:outline-none p-2" aria-label="Toggle Menu">
            <template x-if="!mobileMenuOpen">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </template>
            <template x-if="mobileMenuOpen">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </template>
        </button>
    </div>

    {{-- 5. Mobile Menu Overlay --}}
    <div 
        x-show="mobileMenuOpen" 
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-5"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-5"
        class="absolute top-full left-0 w-full bg-white border-b border-gray-100 shadow-xl py-6 px-8 flex flex-col space-y-5 md:hidden z-[100]"
    >
        @include('partials.nav-links')
        <hr class="border-gray-100">
        <div class="flex flex-col space-y-3">
            @include('partials.auth-buttons')
        </div>
    </div>

    @auth
        @vite('resources/js/logout.js')
    @endauth
</header>

{{-- Pisahkan Link Navigasi (Buat file baru: partials/nav-links.blade.php atau biarkan di sini) --}}
{{-- Catatan: Jika tidak ingin dipisah, copy-paste link manual ke bagian desktop dan mobile --}}

<style>
    /* Gunakan prefix 'custom-' agar tidak bentrok dengan framework lain */
    .custom-nav-link {
        display: inline-block;
        transition: all 0.3s ease;
        position: relative;
    }

    .custom-nav-link-active {
        font-weight: 800 !important; 
        color: #002060 !important; 
    }

    /* Efek underline tipis untuk link aktif di desktop */
    @media (min-width: 768px) {
        .custom-nav-link-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #002060;
        }
    }
    
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link, .custom-nav-link');

        function updateActiveNavbar() {
            const currentPath = window.location.pathname;
            const currentHash = window.location.hash;

            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                link.classList.remove('custom-nav-link-active');
                
                // Logika Hash
                if (currentHash && linkHref.includes(currentHash)) {
                    link.classList.add('custom-nav-link-active');
                }
                // Logika Home
                else if (!currentHash && currentPath === '/' && linkHref.includes('#beranda')) {
                    link.classList.add('custom-nav-link-active');
                }
                // Logika Route
                else if (!currentHash && linkHref.includes(currentPath) && currentPath !== '/') {
                    link.classList.add('custom-nav-link-active');
                }
            });
        }

        updateActiveNavbar();
        window.addEventListener('hashchange', updateActiveNavbar);
        
        // Tutup menu mobile saat link diklik
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                setTimeout(updateActiveNavbar, 10);
            });
        });
    });
</script>