<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SD Muhammadiyah 2 Ambarketawang</title>

        {{-- Swiper CSS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        
        {{-- AOS CSS --}}
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Memastikan transisi antar section halus */
            html {
                scroll-behavior: smooth;
            }
            
            section[id], main[id] {
                scroll-margin-top: 100px;
            }
            
            /* Tambahan agar efek timbul di navbar tidak merusak layout */
            .nav-link {
                display: inline-block;
                transition: all 0.3s ease;
            }

            .swiper-pagination-bullet-active {
                background: #002060 !important;
            }
            .swiper-grid-column .swiper-wrapper {
                flex-direction: row !important;
            }
            @media (max-width: 1024px) {
                .swiper-button-next, .swiper-button-prev {
                    display: none;
                }
            }
            .floating { 
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0% { transform: translatey(0px); }
                50% { transform: translatey(-20px); }
                100% { transform: translatey(0px); }
            }
        </style>
    </head>
    <body class="antialiased font-sans bg-white overflow-x-hidden">
        
        {{-- 1. Navbar (Full Width) --}}
        @include('partials.navbar')

        {{-- 2. Container Utama --}}
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            
            {{-- SECTION BERANDA --}}
            <main id="beranda" class="grid lg:grid-cols-2 gap-12 items-center py-12 lg:py-24">
                @if ($kontenBeranda)
                    @php
                        $media_utama = $kontenBeranda->media->where('urutan', 0)->first();
                        $image_url = $media_utama ? asset('storage/' . $media_utama->file_path) : asset('storage/default.png');
                    @endphp
                    <div class="text-center lg:text-left" data-aos="fade-right" data-aos-duration="1000">
                        <h1 class="text-4xl md:text-5xl font-bold text-black leading-tight">
                            {!! nl2br(e($kontenBeranda->judul)) !!} 
                        </h1>
                        <p class="mt-10 text-lg text-black">
                            {!! nl2br(e($kontenBeranda->isi)) !!}
                        </p>
                    </div>
                    <div class="floating" data-aos="fade-left" data-aos-duration="1000">
                        <img src="{{ $image_url }}" class="w-full h-auto object-cover rounded-xl shadow-2xl">
                    </div>
                @endif
            </main>

            {{-- SECTION TENTANG SEKOLAH --}}
            <section id="tentang-sekolah" class="py-20 lg:py-24">
                @if ($kontenTentangSekolah && $kontenTentangSekolah->count() > 0)
                    <h2 class="text-3xl md:text-4xl font-extrabold text-center text-black mb-8" data-aos="fade-up">Tentang sekolah</h2>
                    <div class="max-w-5xl mx-auto px-4">
                        @php
                            $deskripsiUtama = $kontenTentangSekolah->first(fn($i) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($i->judul), ['sejarah', 'tentang sekolah']));
                            $visiData = $kontenTentangSekolah->first(fn($i) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($i->judul), 'visi'));
                            $misiData = $kontenTentangSekolah->first(fn($i) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($i->judul), 'misi'));
                        @endphp

                        @if($deskripsiUtama)
                        <div class="text-center mb-16" data-aos="fade-up" data-aos-delay="200">
                            <p class="text-blue-900 leading-relaxed text-lg md:text-xl font-medium">
                                {!! nl2br(e($deskripsiUtama->isi)) !!}
                            </p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                            @if($visiData)
                            <div class="bg-[#EFE4D2] p-8 md:p-10 rounded-2xl text-center" data-aos="flip-left" data-aos-delay="400">
                                <h3 class="text-2xl font-bold text-black mb-6">{{ $visiData->judul }}</h3>
                                <p class="text-gray-800 leading-relaxed text-lg">{{ $visiData->isi }}</p>
                            </div>
                            @endif

                            @if($misiData)
                            <div class="bg-[#EFE4D2] p-8 md:p-10 rounded-2xl text-center" data-aos="flip-right" data-aos-delay="600">
                                <h3 class="text-2xl font-bold text-black mb-6">{{ $misiData->judul }}</h3>
                                <p class="text-gray-800 leading-relaxed text-lg">{{ $misiData->isi }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            {{-- SECTION EKSTRAKURIKULER --}}
            <section id="ekstrakurikuler" class="py-20 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="text-center mb-16" data-aos="fade-up">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-black">Ekstrakurikuler</h2>
                    </div>
                    @if(isset($kontenEkstrakurikuler) && $kontenEkstrakurikuler->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($kontenEkstrakurikuler as $index => $ekskul)
                                <div class="group rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 bg-white h-full flex flex-col" 
                                     data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                    <div class="relative h-64 w-full bg-gray-100 overflow-hidden" id="slider-{{ $ekskul->id }}" data-current="0" data-total="{{ $ekskul->media->count() }}">
                                        @foreach($ekskul->media as $mIndex => $media)
                                            <div class="slide-item absolute inset-0 w-full h-full transition-opacity duration-500 {{ $mIndex === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                                                <img src="{{ asset('storage/' . $media->file_path) }}" class="w-full h-full object-cover" alt="{{ $ekskul->judul }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="p-6 text-center flex-1 flex flex-col justify-center bg-[#F2E8D9]">
                                        <h3 class="text-xl font-bold text-black mb-3">{{ $ekskul->judul }}</h3>
                                        <p class="text-gray-800 text-sm leading-relaxed line-clamp-3">{{ $ekskul->isi }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

          {{-- 4. SECTION TENAGA PENGAJAR --}}
<section id="tenaga-pengajar" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-black">Tenaga Pengajar</h2>
        </div>

        @if(isset($kontenTenagaPengajar) && $kontenTenagaPengajar->count() > 0)
            <div class="relative px-4 md:px-12" data-aos="zoom-in" data-aos-duration="1000">
                <div class="swiper swiperTenagaPengajar !pb-16">
                    <div class="swiper-wrapper">
                        @foreach($kontenTenagaPengajar as $guru)
                            @php 
                                $fotoUrl = $guru->media->first() ? asset('storage/' . $guru->media->first()->file_path) : asset('storage/default-avatar.png'); 
                            @endphp
                            <div class="swiper-slide">
                                <div class="bg-[#F5E9DA] rounded-2xl p-5 flex items-center gap-6 h-44 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    
                                    {{-- FOTO: Sekarang tanpa background putih dan padding --}}
                                    <div class="w-24 h-32 rounded-xl overflow-hidden flex-shrink-0 shadow-md">
                                        <img src="{{ $fotoUrl }}" alt="{{ $guru->judul }}" class="w-full h-full object-cover">
                                    </div>

                                    <div class="flex flex-col">
                                        <h3 class="text-lg font-bold text-black leading-tight">{{ $guru->judul }}</h3>
                                        <p class="text-gray-700 text-sm mt-2 font-medium">{{ $guru->isi }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-button-prev !w-10 !h-10 !bg-[#002060]/10 hover:!bg-[#002060] !text-white !rounded-full after:!text-sm !left-0"></div>
                <div class="swiper-button-next !w-10 !h-10 !bg-[#002060] !text-white !rounded-full after:!text-sm !right-0"></div>
            </div>
        @endif
    </div>
</section>
            {{-- SECTION PPDB --}}
            @if($kontenPPDB)
            <section id="ppdb" class="py-20 bg-white" data-aos="fade-up" data-aos-duration="1000">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-black mb-2">{!! nl2br(e($kontenPPDB->judul)) !!}</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div class="flex flex-col items-start" data-aos="fade-right" data-aos-delay="300">
                            <h4 class="text-2xl md:text-3xl font-bold text-[#002060] mb-6">Ayo bergabung dengan kami!</h4>
                            <a href="/pendaftaran" class="px-8 py-3 text-white bg-[#002060] rounded-xl hover:bg-blue-900 shadow-lg transform transition hover:scale-110">Daftar Sekarang</a>
                        </div>
                        <div class="bg-white p-8 rounded-xl shadow-xl border border-gray-100" data-aos="fade-left" data-aos-delay="500">
                            <h5 class="text-xl font-bold text-black mb-4">Syarat Pendaftaran :</h5>
                            <div class="text-gray-800 leading-relaxed whitespace-pre-line">{!! nl2br(e($kontenPPDB->isi)) !!}</div>
                        </div>
                    </div>
                </div>
            </section>
            @endif
        </div>

        {{-- 3. Footer (Full Width) --}}
        @include('partials.footer')

        {{-- Scripts --}}
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({ once: true });

            var swiperGuru = new Swiper(".swiperTenagaPengajar", {
                slidesPerView: 1,
                grid: { rows: 2, fill: "row" },
                spaceBetween: 30,
                pagination: { el: ".swiper-pagination", clickable: true },
                navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
                breakpoints: {
                    768: { slidesPerView: 2, grid: { rows: 2 } },
                    1024: { slidesPerView: 3, grid: { rows: 2 } }
                }
            });

            function moveSlide(sliderId, direction) {
                const container = document.getElementById(sliderId);
                let current = parseInt(container.getAttribute('data-current'));
                const total = parseInt(container.getAttribute('data-total'));
                const slides = container.querySelectorAll('.slide-item');
                let next = (current + direction + total) % total;
                slides[current].classList.replace('opacity-100', 'opacity-0');
                slides[current].classList.replace('z-10', 'z-0');
                slides[next].classList.replace('opacity-0', 'opacity-100');
                slides[next].classList.replace('z-0', 'z-10');
                container.setAttribute('data-current', next);
            }
        </script>
    </body>
</html>