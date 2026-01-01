@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <style>
        @media (max-width: 768px) {
            main {
                    padding: 1rem !important;
                }

                /* 2. Header Halaman */
                h1.text-3xl {
                    font-size: 1.5rem !important;
                }
            }
    </style>
    <div class="p-4 md:p-6 space-y-6 font-poppins bg-gray-50">
        <div class="max-w-7xl mx-auto">
            
            {{-- Header: Menggunakan komponen yang sama dengan Kelola Pendaftaran --}}
            <x-pageheadersatu title="Dashboard Admin" description="Reporting Pembayaran & Pendaftaran secara real-time" />

            {{-- Toolbar: Meniru gaya 'Daftar Pendaftar' di Kelola Pendaftaran --}}
            <div class="mb-6 mt-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-xl font-semibold text-black">Ringkasan Statistik</h2>
                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest italic">
                    Periode {{ date('Y') }}/{{ date('Y') + 1 }}
                </p>
            </div>

            {{-- SECTION 1: PENDAFTARAN --}}
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 md:p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-primaryblue uppercase tracking-tight">Data Pendaftaran</h3>
                    @auth('admin')
                        <a href="{{ route('admin.pendaftaran.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-primaryblue transition bg-gray-50 px-3 py-1 rounded-full border border-gray-200">Detail ></a>
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
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 flex items-center gap-4 transition-all hover:shadow-md">
                        <img src="{{ asset('icons/' . $stat['icon']) }}" class="w-10 h-10 object-contain">
                        <div>
                            <p class="text-lg font-black {{ $stat['color'] }} leading-none">{{ $stat['count'] ?? 0 }}</p>
                            <p class="text-[9px] font-bold uppercase text-gray-400 mt-1 tracking-wider">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- SECTION 2: PEMBAYARAN --}}
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 md:p-7 flex flex-col min-h-0">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-lg font-bold text-primaryblue uppercase tracking-tight">Data Pembayaran</h3>
                    <a href="{{ route('admin.pembayaran.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-primaryblue transition bg-gray-50 px-3 py-1 rounded-full border border-gray-200">Detail ></a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    @php
                        $pembayaranStats = [
                            ['label' => 'Total Siswa', 'count' => $totalPendaftaran, 'icon' => 'people.svg', 'color' => 'text-black'],
                            ['label' => 'Lunas', 'count' => $pembayaranLunas, 'icon' => 'acc.svg', 'color' => 'text-emerald-600'],
                            ['label' => 'Belum Lunas', 'count' => $pembayaranBelumLunas, 'icon' => 'decline.svg', 'color' => 'text-rose-600'],
                            ['label' => 'Menunggu', 'count' => $pembayaranPending, 'icon' => 'pending.svg', 'color' => 'text-amber-500'],
                        ];
                    @endphp

                    @foreach($pembayaranStats as $pay)
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 transition-all hover:scale-[1.02]">
                        <img src="{{ asset('icons/' . $pay['icon']) }}" class="w-12 h-12 object-contain">
                        <div>
                            <p class="text-xl font-black {{ $pay['color'] }} leading-none">{{ $pay['count'] ?? 0 }}</p>
                            <p class="text-[9px] font-bold uppercase text-gray-400 mt-2 tracking-wider">{{ $pay['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Footer Info --}}
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mt-auto">
                    <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 flex items-center gap-5 md:min-w-[260px]">
                        <img src="{{ asset('icons/decline.svg') }}" class="w-12 h-12 object-contain">
                        <div>
                            <p class="text-xl font-black text-rose-600 leading-none">{{ $pembayaranBelumBayar ?? 0 }}</p>
                            <p class="text-[9px] font-bold uppercase text-gray-400 mt-2 tracking-wider">Belum Bayar</p>
                        </div>
                    </div>

                    <div class="bg-primaryblue p-6 rounded-2xl shadow-lg shadow-primaryblue/20 flex items-center justify-between lg:min-w-[400px]">
                        <div class="flex items-center gap-5">
                            <div class="bg-white/10 p-4 rounded-xl text-white shadow-inner">
                                <i class="fas fa-wallet text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-white/60 uppercase tracking-[0.2em] mb-1">Total Dana Masuk</p>
                                <p class="text-2xl font-black text-white leading-tight">
                                    Rp {{ number_format($totalUangMasuk ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Spacer bawah mobile --}}
            <div class="h-10 md:hidden"></div>
        </div>
    </div>
@endsection