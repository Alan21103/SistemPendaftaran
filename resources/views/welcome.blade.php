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
            html { scroll-behavior: smooth; }
            section[id], main[id] { scroll-margin-top: 80px; }
            
            .nav-link {
                display: inline-block;
                transition: all 0.3s ease;
            }

            .swiper-pagination-bullet-active {
                background: #002060 !important;
            }

            /* Efek Hover untuk kartu */
            .floating-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .floating-card:hover {
                transform: translateY(-10px);
            }

            /* Style tombol slider manual ekstrakurikuler */
            .slider-btn {
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .group:hover .slider-btn {
                opacity: 1;
            }
        </style>
    </head>
    <body class="antialiased font-sans bg-white overflow-x-hidden">
        
        {{-- 1. Navbar --}}
        @include('partials.navbar')

        {{-- 2. SECTION BERANDA (HERO WITH GLASS EFFECT CARDS) --}}
        @if ($kontenBeranda)
            @php
                $media_utama = $kontenBeranda->media->where('urutan', 0)->first();
                $image_url = $media_utama ? asset('storage/' . $media_utama->file_path) : asset('storage/default.png');
                
                $deskripsiUtama = $kontenTentangSekolah->first(fn($i) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($i->judul), ['sejarah', 'tentang sekolah']));
                $visiData = $kontenTentangSekolah->first(fn($i) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($i->judul), 'visi'));
                $misiData = $kontenTentangSekolah->first(fn($i) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($i->judul), 'misi'));
            @endphp

            <main id="beranda" class="relative min-h-screen flex flex-col justify-center pb-24 md:pb-32">
                {{-- Background Image --}}
                <div class="absolute inset-0 z-0">
                    <img src="{{ $image_url }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/60"></div> 
                </div>

                <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-8 pt-24 mb-16">
                    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-start gap-8">
                        <div class="text-white lg:w-2/3" data-aos="fade-right" data-aos-duration="1000">
                            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4 text-white">
                                {!! nl2br(e($kontenBeranda->judul)) !!}
                            </h1>
                            <p class="text-base md:text-lg font-light max-w-2xl opacity-90 text-gray-100">
                                {!! nl2br(e($kontenBeranda->isi)) !!}
                            </p>
                        </div>
                        
                        <div class="lg:w-1/3 flex lg:justify-end" data-aos="fade-left" data-aos-duration="1000">
                            <a href="#ppdb" class="bg-white text-[#002060] px-8 py-3 rounded-full font-bold text-lg hover:bg-blue-50 transition-all shadow-xl">
                                Informasi PPDB
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Grid Layout Kartu dengan Efek Glassmorphism --}}
                <div class="relative z-20 w-full max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                      {{-- Card Tentang Sekolah --}}
<div class="md:row-span-2 bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl floating-card flex flex-col justify-center" data-aos="fade-up" data-aos-delay="200">
    <h3 class="text-2xl font-bold text-white mb-2 text-center">Tentang Sekolah</h3>
    <p class="text-white/90 text-base leading-relaxed text-center italic">
        "{{ $deskripsiUtama ? $deskripsiUtama->isi : 'Informasi sekolah belum tersedia.' }}"
    </p>
</div>

                        {{-- Card Visi --}}
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl floating-card" data-aos="fade-up" data-aos-delay="400">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-xl font-bold text-white">Visi</h3>
                            </div>
                            <p class="text-white/80 text-sm leading-relaxed">
                                {{ $visiData ? $visiData->isi : 'Visi belum diatur.' }}
                            </p>
                        </div>

                        {{-- Card Misi --}}
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl floating-card" data-aos="fade-up" data-aos-delay="600">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-xl font-bold text-white">Misi</h3>
                            </div>
                            <p class="text-white/80 text-sm leading-relaxed">
                                {{ $misiData ? $misiData->isi : 'Misi belum diatur.' }}
                            </p>
                        </div>

                    </div>
                </div>
            </main>
        @endif

        {{-- 3. SECTION EKSTRAKURIKULER --}}
        <section id="ekstrakurikuler" class="py-20 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
    <h2 class="text-3xl md:text-4xl font-extrabold text-black">Ekstrakurikuler</h2>
    {{-- Teks deskripsi dipaksa satu baris --}}
    <p class="text-gray-500 mt-4 text-sm md:text-lg whitespace-nowrap">
        Wadah pengembangan minat, bakat, dan karakter siswa melalui berbagai kegiatan nonakademik.
    </p>
</div>
                
                @if(isset($kontenEkstrakurikuler) && $kontenEkstrakurikuler->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($kontenEkstrakurikuler as $index => $ekskul)
                            <div class="group rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 bg-white h-full flex flex-col" 
                                 data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                
                                {{-- Area Gambar & Navigasi Slider --}}
                                <div class="relative h-64 w-full bg-gray-100 overflow-hidden" id="slider-{{ $ekskul->id }}" data-current="0" data-total="{{ $ekskul->media->count() }}">
                                    @foreach($ekskul->media as $mIndex => $media)
                                        <div class="slide-item absolute inset-0 w-full h-full transition-opacity duration-500 {{ $mIndex === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                                            <img src="{{ asset('storage/' . $media->file_path) }}" class="w-full h-full object-cover" alt="{{ $ekskul->judul }}">
                                        </div>
                                    @endforeach

                                    @if($ekskul->media->count() > 1)
                                        <button onclick="moveSlide('slider-{{ $ekskul->id }}', -1)" class="slider-btn absolute left-2 top-1/2 -translate-y-1/2 z-20 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
                                        </button>
                                        <button onclick="moveSlide('slider-{{ $ekskul->id }}', 1)" class="slider-btn absolute right-2 top-1/2 -translate-y-1/2 z-20 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                        
                                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-1.5">
                                            @foreach($ekskul->media as $mIndex => $media)
                                                <div class="dot-{{ $ekskul->id }} w-2 h-2 rounded-full {{ $mIndex === 0 ? 'bg-white' : 'bg-white/40' }}"></div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="p-6 text-center flex-1 flex flex-col justify-center bg-white">
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
<section id="tenaga-pengajar" class="py-20 bg-white"> {{-- Ganti bg-white ke gray-50 sedikit agar shadow putih lebih kontras --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-black">Tenaga Pengajar</h2>
            <p class="text-gray-500 mt-4 text-sm md:text-lg">
                Tenaga pendidik profesional yang berdedikasi dalam membimbing serta mengembangkan potensi siswa.
            </p>
        </div>

        @if(isset($kontenTenagaPengajar) && $kontenTenagaPengajar->count() > 0)
            <div class="relative px-4 md:px-12" data-aos="zoom-in">
                <div class="swiper swiperTenagaPengajar !pb-20">
                    <div class="swiper-wrapper">
                        @foreach($kontenTenagaPengajar as $guru)
                            @php 
                                $fotoUrl = $guru->media->first() ? asset('storage/' . $guru->media->first()->file_path) : asset('storage/default-avatar.png'); 
                            @endphp
                            <div class="swiper-slide p-4"> {{-- Tambah padding p-4 agar shadow tidak terpotong saat hover --}}
                                <div class="bg-white rounded-[2rem] p-6 flex items-center gap-8 
                                            shadow-[0_15px_30px_-5px_rgba(0,0,0,0.15)] 
                                            border border-gray-100 
                                            transition-all duration-300 ease-in-out 
                                            cursor-pointer group">
                                    
                                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-[1.5rem] overflow-hidden flex-shrink-0 bg-gray-100 shadow-inner transition-transform duration-300">
                                        <img src="{{ $fotoUrl }}" alt="{{ $guru->judul }}" class="w-full h-full object-cover">
                                    </div>

                                    <div class="flex flex-col">
                                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 tracking-tight transition-colors">
                                            {{ $guru->judul }}
                                        </h3>
                                        <p class="text-gray-500 text-base md:text-lg mt-1 font-normal">
                                            {{ $guru->isi }}
                                        </p>
                                    </div>
                                    
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-button-prev !w-12 !h-12 !bg-white !text-blue-900 !rounded-full shadow-lg after:!text-lg !left-0 border border-gray-100 hover:bg-blue-900 hover:text-white transition-all"></div>
                <div class="swiper-button-next !w-12 !h-12 !bg-white !text-blue-900 !rounded-full shadow-lg after:!text-lg !right-0 border border-gray-100 hover:bg-blue-900 hover:text-white transition-all"></div>
            </div>
        @endif
    </div>
</section>

        {{-- 5. SECTION PPDB --}}
        @if($kontenPPDB)
        <section id="ppdb" class="py-20 bg-white" data-aos="fade-up">
            <div class="max-w-6xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-black mb-2">{!! nl2br(e($kontenPPDB->judul)) !!}</h2>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <div class="flex flex-col items-start" data-aos="fade-right">
                        <h4 class="text-2xl md:text-3xl font-bold text-[#002060] mb-6">Ayo bergabung dengan kami!</h4>
                        <a href="/pendaftaran" class="px-10 py-4 text-white bg-[#002060] rounded-xl hover:bg-blue-900 shadow-lg transform transition hover:scale-105">Daftar Sekarang</a>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-xl shadow-xl border border-gray-100" data-aos="fade-left">
                        <h5 class="text-xl font-bold text-black mb-4">Syarat Pendaftaran :</h5>
                        <div class="text-gray-800 leading-relaxed whitespace-pre-line">{!! nl2br(e($kontenPPDB->isi)) !!}</div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- 6. Footer --}}
        @include('partials.footer')

        {{-- Scripts --}}
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            // Initialize AOS
            AOS.init({ once: true, duration: 800 });

            // Initialize Swiper Tenaga Pengajar
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

            // Logic Slider Ekstrakurikuler Manual
            function moveSlide(sliderId, direction) {
                const container = document.getElementById(sliderId);
                let current = parseInt(container.getAttribute('data-current'));
                const total = parseInt(container.getAttribute('data-total'));
                const slides = container.querySelectorAll('.slide-item');
                const dots = container.querySelectorAll('.dot-' + sliderId.split('-')[1]);
                
                if(total <= 1) return;

                let next = (current + direction + total) % total;

                // Transisi Fade Gambar
                slides[current].classList.replace('opacity-100', 'opacity-0');
                slides[current].classList.replace('z-10', 'z-0');
                slides[next].classList.replace('opacity-0', 'opacity-100');
                slides[next].classList.replace('z-0', 'z-10');

                // Update Titik Indikator
                if(dots.length > 0) {
                    dots[current].classList.replace('bg-white', 'bg-white/40');
                    dots[next].classList.replace('bg-white/40', 'bg-white');
                }

                container.setAttribute('data-current', next);
            }
        </script>
    </body>
</html>