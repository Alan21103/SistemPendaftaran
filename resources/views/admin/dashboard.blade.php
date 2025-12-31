@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    {{-- H-screen dan overflow-hidden agar tetap satu halaman --}}
    <div class="flex h-screen bg-gray-50 font-poppins overflow-hidden">
        <div class="h-full">
            <x-sidebar />
        </div>

        <main class="w-full h-full flex flex-col p-4 lg:p-6">
           {{-- Tombol Buka Sidebar (Mobile Only) --}}
            <div class="flex items-center lg:hidden mb-4">
                <button @click="$dispatch('open-sidebar')" class="p-3 bg-white rounded-xl shadow-sm border border-gray-100 text-primaryblue focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="ml-4 font-bold text-primaryblue uppercase tracking-wider text-sm">Menu Dashboard</span>
            </div>

            {{-- Header Halaman --}}
            <div class="mb-5 flex-none">
                <x-pageheaderdua title="Kelola Pendaftaran" description="Kelola persetujuan pendaftaran siswa baru" />
            </div>

            {{-- SECTION 1: PENDAFTARAN (Ukuran Menyesuaikan Elemen / Fit) --}}
            <section class="bg-white rounded-[2rem] p-6 shadow-xl shadow-gray-200/50 border border-gray-100 mb-5 flex-none">
                <div class="flex justify-between items-center mb-6 px-2">
                    <div>
                        <h2 class="text-xl font-black text-primaryblue tracking-tight uppercase">Data Pendaftaran</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Periode {{ date('Y') }}/{{ date('Y') + 1 }}</p>
                    </div>
                    @auth('admin')
                        <a href="{{ route('admin.pendaftaran.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-primaryblue transition shadow-sm bg-gray-50 px-3 py-1 rounded-full">Detail ></a>
                    @endauth
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $pendaftaranStats = [
                            ['label' => 'Total Pendaftar', 'count' => $totalPendaftaran, 'icon' => 'people.svg', 'color' => 'text-black'],
                            ['label' => 'Diterima', 'count' => $diterimaCount, 'icon' => 'acc.svg', 'color' => 'text-emerald-600'],
                            ['label' => 'Ditolak', 'count' => $ditolakCount, 'icon' => 'decline.svg', 'color' => 'text-rose-600'],
                            ['label' => 'Pending', 'count' => $pendingCount, 'icon' => 'pending.svg', 'color' => 'text-amber-500'],
                        ];
                    @endphp

                    @foreach($pendaftaranStats as $stat)
                    <div class="bg-white p-4 rounded-[1.5rem] border border-gray-100 shadow-sm transition-all duration-300 hover:scale-105 flex items-center gap-4">
                        <img src="{{ asset('icons/' . $stat['icon']) }}" class="w-12 h-12 object-contain">
                        <div>
                            <p class="text-xl font-black {{ $stat['color'] }} leading-none">{{ $stat['count'] ?? 0 }}</p>
                            <p class="text-[9px] font-bold uppercase text-gray-400 mt-1 tracking-wider">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- SECTION 2: PEMBAYARAN (Dibuat Flex-1 agar mengisi sisa ruang) --}}
            <section class="flex-1 bg-white rounded-[2rem] p-7 shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col justify-center min-h-0">
                <div class="flex justify-between items-center mb-8 px-2 flex-none">
                    <div>
                        <h2 class="text-2xl font-black text-primaryblue tracking-tight uppercase">Data Pembayaran</h2>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic">Laporan Keuangan Real-time</p>
                    </div>
                    <a href="{{ route('admin.pembayaran.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-primaryblue transition shadow-sm bg-gray-50 px-3 py-1 rounded-full">Detail ></a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8 flex-none">
                    @php
                        $pembayaranStats = [
                            ['label' => 'Total Siswa', 'count' => $totalPendaftaran, 'icon' => 'people.svg', 'color' => 'text-black'],
                            ['label' => 'Lunas', 'count' => $pembayaranLunas, 'icon' => 'acc.svg', 'color' => 'text-emerald-600'],
                            ['label' => 'Belum Lunas', 'count' => $pembayaranBelumLunas, 'icon' => 'decline.svg', 'color' => 'text-rose-600'],
                            ['label' => 'Menunggu', 'count' => $pembayaranPending, 'icon' => 'pending.svg', 'color' => 'text-amber-500'],
                        ];
                    @endphp

                    @foreach($pembayaranStats as $pay)
                    <div class="bg-gray-50 p-5 rounded-[1.8rem] border border-gray-100 shadow-sm transition-all duration-300 hover:scale-105 flex items-center gap-5">
                        <img src="{{ asset('icons/' . $pay['icon']) }}" class="w-14 h-14 object-contain">
                        <div>
                            <p class="text-2xl font-black {{ $pay['color'] }} leading-none">{{ $pay['count'] ?? 0 }}</p>
                            <p class="text-[10px] font-bold uppercase text-gray-400 mt-2 tracking-wider">{{ $pay['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 px-2 flex-none mt-auto">
                    <div class="bg-gray-50 p-5 rounded-[1.8rem] border border-gray-100 shadow-sm transition-all duration-300 hover:scale-105 flex items-center gap-5 min-w-[240px]">
                        <img src="{{ asset('icons/decline.svg') }}" class="w-14 h-14 object-contain">
                        <div>
                            <p class="text-2xl font-black text-rose-600 leading-none">{{ $pembayaranBelumBayar ?? 0 }}</p>
                            <p class="text-[10px] font-bold uppercase text-gray-400 mt-2 tracking-wider">Belum Bayar</p>
                        </div>
                    </div>

                    <div class="bg-primaryblue p-6 rounded-[2rem] shadow-2xl shadow-primaryblue/30 transition-all duration-500 hover:scale-105 flex items-center justify-between min-w-[380px]">
                        <div class="flex items-center gap-5">
                            <div class="bg-white/10 p-4 rounded-2xl text-white shadow-inner">
                                <i class="fas fa-wallet text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-white/60 uppercase tracking-[0.2em] mb-1">Total Dana Masuk</p>
                                <p class="text-2xl font-black text-white leading-tight tracking-tight">
                                    Rp {{ number_format($totalUangMasuk ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection