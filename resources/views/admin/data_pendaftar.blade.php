@extends('admin.layouts.app')

@section('title', 'Kelola Pendaftaran')

@section('content')

    {{-- LOAD ASSET CSS --}}
    @vite(['resources/css/custom-dropdown.css', 'resources/css/swal-modern.css'])

    <div class="flex min-h-screen bg-white">

        <div class="h-screen sticky top-0 ">
            <x-sidebar />
        </div>

        <main class="w-full overflow-y-auto p-6 md:p-6">

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
            <meta name="csrf-token" content="{{ csrf_token() }}">
            
            {{-- Tambahkan library SweetAlert2 --}}
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <div class="max-w-7xl mx-auto">

                <x-pageheadersatu title="Kelola Pendaftaran" description="Kelola data pendaftar di sini!" />

                {{-- Toolbar --}}
                <div class="mb-6 flex flex-col gap-3 items-start">
                    <h2 class="text-xl font-semibold text-black">Daftar Pendaftar</h2>
                    <a href="{{ route('admin.export.pendaftaran') }}"
                        class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg shadow-sm transition">
                        <img src="{{ asset('icons/export.svg') }}" alt="Export Excel Icon" class="h-5 w-5">
                        Ekspor Excel
                    </a>
                </div>

                {{-- FILTER BAR (TETAP SEPERTI ASLI) --}}
                <div class="mb-6">
                    <form id="filterForm" action="{{ route('admin.pendaftaran.index') }}" method="GET"
                        class="flex flex-wrap items-center gap-3">

                        {{-- Search --}}
                        <div class="flex-1 min-w-[200px] max-w-3xl relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" placeholder="Cari nama atau NISN..."
                                value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                        </div>

                        {{-- CUSTOM DROPDOWN STATUS --}}
                        <div class="custom-select-container" id="dropdownStatus">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <div class="custom-select-trigger">
                                <span>{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <div class="custom-select-options">
                                <div class="custom-select-option {{ request('status') == '' ? 'selected' : '' }}" data-value="">Semua Status</div>
                                @foreach ($list_status as $status)
                                    <div class="custom-select-option {{ request('status') == $status ? 'selected' : '' }}" data-value="{{ $status }}">{{ ucfirst($status) }}</div>
                                @endforeach
                            </div>
                        </div>

                        {{-- CUSTOM DROPDOWN ASAL SEKOLAH --}}
                        <div class="custom-select-container" id="dropdownSekolah">
                            <input type="hidden" name="asal_sekolah" value="{{ request('asal_sekolah') }}">
                            <div class="custom-select-trigger">
                                <span class="truncate max-w-[150px]">{{ request('asal_sekolah') ? request('asal_sekolah') : 'Semua Sekolah' }}</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <div class="custom-select-options">
                                <div class="custom-select-option {{ request('asal_sekolah') == '' ? 'selected' : '' }}" data-value="">Semua Sekolah</div>
                                @foreach ($list_sekolah as $sekolah)
                                    <div class="custom-select-option {{ request('asal_sekolah') == $sekolah ? 'selected' : '' }}" data-value="{{ $sekolah }}">{{ $sekolah }}</div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tombol Sort Nama --}}
                        <button type="button" id="toggleSortNama" class="py-2.5 px-4 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm flex items-center gap-2 transition shadow-sm">
                            <span>Nama</span>
                            <span class="text-xs text-gray-500">@if(request('sort_by') === 'nama_desc') ▼ @elseif(request('sort_by') === 'nama_asc') ▲ @else ↕ @endif</span>
                        </button>

                        {{-- Tombol Sort Tanggal --}}
                        <button type="button" id="toggleSortTanggal" class="py-2.5 px-4 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm flex items-center gap-2 transition shadow-sm">
                            <span>Tanggal</span>
                            <span class="text-xs text-gray-500">@if(request('sort_by') === 'tanggal_asc') ▲ @else ▼ @endif</span>
                        </button>

                        <input type="hidden" name="sort_by" id="hiddenSortBy" value="{{ request('sort_by') }}">
                    </form>
                </div>

                {{-- Table (Tetap Seperti Asli) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NISN</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Asal Sekolah</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($pendaftarans as $p)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">{{ $p->nama_siswa }}</div></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-500">{{ $p->nisn ?? '-' }}</div></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-500">{{ $p->asal_sekolah }}</div></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusConfig = match (strtolower($p->status)) {
                                                    'diterima', 'disetujui' => 'bg-teal-100 text-teal-700 border-teal-700',
                                                    'ditolak' => 'bg-red-100 text-red-700 border-red-500',
                                                    default => 'bg-yellow-100 text-yellow-700 border-yellow-500'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-4 py-1 rounded-full text-xs font-bold border-2 {{ $statusConfig }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center items-center gap-2">
                                                <a href="{{ route('admin.pendaftaran.show', $p->id_pendaftaran) }}"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-600 text-xs font-semibold transition-all duration-200 hover:bg-indigo-600 hover:text-white shadow-sm">
                                                    Detail
                                                </a>

                                                @if(strtolower($p->status) === 'pending')
                                                    <button onclick="handleAction(this, '{{ route('admin.pendaftaran.approve', $p->id_pendaftaran) }}', 'setuju')"
                                                        class="inline-flex items-center px-3 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 text-xs font-semibold transition-all duration-200 hover:bg-emerald-500 hover:text-white shadow-sm">
                                                        Setujui
                                                    </button>
                                                    <button onclick="handleAction(this, '{{ route('admin.pendaftaran.reject', $p->id_pendaftaran) }}', 'tolak')"
                                                        class="inline-flex items-center px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-600 text-xs font-semibold transition-all duration-200 hover:bg-rose-500 hover:text-white shadow-sm">
                                                        Tolak
                                                    </button>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-xs font-semibold cursor-not-allowed opacity-60">Setujui</span>
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-xs font-semibold cursor-not-allowed opacity-60">Tolak</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- JS --}}
    @vite(['resources/js/pendaftaran-script.js', 'resources/js/pendaftaran-action.js'])

@endsection