@if (Route::has('login'))
    @auth
        {{-- Tombol Logout --}}
        <a href="#" id="logoutButton" 
           class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all active:scale-95 shadow-sm text-center">
            Logout
        </a>
    @else
        <div class="flex flex-col md:flex-row gap-3">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" 
                   class="px-7 py-2 text-xs lg:text-sm font-bold text-white bg-[#002060] rounded-full hover:bg-blue-950 transition-all shadow-sm active:scale-95 text-center">
                    Sign Up
                </a>
            @endif
            <a href="{{ route('login') }}" 
               class="px-7 py-2 text-xs lg:text-sm font-bold text-[#002060] border-2 border-[#002060] rounded-full hover:bg-gray-50 transition-all shadow-sm active:scale-95 text-center md:bg-transparent">
                Sign In
            </a>
        </div>
    @endauth
@endif