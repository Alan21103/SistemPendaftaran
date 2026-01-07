@extends('admin.layouts.app')

@section('title', 'Kelola Pembayaran')

@section('content')

    {{-- LOAD ASSETS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }

        @media (max-width: 768px) {
            thead { display: none; }
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
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
            }
            td:before {
                content: attr(data-label);
                font-size: 10px;
                font-weight: 800;
                text-transform: uppercase;
                color: #9ca3af;
                float: left;
                text-align: left;
            }
            td.text-center { justify-content: center; border-top: 1px dashed #eee !important; padding-top: 1rem !important; }
        }

        .custom-select-container { position: relative; min-width: 160px; cursor: pointer; }
        .custom-select-trigger {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.6rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem;
            background: white; font-size: 0.875rem; transition: all 0.2s;
        }
        .custom-select-trigger:hover { border-color: #3b82f6; }
        
        .custom-select-options {
            position: absolute; top: 110%; left: 0; right: 0; background: white;
            border: 1px solid #d1d5db; border-radius: 0.75rem; z-index: 50;
            display: none; max-height: 200px; overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .custom-select-container.active .custom-select-options { display: block; }
        
        .custom-select-option { padding: 0.6rem 1rem; font-size: 0.875rem; transition: all 0.2s; }
        .custom-select-option:hover { background-color: #3b82f6; color: white; }

        .arrow { transition: transform 0.2s; font-size: 0.75rem; color: #9ca3af; }
        .custom-select-container.active .arrow { transform: rotate(180deg); }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        /* Background pagination */
        nav[role="navigation"] {
            background-color: white;
        }

        /* Tombol pagination */
        nav[role="navigation"] a,
        nav[role="navigation"] span {
            background-color: white !important;
            color: #374151; /* gray-700 */
            border-color: #e5e7eb; /* gray-200 */
        }

        /* Page aktif */
        nav[role="navigation"] span[aria-current="page"] {
            background-color: white !important;
            color: #000 !important;
            font-weight: 600;
            border-color: #000;
        }

        /* Hover */
        nav[role="navigation"] a:hover {
            background-color: #f9fafb !important;
        }
    </style>

    <div class="flex min-h-screen bg-white" x-data="{ 
        openModal: false, 
        selectedData: { nama: '', nisn: '', total: 0, sudah_bayar: 0, sisa: 0, riwayat: [] } 
    }">
        
        <main class="w-full overflow-y-auto p-6">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <div class="max-w-7xl mx-auto">
                <x-pageheadersatu title="Kelola Pembayaran" description="Verifikasi bukti transfer dan pantau tagihan siswa!" />

                <div class="mb-6 flex flex-col gap-3">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Pembayaran</h2>
                    <div>
                        <a href="{{ route('admin.export.pembayaran') }}"
                            class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium py-2 px-3 md:px-4 rounded-lg shadow-sm transition text-sm">
                            <img src="{{ asset('icons/export.svg') }}" alt="Export" class="h-4 w-4 md:h-5 md:w-5">
                            Export Excel
                        </a>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="mb-8">
                    <form id="filterForm" action="{{ route('admin.pembayaran.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[280px] relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" id="searchInput" placeholder="Cari siswa, NISN..." value="{{ request('search') }}" oninput="doSearch()"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="flex items-center bg-white border border-gray-300 rounded-xl px-4 h-[46px] shadow-sm">
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar text-gray-400"></i>
                                <div class="flex flex-col">
                                    <label class="text-[8px] font-black text-gray-400 uppercase leading-none">Dari</label>
                                    <input type="text" name="start_date" id="start_date" value="{{ request('start_date') }}" placeholder="Mulai" class="bg-transparent border-none p-0 text-xs font-bold w-20 outline-none cursor-pointer">
                                </div>
                            </div>
                            <div class="h-6 w-[1px] bg-gray-200 mx-3"></div>
                            <div class="flex items-center gap-2">
                                <div class="flex flex-col">
                                    <label class="text-[8px] font-black text-gray-400 uppercase leading-none">Ke</label>
                                    <input type="text" name="end_date" id="end_date" value="{{ request('end_date') }}" placeholder="Selesai" class="bg-transparent border-none p-0 text-xs font-bold w-20 outline-none cursor-pointer">
                                </div>
                            </div>
                        </div>

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
                    </form>
                </div>

                {{-- Table Section --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4">NISN / No. Telp</th>
                                    <th class="px-6 py-4">Tgl Daftar</th>
                                    <th class="px-6 py-4">Total Tagihan</th>
                                    <th class="px-6 py-4">Sisa Tagihan</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($datas as $d)
                                    <tr class="hover:bg-gray-50/80 transition-colors text-sm">
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $d->pendaftaran->nama_siswa }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium">{{ $d->pendaftaran->nisn ?? '-' }}</div>
                                            <div class="text-xs text-gray-400">{{ $d->pendaftaran->no_telp ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $d->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 font-semibold">Rp {{ number_format($d->total_tagihan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 font-bold {{ $d->sisa_tagihan > 0 ? 'text-rose-600' : 'text-gray-400' }}">Rp {{ number_format($d->sisa_tagihan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border 
                                                {{ strtolower($d->status_pembayaran) == 'lunas' 
                                                    ? 'bg-emerald-50 text-emerald-600 border-emerald-200' 
                                                    : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                                                {{ $d->status_pembayaran }}
                                            </span>
                                        </td>
                                       <td class="px-6 py-4">
                                        @php 
                                            $pembayaranPending = $d->pembayaran->where('status_konfirmasi', 'Menunggu Verifikasi')->first(); 
                                            $totalTerbayar = $d->pembayaran->where('status_konfirmasi', 'Diterima')->sum('nominal_bayar');
                                            $status = strtolower($d->status_pembayaran);
                                            
                                            // LOGIKA REVISI: Pastikan mengecek isi string foto_kwitansi tidak kosong
                                            $dataKwitansi = $d->pembayaran->whereNotNull('foto_kwitansi')->where('foto_kwitansi', '!=', '')->first();
                                        @endphp

                                        <div class="flex items-center justify-center gap-2">
                                            @if($pembayaranPending)
                                                <button @click="$dispatch('open-verifikasi', { 
                                                    id: '{{ $pembayaranPending->id }}',
                                                    nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                    nominal_input: '{{ number_format($pembayaranPending->nominal_bayar, 0, ',', '.') }}',
                                                    bukti: '{{ route('admin.pembayaran.view-bukti', $pembayaranPending->id) }}'
                                                })" class="px-2 py-1 bg-amber-500 text-white rounded-md text-[10px] font-bold hover:bg-amber-600 transition animate-pulse whitespace-nowrap">
                                                    Verifikasi
                                                </button>
                                            @else
                                                <button @click="openModal = true; selectedData = { 
                                                    nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}', 
                                                    nisn: '{{ $d->pendaftaran->nisn }}', 
                                                    total: '{{ number_format($d->total_tagihan, 0, ',', '.') }}', 
                                                    sudah_bayar: '{{ number_format($totalTerbayar, 0, ',', '.') }}',
                                                    sisa: '{{ number_format($d->sisa_tagihan, 0, ',', '.') }}',
                                                    riwayat: {{ json_encode($d->pembayaran->map(function($p) {
                                                        return [
                                                            'id' => $p->id,
                                                            'nominal' => number_format($p->nominal_bayar, 0, ',', '.'),
                                                            'tanggal' => $p->created_at->format('d/m/Y'),
                                                            'status' => $p->status_konfirmasi, 
                                                            'bukti_url' => route('admin.pembayaran.view-bukti', $p->id)
                                                        ];
                                                    })) }}
                                                }"
                                                class="px-2 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-md text-[10px] font-bold hover:bg-indigo-600 hover:text-white transition whitespace-nowrap">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>

                                                @if($status == 'lunas')
                                                    @if($dataKwitansi)
                                                        {{-- TOMBOL LIHAT HANYA MUNCUL JIKA STRING FOTO ADA --}}
                                                        <button @click="$dispatch('view-kwitansi-pop', { 
                                                            nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                            img: '{{ asset('storage/' . $dataKwitansi->foto_kwitansi) }}'
                                                        })"
                                                           class="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white rounded-md text-[10px] font-bold transition-all shadow-sm flex items-center gap-1 whitespace-nowrap">
                                                            <i class="fas fa-check-circle"></i> Lihat Kwitansi
                                                        </button>
                                                    @else
                                                        {{-- TOMBOL UPLOAD MUNCUL JIKA FOTO NULL/KOSONG --}}
                                                        @php 
                                                            $pembayaranTerakhir = $d->pembayaran->where('status_konfirmasi', 'Diterima')->sortByDesc('created_at')->first(); 
                                                        @endphp
                                                        <button @click="$dispatch('open-modal-kwitansi', { 
                                                            id: '{{ $pembayaranTerakhir->id ?? $d->id }}', 
                                                            nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                            sudah_bayar: '{{ number_format($d->total_tagihan, 0, ',', '.') }}'
                                                        })" 
                                                        class="px-2 py-1 bg-[#FCA800] hover:bg-[#e09600] text-white rounded-md text-[10px] font-bold transition-all shadow-sm flex items-center gap-1 whitespace-nowrap">
                                                            <i class="fas fa-file-invoice"></i> Kwitansi
                                                        </button>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 font-medium">Data tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mt-4">
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $datas->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- MODAL UPLOAD KWITANSI LUNAS --}}
        <div x-data="{ 
            openKwitansi: false, 
            kwitansiData: {}, 
            previewUrl: null,
            submitKwitansi() {
                let formData = new FormData(this.$refs.formKwitansi);
                Swal.fire({ title: 'Mohon Tunggu', text: 'Sedang memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                fetch('/admin/pembayaran/update-kwitansi/' + this.kwitansiData.id, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: 'Kwitansi berhasil disimpan', icon: 'success', timer: 1500, showConfirmButton: false })
                        .then(() => window.location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                }).catch(() => Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error'));
            }
        }"
            @open-modal-kwitansi.window="openKwitansi = true; kwitansiData = $event.detail; previewUrl = null"
            x-show="openKwitansi" x-cloak
            class="fixed inset-0 z-[1001] flex items-center justify-center p-4">
            
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openKwitansi = false"></div>
            
            <div class="relative bg-white w-full max-w-lg rounded-[32px] shadow-2xl p-8 transform transition-all"
                x-show="openKwitansi" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95">
                
                <h2 class="text-2xl font-black text-center text-gray-900 mb-6">Upload Kwitansi Lunas</h2>

                <div class="bg-gray-50 rounded-2xl p-4 mb-6 border border-gray-100">
                    <div class="flex justify-between mb-2 text-xs font-bold uppercase">
                        <span class="text-gray-500">Nama Siswa</span>
                        <span class="text-gray-900" x-text="kwitansiData.nama"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-gray-500 font-bold uppercase">Total Pelunasan</span>
                        <span class="text-sm font-extrabold text-emerald-600">Rp <span x-text="kwitansiData.sudah_bayar"></span></span>
                    </div>
                </div>

                <form x-ref="formKwitansi" @submit.prevent="submitKwitansi()" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-gray-700">Pilih File Kwitansi (JPG/PNG)</label>
                        <div class="relative group">
                            <input type="file" name="foto_kwitansi" accept="image/*" required @change="previewUrl = URL.createObjectURL($event.target.files[0])"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 flex flex-col items-center justify-center bg-gray-50 group-hover:bg-gray-100 transition-colors">
                                <template x-if="!previewUrl">
                                    <div class="text-center">
                                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-cloud-upload-alt text-xl"></i></div>
                                        <p class="text-xs text-gray-500 font-medium">Klik atau seret gambar ke sini</p>
                                    </div>
                                </template>
                                <template x-if="previewUrl">
                                    <div class="text-center">
                                        <img :src="previewUrl" class="h-40 w-auto mx-auto rounded-lg shadow-md mb-2 object-cover border-2 border-white">
                                        <p class="text-[10px] text-blue-600 font-bold">Gambar terpilih - Klik untuk mengganti</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 mt-8">
                        <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 transition active:scale-95 text-sm">
                            Konfirmasi & Simpan Kwitansi
                        </button>
                        <button type="button" @click="openKwitansi = false" class="w-full py-3 text-gray-400 font-bold rounded-xl text-xs hover:text-gray-600 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- POPUP LIHAT KWITANSI (VIEW ONLY) --}}
       <div x-data="{ openView: false, viewData: {} }"
        @view-kwitansi-pop.window="openView = true; viewData = $event.detail"
        x-show="openView" 
        x-cloak
        class="fixed inset-0 z-[1002] flex items-center justify-center p-4 sm:p-6">
        
        {{-- Overlay dengan efek Blur --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" 
            x-show="openView" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            @click="openView = false">
        </div>

        {{-- Konten Pop-up --}}
        <div class="relative bg-white w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/20"
            x-show="openView" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
            x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            {{-- Header --}}
            <div class="px-8 pt-8 pb-4 flex justify-between items-center">
                <div>
                    <p class="text-teal-600 text-xs font-black uppercase tracking-widest mb-1">Pratinjau Dokumen</p>
                    <h3 class="text-2xl font-black text-gray-900 leading-none" x-text="viewData.nama"></h3>
                </div>
                <button @click="openView = false" 
                    class="bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-500 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Area Gambar --}}
            <div class="px-8 pb-4">
                <div class="group relative bg-gray-50 rounded-[2rem] overflow-hidden border-2 border-dashed border-gray-200 flex items-center justify-center min-h-[300px] transition-all hover:border-teal-300">
                    <img :src="viewData.img" 
                        class="w-full h-auto object-contain max-h-[65vh] shadow-sm group-hover:scale-[1.02] transition-transform duration-500"
                        alt="Kwitansi Pembayaran">
                    
                    {{-- Overlay Hover Gambar --}}
                    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                </div>
            </div>

            {{-- Footer/Aksi --}}
            <div class="px-8 pb-8 pt-2 flex flex-col sm:flex-row gap-3">
                <button @click="openView = false" 
                    class="flex-1 px-6 py-4 border-2 border-[#003366] text-[#003366] font-black hover:bg-[#003366] hover:text-white rounded-2xl font-bold transition-all active:scale-95">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>

        {{-- MODAL DETAIL PEMBAYARAN --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="openModal = false"></div>
                <div x-show="openModal" x-transition.scale.95 class="inline-block w-full max-w-xl p-6 my-8 text-left align-middle bg-white shadow-2xl rounded-3xl relative z-10">
                    <h3 class="mb-6 text-xl font-black text-center text-gray-900">Detail Pembayaran</h3>
                    <div class="grid grid-cols-3 gap-4 p-4 mb-6 bg-gray-50/50 border border-gray-100 rounded-2xl text-xs">
                        <div><p class="text-gray-400 font-bold uppercase">Nama</p><p class="font-bold text-gray-800" x-text="selectedData.nama"></p></div>
                        <div><p class="text-gray-400 font-bold uppercase">NISN</p><p class="font-bold text-gray-800" x-text="selectedData.nisn"></p></div>
                        <div><p class="text-gray-400 font-bold uppercase">Cicilan</p><p class="font-bold text-gray-800" x-text="selectedData.riwayat.length + 'x Pembayaran'"></p></div>
                    </div>
                    <div class="mb-6 border-2 border-gray-800 rounded-xl p-4 space-y-2 text-xs">
                        <div class="flex justify-between"><span>Total Tagihan</span><span class="font-bold" x-text="'Rp ' + selectedData.total"></span></div>
                        <div class="flex justify-between"><span>Sudah Dibayar</span><span class="font-bold text-emerald-500" x-text="'Rp ' + selectedData.sudah_bayar"></span></div>
                        <div class="pt-2 border-t flex justify-between font-black text-gray-900"><span>Sisa Tagihan</span><span :class="selectedData.sisa === '0' ? 'text-gray-400' : 'text-rose-500'" x-text="'Rp ' + selectedData.sisa"></span></div>
                    </div>
                    <div class="space-y-2 max-h-[200px] overflow-y-auto custom-scrollbar">
                        <template x-for="(item, index) in selectedData.riwayat" :key="index">
                            <div class="flex justify-between items-center p-3 border rounded-xl bg-white text-xs">
                                <div><p class="font-bold" x-text="'Cicilan ' + (index + 1)"></p><p class="text-gray-400" x-text="item.tanggal"></p><a :href="item.bukti_url" target="_blank" class="text-blue-500 font-bold">Lihat Bukti</a></div>
                                <div class="text-right"><p class="font-black text-gray-900" x-text="'Rp ' + item.nominal"></p><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border" :class="item.status === 'Diterima' ? 'bg-emerald-50 text-emerald-600' : 'bg-emerald-50 text-emerald-600'" x-text="item.status"></span></div>
                            </div>
                        </template>
                    </div>
                    <div class="mt-8 flex justify-end"><button @click="openModal = false" class="px-6 py-2 border-2 border-[#003366] text-[#003366] text-xs font-black rounded-xl hover:bg-[#003366] hover:text-white transition-all">Tutup</button></div>
                </div>
            </div>
        </div>

        {{-- MODAL VERIFIKASI --}}
        <div x-data="{ openVerif: false, verifData: {}, showFullImage: false }"
            @open-verifikasi.window="openVerif = true; verifData = $event.detail" 
            x-show="openVerif" x-cloak
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openVerif = false"></div>
            <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6">
                <h2 class="text-xl font-bold text-center text-gray-900 mb-6">Verifikasi Pembayaran</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div class="aspect-square border rounded-xl overflow-hidden bg-gray-50 cursor-zoom-in" @click="showFullImage = true"><img :src="verifData.bukti" class="w-full h-full object-contain"></div>
                    <div class="flex flex-col space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl space-y-2">
                            <div class="flex justify-between text-gray-500"><span>Siswa</span><span class="font-bold text-gray-800" x-text="verifData.nama"></span></div>
                            <div class="flex justify-between text-gray-500"><span>Nominal</span><span class="font-bold text-emerald-500" x-text="'Rp ' + verifData.nominal_input"></span></div>
                        </div>
                        <div class="flex flex-col gap-2 mt-auto">
                            <button @click="handleReject(verifData.id)" class="w-full py-2 border-2 border-rose-500 text-rose-500 font-bold rounded-xl">Tolak Bukti</button>
                            <button @click="handleVerify(verifData.id)" class="w-full py-2 bg-[#003366] text-white font-bold rounded-xl">Konfirmasi & Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="showFullImage" @click="showFullImage = false" class="fixed inset-0 z-[1100] bg-black/90 flex items-center justify-center p-6"><img :src="verifData.bukti" class="max-w-full max-h-full object-contain"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#start_date", { dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y", onChange: () => document.getElementById('filterForm').submit() });
            flatpickr("#end_date", { dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y", onChange: () => document.getElementById('filterForm').submit() });
            
            document.querySelectorAll('.custom-select-container').forEach(d => {
                const trigger = d.querySelector('.custom-select-trigger');
                if(trigger) trigger.onclick = (e) => { 
                    e.stopPropagation(); 
                    document.querySelectorAll('.custom-select-container').forEach(other => { if(other !== d) other.classList.remove('active'); });
                    d.classList.toggle('active'); 
                };
                d.querySelectorAll('.custom-select-option').forEach(o => o.onclick = () => { 
                    d.querySelector('input').value = o.dataset.value; 
                    document.getElementById('filterForm').submit(); 
                });
            });
            window.onclick = () => { document.querySelectorAll('.custom-select-container').forEach(d => d.classList.remove('active')); };
        });

        let timeout = null;
        function doSearch() { clearTimeout(timeout); timeout = setTimeout(() => document.getElementById('filterForm').submit(), 600); }

        function handleVerify(id) {
            Swal.fire({ title: 'Simpan?', text: "Konfirmasi pembayaran ini?", icon: 'question', showCancelButton: true, confirmButtonColor: '#003366', confirmButtonText: 'Ya, Simpan' }).then(r => {
                if (r.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });
                    fetch(`/admin/pembayaran/verify/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => Swal.fire('Berhasil', data.message, 'success').then(() => window.location.reload()))
                    .catch(() => Swal.fire('Error', 'Gagal memproses data', 'error'));
                }
            });
        }

        function handleReject(id) {
            Swal.fire({ title: 'Tolak?', input: 'textarea', inputPlaceholder: 'Alasan...', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', confirmButtonText: 'Tolak' }).then(r => {
                if (r.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });
                    fetch(`/admin/pembayaran/reject/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ alasan: r.value }) })
                    .then(res => res.json())
                    .then(data => Swal.fire('Ditolak', data.message, 'success').then(() => window.location.reload()))
                    .catch(() => Swal.fire('Error', 'Gagal memproses data', 'error'));
                }
            });
        }
    </script>
@endsection