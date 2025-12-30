@extends('admin.layouts.app')

@section('title', 'Kelola Pembayaran')

@section('content')

    {{-- LOAD ALPINE.JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Styling Dropdown Custom */
        .custom-select-container {
            position: relative;
            min-width: 160px;
            cursor: pointer;
        }

        .custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: white;
            font-size: 0.875rem;
        }

        .custom-select-options {
            position: absolute;
            top: 110%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 50;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .custom-select-container.active .custom-select-options {
            display: block;
        }

        .custom-select-option {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .custom-select-option:hover {
            background-color: #f3f4f6;
        }

        .custom-select-option.selected {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }

        .arrow {
            transition: transform 0.2s;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .custom-select-container.active .arrow {
            transform: rotate(180deg);
        }
    </style>

    <div class="flex min-h-screen bg-white" x-data="{ openModal: false, selectedData: { riwayat: [] } }">
        <div class="h-screen sticky top-0 ">
            <x-sidebar />
        </div>

        <main class="w-full overflow-y-auto p-6">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <div class="max-w-7xl mx-auto">
                <x-pageheadersatu title="Kelola Pembayaran"
                    description="Verifikasi bukti transfer dan pantau tagihan siswa di sini!" />

                <div class="mb-6 flex flex-col gap-3 items-start">
                    <h2 class="text-xl font-semibold text-black">Daftar Pembayaran</h2>
                    <a href="{{ route('admin.export.pembayaran') }}"
                        class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg shadow-sm transition">
                        <img src="{{ asset('icons/export.svg') }}" alt="Export Excel Icon" class="h-5 w-5">
                        Ekspor Excel
                    </a>
                </div>
                {{-- Filter Bar --}}
                <div class="mb-6 mt-8">
                    <form id="filterForm" action="{{ route('admin.pembayaran.index') }}" method="GET"
                        class="flex flex-wrap items-center gap-3">

                        {{-- 1. Search Input --}}
                        <div class="flex-1 min-w-[200px] max-w-md relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                            <input type="text" name="search" id="searchInput"
                                placeholder="Cari nama siswa, NISN, No Telepon atau sekolah..."
                                value="{{ request('search') }}" oninput="doSearch()" {{-- Panggil fungsi debounce --}}
                                class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs shadow-sm">
                        </div>

                        {{-- 2. Date Range Picker (Auto Submit) --}}
                        <div class="flex items-center bg-white border border-gray-300 rounded-lg px-3 shadow-sm hover:border-blue-400 transition-colors"
                            style="height: 38px;"> {{-- Mengunci tinggi agar sama dengan input lainnya --}}

                            {{-- Input Dari --}}
                            <div class="flex items-center gap-2">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-tighter leading-none">Dari</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    onchange="document.getElementById('filterForm').submit()"
                                    class="border-none p-0 text-xs focus:ring-0 cursor-pointer text-gray-600 bg-transparent outline-none min-w-[100px]"
                                    style="line-height: 1;">
                            </div>

                            {{-- Separator --}}
                            <div class="h-4 w-[1px] bg-gray-200 mx-2"></div>

                            {{-- Input Ke --}}
                            <div class="flex items-center gap-2">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-tighter leading-none">Ke</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    onchange="document.getElementById('filterForm').submit()"
                                    class="border-none p-0 text-xs focus:ring-0 cursor-pointer text-gray-600 bg-transparent outline-none min-w-[100px]"
                                    style="line-height: 1;">
                            </div>
                        </div>

                        {{-- 3. Dropdown Status (Menggunakan JS Anda) --}}
                        <div class="custom-select-container" id="dropdownStatus">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <div class="custom-select-trigger py-2 min-w-[120px]">
                                <span
                                    class="text-xs">{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</span>
                                <i class="fas fa-chevron-down arrow text-[9px]"></i>
                            </div>
                            <div class="custom-select-options">
                                <div class="custom-select-option text-xs" data-value="">Semua Status</div>
                                @foreach ($list_status as $status)
                                    <div class="custom-select-option text-xs" data-value="{{ $status }}">{{ ucfirst($status) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 4. Dropdown Sekolah (Menggunakan JS Anda) --}}
                        <div class="custom-select-container" id="dropdownSekolah">
                            <input type="hidden" name="asal_sekolah" value="{{ request('asal_sekolah') }}">
                            <div class="custom-select-trigger py-2 min-w-[140px]">
                                <span
                                    class="truncate max-w-[100px] text-xs">{{ request('asal_sekolah') ? request('asal_sekolah') : 'Semua Sekolah' }}</span>
                                <i class="fas fa-chevron-down arrow text-[9px]"></i>
                            </div>
                            <div class="custom-select-options border-none">
                                <div class="custom-select-option text-xs" data-value="">Semua Sekolah</div>
                                @foreach ($list_sekolah as $sekolah)
                                    <div class="custom-select-option text-xs" data-value="{{ $sekolah }}">{{ $sekolah }}</div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 5. Reset Icon --}}
                        @if(request()->anyFilled(['search', 'status', 'asal_sekolah', 'start_date', 'end_date']))
                            <a href="{{ route('admin.pembayaran.index') }}"
                                class="ml-auto p-2 text-gray-400 hover:text-rose-500 transition-colors" title="Reset Filter">
                                <i class="fas fa-times-circle text-lg"></i>
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Table --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nama Lengkap
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">NISN / No.
                                        Telp</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tgl Daftar
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Total Tagihan
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Sudah Dibayar
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Sisa Tagihan
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($datas as $d)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $d->pendaftaran->nama_siswa }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="font-medium text-gray-900">{{ $d->pendaftaran->nisn ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $d->pendaftaran->no_telp ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $d->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                                            Rp {{ number_format($d->total_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                                            Rp {{ number_format($d->total_tagihan - $d->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $d->sisa_tagihan > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                            Rp {{ number_format($d->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $d->status_pembayaran == 'lunas' ? 'bg-emerald-100 text-emerald-700 border-emerald-500' : 'bg-yellow-100 text-yellow-700 border-yellow-500' }}">
                                                {{ $d->status_pembayaran }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
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
                                                                                                })"
                                                    class="px-4 py-1.5 bg-amber-500 text-white rounded-lg text-xs font-bold hover:bg-amber-600 transition shadow-sm animate-pulse">
                                                    <i class="fas fa-clipboard-check mr-1"></i> Verifikasi
                                                </button>
                                            @else
                                                                            <button
                                                                                @click="openModal = true; selectedData = { 
                                                                                                                                                                                                                                                nama: '{{ addslashes($d->pendaftaran->nama_siswa) }}',
                                                                                                                                                                                                                                                nisn: '{{ $d->pendaftaran->nisn ?? '-' }}',
                                                                                                                                                                                                                                                no_telp: '{{ $d->pendaftaran->no_telp ?? '-' }}',
                                                                                                                                                                                                                                                total_cicilan: '{{ $d->pembayaran->count() }}x pembayaran', 
                                                                                                                                                                                                                                                total: '{{ number_format($d->total_tagihan, 0, ',', '.') }}',
                                                                                                                                                                                                                                                terbayar: '{{ number_format($d->total_tagihan - $d->sisa_tagihan, 0, ',', '.') }}',
                                                                                                                                                                                                                                                sisa: '{{ number_format($d->sisa_tagihan, 0, ',', '.') }}',
                                                                                                                                                                                                                                                riwayat: {{ $d->pembayaran->map(function ($item, $index) {
                                                    return [
                                                        'nama' => 'Cicilan ' . ($index + 1),
                                                        'tanggal' => $item->created_at->format('d/m/Y'),
                                                        'jumlah' => number_format($item->nominal_bayar, 0, ',', '.'),
                                                        'status' => $item->status_konfirmasi,
                                                        'bukti_url' => route('admin.pembayaran.view-bukti', $item->id)
                                                    ];
                                                })->toJson() }}
                                                                                                                                                                                                                                            }"
                                                                                class="px-4 py-1.5 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-600 hover:text-white transition">
                                                                                Lihat Detail
                                                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">Tidak ada data pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        {{-- MODAL DETAIL (Untuk Riwayat) --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-[999] overflow-y-auto flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="openModal = false"></div>
            <div class="relative bg-white w-full max-w-2xl rounded-[20px] shadow-2xl p-8 overflow-hidden">
                <h3 class="text-2xl font-extrabold text-center text-gray-900 mb-8">Detail Pembayaran</h3>
                <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Nama Siswa</p>
                            <p class="text-sm font-bold text-gray-900" x-text="selectedData.nama"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">NISN</p>
                            <p class="text-sm font-bold text-gray-900" x-text="selectedData.nisn"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Cicilan</p>
                            <p class="text-sm font-bold text-gray-900" x-text="selectedData.total_cicilan"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-8 space-y-3">
                    <div class="flex justify-between text-sm"><span>Total Tagihan</span><span class="font-bold">Rp. <span
                                x-text="selectedData.total"></span></span></div>
                    <div class="flex justify-between text-sm text-emerald-500 font-bold"><span>Sudah Dibayar</span><span>Rp.
                            <span x-text="selectedData.terbayar"></span></span></div>
                    <div class="border-t pt-3 flex justify-between font-bold"><span>Sisa Tagihan</span><span
                            class="text-rose-600">Rp. <span x-text="selectedData.sisa"></span></span></div>
                </div>
                <div class="mb-8 max-h-60 overflow-y-auto space-y-4 pr-2">
                    <template x-for="item in selectedData.riwayat">
                        <div class="p-4 border rounded-xl flex justify-between items-center bg-white">
                            <div>
                                <p class="font-bold text-gray-900" x-text="item.nama"></p>
                                <p class="text-xs text-gray-400" x-text="item.tanggal"></p>
                                <a :href="item.bukti_url" target="_blank"
                                    class="text-xs text-blue-500 underline font-semibold mt-1 inline-block">Buka Bukti</a>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">Rp. <span x-text="item.jumlah"></span></p>
                                <span :class="{
                                                        'bg-emerald-400': item.status === 'Dikonfirmasi',
                                                        'bg-rose-400': item.status === 'Ditolak',
                                                        'bg-amber-400': item.status === 'Menunggu'
                                                    }"
                                    class="inline-block px-2 py-0.5 rounded-full text-[10px] text-white font-bold mt-1"
                                    x-text="item.status"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <button @click="openModal = false"
                    class="w-full py-3 border-2 border-indigo-900 text-indigo-900 font-bold rounded-xl hover:bg-indigo-50 transition">Tutup
                    Riwayat</button>
            </div>
        </div>

        {{-- MODAL VERIFIKASI (Untuk Approve/Reject) --}}
        <div x-data="{ openVerif: false, verifData: {} }"
            @open-verifikasi.window="openVerif = true; verifData = $event.detail" x-show="openVerif" x-cloak
            class="fixed inset-0 z-[1000] overflow-y-auto flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60" @click="openVerif = false"></div>
            <div
                class="relative bg-white w-full max-w-4xl rounded-[30px] shadow-2xl p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
                {{-- Sisi Kiri Bukti --}}
                <div>
                    <p class="font-bold text-gray-900 mb-3"><i class="fas fa-image mr-2 text-indigo-600"></i> Bukti Transfer
                    </p>
                    <div
                        class="border rounded-2xl overflow-hidden bg-gray-50 aspect-[3/4] flex items-center justify-center relative group">
                        <img :src="verifData.bukti" class="w-full h-full object-contain">
                        <a :href="verifData.bukti" target="_blank"
                            class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="bg-white px-3 py-1 rounded-lg text-xs font-bold shadow">Perbesar</span>
                        </a>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 text-center" x-text="'Tanggal Upload: ' + verifData.tanggal">
                    </p>
                </div>
                {{-- Sisi Kanan Form --}}
                <div class="flex flex-col">
                    <div class="bg-indigo-50 p-6 rounded-2xl mb-6 border border-indigo-100">
                        <p class="text-xs text-indigo-400 font-bold uppercase">Nama Siswa</p>
                        <p class="text-lg font-black text-indigo-900" x-text="verifData.nama"></p>
                    </div>
                    <div class="flex-grow space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nominal yang Dibayar</label>
                            <input type="text" :value="'Rp ' + verifData.nominal_input" readonly
                                class="w-full p-3 bg-gray-50 border rounded-xl font-black text-xl text-gray-800 focus:outline-none">
                        </div>
                        <div class="p-5 border-2 border-dashed rounded-2xl space-y-2 bg-gray-50/30">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 font-medium">Sisa Sebelum Bayar</span>
                                <span class="font-bold text-rose-500" x-text="'Rp ' + verifData.sisa_awal"></span>
                            </div>
                            <div class="pt-2 border-t flex justify-between items-center">
                                <span class="font-bold text-gray-900">Sisa Jika Disetujui</span>
                                <span class="font-black text-indigo-600 text-xl"
                                    x-text="'Rp ' + verifData.sisa_akhir"></span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <button @click="handleReject(verifData.id)"
                            class="py-3 border-2 border-rose-500 text-rose-500 font-bold rounded-xl hover:bg-rose-50 transition flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                        <button @click="handleVerify(verifData.id)"
                            class="py-3 bg-indigo-900 text-white font-bold rounded-xl hover:bg-indigo-800 transition shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-check-double"></i> Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JS UNTUK TOMBOL AKSI
        function handleVerify(id) {
            if (!confirm('Apakah dana sudah masuk ke rekening?')) return;
            fetch(`/admin/pembayaran/verify/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => { if (data.success) window.location.reload(); else alert('Error: ' + data.message); })
                .catch(err => alert('Terjadi kesalahan koneksi.'));
        }

        function handleReject(id) {
            const alasan = prompt("Alasan penolakan bukti ini:");
            if (alasan === null) return;
            fetch(`/admin/pembayaran/reject/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ alasan: alasan })
            })
                .then(res => res.json())
                .then(data => { if (data.success) window.location.reload(); });
        }

        // JS UNTUK DROPDOWN FILTER
        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.custom-select-container');
            const filterForm = document.getElementById('filterForm');

            dropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('.custom-select-trigger');
                const options = dropdown.querySelectorAll('.custom-select-option');
                const hiddenInput = dropdown.querySelector('input[type="hidden"]');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdowns.forEach(d => { if (d !== dropdown) d.classList.remove('active'); });
                    dropdown.classList.toggle('active');
                });

                options.forEach(option => {
                    option.addEventListener('click', () => {
                        hiddenInput.value = option.getAttribute('data-value');
                        filterForm.submit();
                    });
                });
            });
            window.addEventListener('click', () => dropdowns.forEach(d => d.classList.remove('active')));
        });

        let timeout = null;

        function doSearch() {
            // Hapus timeout sebelumnya jika pengguna masih mengetik
            clearTimeout(timeout);

            // Buat timeout baru (500ms atau 0.5 detik)
            timeout = setTimeout(function () {
                document.getElementById('filterForm').submit();
            }, 500);
        }

        // Pastikan kursor tetap berada di akhir teks setelah reload halaman
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            if (searchInput.value !== "") {
                searchInput.focus();
                // Memindahkan kursor ke akhir teks
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        });
    </script>
@endsection