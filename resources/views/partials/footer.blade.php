<div class="relative w-full overflow-hidden leading-[0] rotate-180 bg-white">
    <svg class="relative block w-[calc(100%+1.3px)] h-[60px] lg:h-[80px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#002060"></path>
    </svg>
</div>

<footer class="w-full bg-[#002060] text-white py-12 px-6 lg:px-16">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-24">
        
        {{-- Kolom 1: Informasi Kontak --}}
        <div class="space-y-6">
            <h2 class="text-2xl font-bold leading-tight">SD Muhammadiyah Ambarketawang 2</h2>
            <div class="space-y-4">
                {{-- Alamat --}}
                <div class="flex items-start gap-4">
                    <svg class="w-6 h-6 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-sm leading-relaxed">
                        Kalimanjung, Ambarketawang, Kec. Gamping, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55294
                    </p>
                </div>

                {{-- Telepon --}}
                <div class="flex items-center gap-4">
                    <svg class="w-6 h-6 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <p class="text-sm">0813-2883-2247</p>
                </div>

                {{-- Email --}}
                <div class="flex items-center gap-4">
                    <svg class="w-6 h-6 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm">sdm.ambar2@gmail.com</p>
                </div>
            </div>
        </div>

        {{-- Kolom 2: Link & Jam Operasional --}}
        <div class="flex flex-col space-y-10">
            <div>
                <h3 class="text-xl font-bold mb-4">Link</h3>
                <a href="https://sdmuhambarketawang.sch.id" class="text-sm hover:underline">https://sdmuhambarketawang.sch.id</a>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Jam Operasional</h3>
                <p class="text-sm leading-relaxed">Senin - Jumat : 08.30-15.30.</p>
            </div>
        </div>

        {{-- Kolom 3: Partner --}}
        <div>
            <h3 class="text-xl font-bold mb-6">Partner</h3>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white p-2 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('storage/ti.png') }}" alt="Partner 1" class="h-12 w-auto">
                </div>
                <div class="bg-white p-2 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('storage/umy.png') }}" alt="Partner 2" class="h-12 w-auto">
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-white/10 text-center text-xs opacity-60">
        &copy; {{ date('Y') }} SD Muhammadiyah Ambarketawang 2. All rights reserved.
    </div>
</footer>