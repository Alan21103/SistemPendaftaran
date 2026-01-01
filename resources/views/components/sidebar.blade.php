<aside class="flex h-screen w-64 flex-col overflow-y-auto bg-white px-5 py-6 border-r border-gray-100 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">

    {{-- Penentuan User yang Sedang Login --}}
    @php
        $isLoggedAsAdmin = Auth::guard('admin')->check();
        $isLoggedAsTU = Auth::guard('tata_usaha')->check();
        
        // Ambil data user & label berdasarkan guard yang aktif
        if ($isLoggedAsAdmin) {
            $user = Auth::guard('admin')->user();
            $roleLabel = 'Administrator';
        } else {
            $user = Auth::guard('tata_usaha')->user();
            $roleLabel = 'Tata Usaha';
        }

        // Variabel penentu hak akses menu (Hanya admin asli yang bisa lihat menu pendaftaran & pembayaran)
        $canAccessAll = $isLoggedAsAdmin;
    @endphp

    <div class="flex justify-center mb-8">
        <img src="{{ asset('storage/logosd.png') }}" alt="Logo Sekolah" class="h-12 w-auto object-contain">
    </div>

    {{-- Bagian Profil --}}
    <a href="{{ route('admin.dashboard') }}" 
       class="flex items-center gap-3 px-3 py-3 mb-6 rounded-2xl bg-gray-50 border border-gray-100 hover:border-indigo-100 hover:shadow-sm transition-all duration-300 group">
        
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-indigo-600 shadow-sm ring-1 ring-gray-100 group-hover:scale-105 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
        </span>

        <div class="flex flex-col">
            <h2 class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">
                {{ Str::limit($user->nama ?? 'User', 15) }}
            </h2>
            <p class="text-xs font-medium text-gray-400 group-hover:text-indigo-400 transition-colors">
                {{ $roleLabel }}
            </p>
        </div>
    </a>

    <nav class="flex flex-col space-y-1.5">

        {{-- Dashboard (Bisa diakses Admin & Tata Usaha) --}}
        @php $isActive = request()->routeIs('admin.dashboard'); @endphp
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
            @if ($isActive) bg-indigo-50 text-indigo-700 font-bold shadow-sm ring-1 ring-indigo-100
            @else text-gray-500 font-medium hover:bg-gray-50 hover:text-gray-900 @endif">
            
            <img src="{{ asset('icons/dashboard.svg') }}" alt="Dashboard Icon"
                class="h-5 w-5 transition-colors text-gray-700 group-hover:text-gray-900">
            <span>Dashboard</span>
        </a>

        {{-- Konten (Bisa diakses Admin & Tata Usaha) --}}
        @php $isActive = request()->routeIs('admin.konten.index*'); @endphp
        <a href="{{ route('admin.konten.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           @if ($isActive) 
               bg-indigo-50 text-indigo-700 font-bold shadow-sm ring-1 ring-indigo-100
           @else 
               text-gray-500 font-medium hover:bg-gray-50 hover:text-gray-900 
           @endif">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="{{ $isActive ? '2' : '1.5' }}" stroke="currentColor" 
                 class="h-5 w-5 transition-colors {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <span>Konten</span>
        </a>

        {{-- MENU KHUSUS ADMIN (Hidden untuk Tata Usaha) --}}
        @if($canAccessAll)
            {{-- Pendaftaran --}}
            @php $isActive = request()->routeIs('admin.pendaftaran.index'); @endphp
            <a href="{{ route('admin.pendaftaran.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
               @if ($isActive) 
                   bg-indigo-50 text-indigo-700 font-bold shadow-sm ring-1 ring-indigo-100
               @else 
                   text-gray-500 font-medium hover:bg-gray-50 hover:text-gray-900 
               @endif">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="{{ $isActive ? '2' : '1.5' }}" stroke="currentColor" 
                     class="h-5 w-5 transition-colors {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
                <span>Pendaftaran</span>
            </a>

            {{-- Pembayaran --}}
            @php $isActive = request()->routeIs('admin.pembayaran.*'); @endphp
            <a href="{{ route('admin.pembayaran.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
                @if ($isActive) 
                    bg-indigo-50 text-indigo-700 font-bold shadow-sm ring-1 ring-indigo-100
                @else 
                    text-gray-500 font-medium hover:bg-gray-50 hover:text-gray-900 
                @endif">

                <img src="{{ asset('icons/pembayaran.svg') }}" alt="Dashboard Icon"
                class="h-5 w-5 transition-colors text-gray-700 group-hover:text-gray-900">
                <span>Pembayaran</span>
            </a>
        @endif

    </nav>

    <div class="mt-auto">
        <div class="my-2 border-t border-gray-100"></div>

        <a href="#" id="logoutButton"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 font-medium transition-all duration-200 hover:bg-rose-50 hover:text-rose-600 hover:shadow-sm group">
            
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                 class="h-5 w-5 text-gray-400 group-hover:text-rose-500 transition-colors">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
            </svg>
            <span>Logout</span>
        </a>
    </div>

    @if(Auth::guard('admin')->check() || Auth::guard('tata_usaha')->check())
        @vite('resources/js/logout.js')
    @endif
</aside>