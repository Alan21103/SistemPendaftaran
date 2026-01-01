@extends('admin.layouts.app')

@section('title', 'Kelola Pembayaran')

@section('content')

    {{-- LOAD ASSETS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
       @media (max-width: 768px) {
            /* 1. Container & Padding */
            main.p-6 { padding: 1rem !important; }
            
            /* 2. Toolbar & Export Button */
            .mb-6.flex.flex-col { gap: 1rem !important; }

            /* 3. Filter Bar Mobile */
            #filterForm { flex-direction: column; align-items: stretch; }
            .flex-1.min-w-\[200px\] { max-width: none !important; }
            .custom-select-container { min-width: 0 !important; width: 100%; }
            .h-\[46px\] { height: auto !important; padding: 10px 0; flex-direction: column; align-items: flex-start; gap: 10px; }
            .h-5.w-\[1px\] { display: none; } /* Hilangkan garis pemisah date di mobile */

            /* 4. Table to Cards Transformation */
            thead { display: none; } /* Sembunyikan header tabel */
            table, tbody, tr, td { display: block; width: 100%; }
            
            tr {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                margin-bottom: 1rem;
                padding: 1rem;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            td {
                padding: 0.5rem 0 !important;
                border: none !important;
                white-space: normal !important;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            /* Tambahkan label untuk setiap baris di mobile */
            td:before {
                content: attr(data-label);
                font-size: 10px;
                font-weight: 800;
                text-transform: uppercase;
                color: #9ca3af;
                letter-spacing: 0.05em;
            }

            td.text-center { justify-content: center; margin-top: 0.5rem; border-top: 1px dashed #eee !important; padding-top: 1rem !important; }
            td:last-child:before { display: none; } /* Hilangkan label untuk tombol aksi */
            
            /* Modal Verifikasi Responsive */
            .relative.bg-white.max-w-4xl { padding: 1.5rem !important; border-radius: 20px !important; }
            .grid-cols-1.md\:grid-cols-2 { gap: 1.5rem !important; }

            input[type="date"] {
                width: 100%;
                -webkit-appearance: none;
                display: block;
            }
            
            /* Menghilangkan padding berlebih form filter di mobile */
            #filterForm {
                display: flex;
                flex-direction: column;
                width: 100%;
            }

            /* Menyesuaikan urutan toolbar jika diperlukan */
            .flex-wrap {
                flex-direction: column;
                align-items: stretch !important;
            }
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Styling for Flatpickr */
        .flatpickr-calendar {
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        /* Styling Dropdown Custom */
        .custom-select-container { position: relative; min-width: 160px; cursor: pointer; }
        .custom-select-trigger {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.6rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem;
            background: white; font-size: 0.875rem; transition: all 0.2s;
        }
        .custom-select-trigger:hover { border-color: #3b82f6; }
        .custom-select-options {
            position: absolute; top: 110%; left: 0; right: 0; background: white;
            border: 1px solid #d1d5db; border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); z-index: 50;
            display: none; max-height: 200px; overflow-y: auto;
        }
        .custom-select-container.active .custom-select-options { display: block; }
        .custom-select-option { padding: 0.6rem 1rem; font-size: 0.875rem; transition: background 0.2s; }
        .custom-select-option:hover { background-color: #f3f4f6; }
        .custom-select-option.selected { background-color: #eff6ff; color: #2563eb; font-weight: 600; }
        .arrow { transition: transform 0.2s; font-size: 0.75rem; color: #9ca3af; }
        .custom-select-container.active .arrow { transform: rotate(180deg); }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    </style>

    <div class="flex min-h-screen bg-white" x-data="{ openModal: false, selectedData: { riwayat: [] } }">
       

        <main class="w-full overflow-y-auto p-6">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <div class="max-w-7xl mx-auto">
                <x-pageheadersatu title="Kelola Pembayaran"
                    description="Verifikasi bukti transfer dan pantau tagihan siswa di sini!" />

                {{-- Toolbar --}}
                <div class="mb-6 flex flex-col gap-3 items-start">
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight">Daftar Pembayaran</h2>
                    <a href="{{ route('admin.export.pembayaran') }}"
                        class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-xl shadow-sm transition-all active:scale-95">
                        <img src="{{ asset('icons/export.svg') }}" alt="Export" class="h-5 w-5">
                        Export Excel
                    </a>
                </div>

                {{-- Filter Bar --}}
                <div class="mb-8">
                    <form id="filterForm" action="{{ route('admin.pembayaran.index') }}" method="GET"
                        class="flex flex-wrap items-center gap-4">

                        <div class="flex-1 min-w-[280px] relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" id="searchInput"
                                placeholder="Cari siswa, NISN, atau sekolah..."
                                value="{{ request('search') }}" oninput="doSearch()"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 text-sm shadow-sm transition-all">
                        </div>

                        <div class="flex items-center bg-white border border-gray-300 rounded-xl px-4 shadow-sm h-[46px]">
                            <div class="flex items-center gap-3">
                                <i class="far fa-calendar text-gray-400"></i>
                                <div class="flex flex-col">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase leading-none mb-0.5 tracking-wider">Dari</label>
                                    <input type="text" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                        placeholder="Mulai"
                                        class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-gray-700 w-24 outline-none">
                                </div>
                            </div>
                            <div class="h-6 w-[1px] bg-gray-200 mx-4"></div>
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase leading-none mb-0.5 tracking-wider">Ke</label>
                                    <input type="text" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                        placeholder="Selesai"
                                        class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-gray-700 w-24 outline-none">
                                </div>
                        {{-- 2. Date Range Picker --}}
                        <div class="w-full md:w-auto flex items-center bg-white border border-gray-300 rounded-lg px-3 shadow-sm hover:border-blue-400 transition-colors h-auto md:h-[46px] py-2 md:py-0">
                            <div class="grid grid-cols-2 md:flex md:items-center w-full divide-x md:divide-x-0 divide-gray-200">
                                
                                {{-- Input Dari --}}
                                <div class="flex items-center gap-2 pr-3 md:pr-0">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Dari</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        onchange="document.getElementById('filterForm').submit()"
                                        class="w-full md:w-auto border-none p-0 text-sm focus:ring-0 cursor-pointer text-gray-600 bg-transparent outline-none min-h-[30px]">
                                </div>

                                {{-- Separator (Hanya muncul di Desktop) --}}
                                <div class="hidden md:block h-5 w-[1px] bg-gray-200 mx-3"></div>

                                {{-- Input Ke --}}
                                <div class="flex items-center gap-2 pl-3 md:pl-0">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Ke</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        onchange="document.getElementById('filterForm').submit()"
                                        class="w-full md:w-auto border-none p-0 text-sm focus:ring-0 cursor-pointer text-gray-600 bg-transparent outline-none min-h-[30px]">
                                </div>

                            </div>
                        </div>

                        {{-- Dropdown Status --}}
                        <div class="custom-select-container" id="dropdownStatus">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <div class="custom-select-trigger h-[46px]">
                                <span class="text-sm font-medium">{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <div class="custom-select-options">
                                <div class="custom-select-option" data-value="">Semua Status</div>
                                @foreach ($list_status as $status)
                                    <div class="custom-select-option" data-value="{{ $status }}">{{ ucfirst($status) }}</div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Dropdown Sekolah (DIKEMBALIKAN) --}}
                        <div class="custom-select-container" id="dropdownSekolah">
                            <input type="hidden" name="asal_sekolah" value="{{ request('asal_sekolah') }}">
                            <div class="custom-select-trigger h-[46px] min-w-[180px]">
                                <span class="truncate max-w-[140px] text-sm font-medium">{{ request('asal_sekolah') ? request('asal_sekolah') : 'Semua Sekolah' }}</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <div class="custom-select-options custom-scrollbar">
                                <div class="custom-select-option" data-value="">Semua Sekolah</div>
                                @foreach ($list_sekolah as $sekolah)
                                    <div class="custom-select-option" data-value="{{ $sekolah }}">{{ $sekolah }}</div>
                                @endforeach
                            </div>
                        </div>

                        @if(request()->anyFilled(['search', 'status', 'asal_sekolah', 'start_date', 'end_date']))
                            <a href="{{ route('admin.pembayaran.index') }}"
                                class="p-2.5 text-gray-400 hover:text-rose-500 transition-colors" title="Reset Filter">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Table Section --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Table --}}
                <div class="bg-white md:bg-white rounded-2xl md:shadow-sm md:border md:border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NISN / No. Telp</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Daftar</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sudah Dibayar</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Tagihan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nama Lengkap</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">NISN / No. Telp</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tgl Daftar</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Total Tagihan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Sudah Dibayar</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Sisa Tagihan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent md:bg-white divide-y divide-gray-100">
                                @forelse ($datas as $d)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $d->pendaftaran->nama_siswa }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="font-medium text-gray-900">{{ $d->pendaftaran->nisn ?? '-' }}</div>
                                            <div class="text-xs text-gray-400">{{ $d->pendaftaran->no_telp ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $d->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">Rp {{ number_format($d->total_tagihan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">Rp {{ number_format($d->total_tagihan - $d->sisa_tagihan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $d->sisa_tagihan > 0 ? 'text-rose-600' : 'text-gray-400' }}">Rp {{ number_format($d->sisa_tagihan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $d->status_pembayaran == 'lunas' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                                                {{ $d->status_pembayaran }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php $pembayaranPending = $d->pembayaran->where('status_konfirmasi', 'Menunggu Verifikasi')->first(); @endphp

                                            @if($pembayaranPending)
                                                <button @click="$dispatch('open-verifikasi', { 
                                                        id: '{{ $pembayaranPending->id }}',
                                                        nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                        total_tagihan_full: '{{ number_format($d->total_tagihan, 0, ',', '.') }}',
                                                        nominal_input: '{{ number_format($pembayaranPending->nominal_bayar, 0, ',', '.') }}',
                                                        sisa_akhir: '{{ number_format($d->sisa_tagihan, 0, ',', '.') }}',
                                                        bukti: '{{ route('admin.pembayaran.view-bukti', $pembayaranPending->id) }}',
                                                        tanggal: '{{ $pembayaranPending->created_at->format('d/m/Y H:i') }}'
                                                    })"
                                                    class="px-4 py-1.5 bg-amber-500 text-white rounded-xl text-xs font-bold hover:bg-amber-600 transition shadow-sm animate-pulse">
                                                    Verifikasi
                                                </button>
                                            @else
                                                <button @click="openModal = true; selectedData = { 
                                                        nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                        nisn: '{{ $d->pendaftaran->nisn ?? '-' }}',
                                                        total: '{{ number_format($d->total_tagihan, 0, ',', '.') }}',
                                                        terbayar: '{{ number_format($d->total_tagihan - $d->sisa_tagihan, 0, ',', '.') }}',
                                                        sisa: '{{ number_format($d->sisa_tagihan, 0, ',', '.') }}'
                                                    }"
                                                    class="px-4 py-1.5 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-xl text-xs font-bold hover:bg-indigo-600 hover:text-white transition">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td data-label="Nama Lengkap" class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $d->pendaftaran->nama_siswa }}
                                        </td>
                                        <td data-label="Identitas" class="px-6 py-4 text-sm text-gray-600">
                                            <div class="text-right md:text-left">
                                                <div class="font-medium text-gray-900">{{ $d->pendaftaran->nisn ?? '-' }}</div>
                                                <div class="text-xs text-gray-500">{{ $d->pendaftaran->no_telp ?? '-' }}</div>
                                            </div>
                                        </td>
                                        <td data-label="Tgl Daftar" class="px-6 py-4 text-sm text-gray-600">
                                            {{ $d->created_at->format('d/m/Y') }}
                                        </td>
                                        <td data-label="Total Tagihan" class="px-6 py-4 text-sm font-semibold text-gray-700">
                                            Rp {{ number_format($d->total_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td data-label="Sudah Dibayar" class="px-6 py-4 text-sm font-bold text-emerald-600">
                                            Rp {{ number_format($d->total_tagihan - $d->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td data-label="Sisa Tagihan" class="px-6 py-4 text-sm font-bold {{ $d->sisa_tagihan > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                            Rp {{ number_format($d->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td data-label="Status Pembayaran" class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $d->status_pembayaran == 'lunas' ? 'bg-emerald-100 text-emerald-700 border-emerald-500' : 'bg-yellow-100 text-yellow-700 border-yellow-500' }}">
                                                {{ $d->status_pembayaran }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $pembayaranPending = $d->pembayaran->where('status_konfirmasi', 'Menunggu Verifikasi')->first();
                                            @endphp

                                            @if($pembayaranPending)
                                                <button @click="$dispatch('open-verifikasi', { 
                                                    id: '{{ $pembayaranPending->id }}',
                                                    nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                    total_tagihan: '{{ number_format($d->total_tagihan, 0, ',', '.') }}',
                                                    sisa_awal: '{{ number_format($d->sisa_tagihan + $pembayaranPending->nominal_bayar, 0, ',', '.') }}',
                                                    nominal_input: '{{ number_format($pembayaranPending->nominal_bayar, 0, ',', '.') }}',
                                                    sisa_akhir: '{{ number_format($d->sisa_tagihan, 0, ',', '.') }}',
                                                    bukti: '{{ route('admin.pembayaran.view-bukti', $pembayaranPending->id) }}',
                                                    tanggal: '{{ $pembayaranPending->created_at->format('d/m/Y H:i') }}'
                                                })" class="w-full md:w-auto px-4 py-1.5 bg-amber-500 text-white rounded-lg text-xs font-bold hover:bg-amber-600 transition shadow-sm animate-pulse">
                                                    <i class="fas fa-clipboard-check mr-1"></i> Verifikasi
                                                </button>
                                            @else
                                                <button @click="openModal = true; selectedData = { ... }"
                                                    class="w-full md:w-auto px-4 py-1.5 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-600 hover:text-white transition">
                                                    Lihat Detail
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">Data tidak ditemukan.</td></tr>
                                @endforelse
                                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        {{-- MODAL VERIFIKASI --}}
        <div x-data="{ openVerif: false, verifData: { tanggal: '' }, showFullImage: false }"
            @open-verifikasi.window="openVerif = true; verifData = $event.detail" 
            x-show="openVerif" x-cloak
            class="fixed inset-0 z-[1000] overflow-y-auto flex items-center justify-center p-4">
            
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openVerif = false"></div>
            
            <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl p-8 overflow-hidden transform transition-all">
                <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Verifikasi Pembayaran</h2>

                <div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] p-5 mb-8 flex justify-between items-center border border-gray-50">
                    <div class="text-center flex-1 border-r border-gray-100"><p class="text-xs text-gray-500 mb-1">Nama Siswa</p><p class="text-base font-bold text-gray-900" x-text="verifData.nama"></p></div>
                    <div class="text-center flex-1 border-r border-gray-100"><p class="text-xs text-gray-500 mb-1">Total Tagihan</p><p class="text-base font-bold text-gray-900">Rp. <span x-text="verifData.total_tagihan_full"></span></p></div>
                    <div class="text-center flex-1"><p class="text-xs text-gray-500 mb-1">Sudah Dibayar</p><p class="text-base font-bold text-emerald-500">Rp. <span x-text="verifData.nominal_input"></span></p></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-base font-bold text-gray-900">Bukti Transfer</label>
                            <span class="text-sm font-bold text-gray-500" x-text="verifData.tanggal"></span>
                        </div>
                        
                        <div class="aspect-square border-2 border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm flex flex-col">
                            <div class="flex-grow flex items-center justify-center bg-gray-50 cursor-zoom-in group relative" @click="showFullImage = true">
                                <img :src="verifData.bukti" class="w-full h-full object-contain p-2">
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <span class="bg-white/90 px-3 py-1 rounded-full text-[10px] font-bold shadow-sm">Klik untuk Perbesar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-5">
                        <div>
                            <label class="block text-base font-bold text-gray-900 mb-2">Nominal yang diterima</label>
                            <input type="text" readonly :value="'Rp. ' + verifData.nominal_input"
                                class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-800 bg-white text-gray-900 font-medium text-sm">
                        </div>
                        <div>
                            <label class="block text-base font-bold text-gray-900 mb-2">Sisa tagihan setelah pembayaran ini :</label>
                            <input type="text" readonly :value="'Rp. ' + verifData.sisa_akhir"
                                class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-800 bg-white text-gray-900 font-medium text-sm">
                        </div>
                        <div>
                            <div class="bg-[#f0f4f8] border-2 border-[#8ba4c0] rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-center text-gray-700 text-sm"><span>Total Tagihan</span><span class="font-bold">Rp. <span x-text="verifData.total_tagihan_full"></span></span></div>
                                <div class="flex justify-between items-center text-gray-700 text-sm"><span>Total Sudah Dibayar</span><span class="font-bold text-emerald-500">Rp. <span x-text="verifData.nominal_input"></span></span></div>
                                <hr class="border-gray-300 my-2">
                                <div class="flex justify-between items-center text-base font-bold">
                                    <span class="text-gray-900">Sisa Tagihan</span>
                                    <span class="text-red-500">Rp. <span x-text="verifData.sisa_akhir"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button @click="handleReject(verifData.id)" class="px-6 py-2 border-2 border-rose-500 text-rose-500 font-bold rounded-xl text-sm transition active:scale-95">Tolak Bukti</button>
                    <button @click="openVerif = false" class="px-6 py-2 border-2 border-[#003366] text-[#003366] font-bold rounded-xl text-sm transition active:scale-95">Batal</button>
                    <button @click="handleVerify(verifData.id)" class="px-8 py-2.5 bg-[#003366] text-white font-bold rounded-xl shadow-lg text-sm transition active:scale-95">Simpan</button>
                </div>
            </div>

            {{-- POPUP GAMBAR FULL --}}
            <div x-show="showFullImage" x-cloak 
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="fixed inset-0 z-[1100] flex items-center justify-center p-6 bg-black/90 backdrop-blur-md">
                
                <button @click="showFullImage = false" 
                    class="absolute top-6 right-6 w-12 h-12 flex items-center justify-center bg-white/20 hover:bg-white/40 text-white rounded-full transition-all shadow-xl active:scale-90 border border-white/30">
                    <i class="fas fa-times text-2xl"></i>
                </button>

                <img :src="verifData.bukti" class="max-w-full max-h-full rounded-lg shadow-2xl object-contain border border-white/10">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#start_date", { dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y", onChange: () => document.getElementById('filterForm').submit() });
            flatpickr("#end_date", { dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y", onChange: () => document.getElementById('filterForm').submit() });
            
            document.querySelectorAll('.custom-select-container').forEach(d => {
                const trigger = d.querySelector('.custom-select-trigger');
                if(trigger) {
                    trigger.onclick = (e) => { e.stopPropagation(); d.classList.toggle('active'); };
                }
                d.querySelectorAll('.custom-select-option').forEach(o => o.onclick = () => { d.querySelector('input').value = o.dataset.value; document.getElementById('filterForm').submit(); });
            });
        });

        let timeout = null;
        function doSearch() { clearTimeout(timeout); timeout = setTimeout(() => document.getElementById('filterForm').submit(), 600); }

        function handleVerify(id) {
            Swal.fire({ title: 'Simpan?', text: "Konfirmasi pembayaran ini?", icon: 'question', showCancelButton: true, confirmButtonColor: '#003366', confirmButtonText: 'Simpan' }).then(r => {
                if (r.isConfirmed) fetch(`/admin/pembayaran/verify/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, 'Content-Type': 'application/json' } }).then(() => window.location.reload());
            });
        }

        function handleReject(id) {
            Swal.fire({ title: 'Tolak?', input: 'textarea', inputPlaceholder: 'Alasan penolakan...', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48' }).then(r => {
                if (r.isConfirmed) fetch(`/admin/pembayaran/reject/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({ alasan: r.value }) }).then(() => window.location.reload());
            });
        }
    </script>
@endsection