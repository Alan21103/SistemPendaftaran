@extends('admin.layouts.app')

@section('title', 'Kelola Pendaftaran')

@section('content')

    {{-- LOAD ASSET CSS --}}
    @vite(['resources/css/custom-dropdown.css', 'resources/css/swal-modern.css'])
    <style>
    @media (max-width: 768px) {
        .custom-select-container {
            min-width: 150px !important;
            width: auto !important;
        }

         /* 3. Filter Bar Mobile */
            #filterForm { flex-direction: column; align-items: stretch; }
            .flex-1.min-w-\[200px\] { max-width: none !important; }
            .custom-select-container { min-width: 0 !important; width: 100%; }
            .h-\[46px\] { height: auto !important; padding: 10px 0; flex-direction: column; align-items: flex-start; gap: 10px; }
            .h-5.w-\[1px\] { display: none; } /* Hilangkan garis pemisah date di mobile */
        
    }
    </style>
    <div class="flex min-h-screen bg-white">

        <main class="w-full overflow-y-auto p-4 md:p-6">

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
            <meta name="csrf-token" content="{{ csrf_token() }}">
            
            {{-- Tambahkan library SweetAlert2 --}}
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <div class="max-w-7xl mx-auto">

                <x-pageheadersatu title="Kelola Pendaftaran" description="Kelola data pendaftar di sini!" />

                {{-- Toolbar --}}
                <div class="mb-6 flex flex-row justify-between items-center gap-3">
                    <h2 class="text-xl font-semibold text-black">Daftar Pendaftar</h2>
                    <a href="{{ route('admin.export.pendaftaran') }}"
                        class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium py-2 px-3 md:px-4 rounded-lg shadow-sm transition text-sm">
                        <img src="{{ asset('icons/export.svg') }}" alt="Export" class="h-4 w-4 md:h-5 md:w-5">
                        <span class="hidden sm:inline">Export Excel</span>
                        <span class="sm:hidden">Export</span>
                    </a>
                </div>

                {{-- FILTER BAR --}}
                <div class="mb-6">
                    <form id="filterForm" action="{{ route('admin.pendaftaran.index') }}" method="GET" 
                        class="flex flex-col md:flex-row md:items-center gap-3">

                        {{-- Search - Full width di mobile, lebar maksimal di desktop --}}
                        <div class="relative flex-1 md:max-w-xs lg:max-w-md">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" placeholder="Cari nama atau NISN..."
                                value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl md:rounded-lg border border-gray-300 bg-white focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                        </div>

                        {{-- Grouping Dropdown & Sort: Grid 2 kolom di mobile, Flex Row di desktop --}}
                        <div class="grid grid-cols-2 md:flex md:flex-row gap-2 md:gap-3">
                            
                            {{-- Dropdown Status --}}
                            <div class="custom-select-container !w-full md:!w-[160px]" id="dropdownStatus">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <div class="custom-select-trigger !py-2.5 !rounded-xl md:!rounded-lg !text-xs md:!text-sm">
                                    <span class="truncate">{{ request('status') ? ucfirst(request('status')) : 'Status' }}</span>
                                    <i class="fas fa-chevron-down arrow"></i>
                                </div>
                                <div class="custom-select-options">
                                    <div class="custom-select-option" data-value="">Semua Status</div>
                                    @foreach ($list_status as $status)
                                        <div class="custom-select-option" data-value="{{ $status }}">{{ ucfirst($status) }}</div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Dropdown Sekolah --}}
                            <div class="custom-select-container !w-full md:!w-[180px]" id="dropdownSekolah">
                                <input type="hidden" name="asal_sekolah" value="{{ request('asal_sekolah') }}">
                                <div class="custom-select-trigger !py-2.5 !rounded-xl md:!rounded-lg !text-xs md:!text-sm">
                                    <span class="truncate max-w-[80px] md:max-w-[120px]">{{ request('asal_sekolah') ? request('asal_sekolah') : 'Sekolah' }}</span>
                                    <i class="fas fa-chevron-down arrow"></i>
                                </div>
                                <div class="custom-select-options">
                                    <div class="custom-select-option" data-value="">Semua Sekolah</div>
                                    @foreach ($list_sekolah as $sekolah)
                                        <div class="custom-select-option" data-value="{{ $sekolah }}">{{ $sekolah }}</div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Sort Nama --}}
                            <button type="button" id="toggleSortNama" 
                                class="py-2.5 px-3 rounded-xl md:rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-xs md:text-sm font-medium flex items-center justify-between md:justify-center md:gap-2 transition shadow-sm">
                                <span>Nama</span>
                                <span class="text-gray-500">@if(request('sort_by') === 'nama_desc') ▼ @elseif(request('sort_by') === 'nama_asc') ▲ @else ↕ @endif</span>
                            </button>

                            {{-- Sort Tanggal --}}
                            <button type="button" id="toggleSortTanggal" 
                                class="py-2.5 px-3 rounded-xl md:rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-xs md:text-sm font-medium flex items-center justify-between md:justify-center md:gap-2 transition shadow-sm">
                                <span>Tanggal</span>
                                <span class="text-gray-500">@if(request('sort_by') === 'tanggal_asc') ▲ @else ▼ @endif</span>
                            </button>
                        </div>

                        <input type="hidden" name="sort_by" id="hiddenSortBy" value="{{ request('sort_by') }}">
                    </form>
                </div>

                {{-- VIEW TABLE (DESKTOP ONLY) --}}
                <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
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

                {{-- VIEW CARD (MOBILE ONLY) --}}
                <div class="grid grid-cols-1 gap-4 md:hidden">
                    @forelse ($pendaftarans as $p)
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="max-w-[70%]">
                                    <h3 class="text-sm font-bold text-gray-900 leading-tight">{{ $p->nama_siswa }}</h3>
                                    <p class="text-[11px] text-gray-500 mt-1">NISN: {{ $p->nisn ?? '-' }}</p>
                                </div>
                                @php
                                    $statusMobile = match (strtolower($p->status)) {
                                        'diterima', 'disetujui' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-yellow-50 text-yellow-700 border-yellow-200'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $statusMobile }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2.5 mb-5">
                                <div class="flex items-center gap-3 text-xs text-gray-600">
                                    <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center">
                                        <i class="fas fa-school text-gray-400"></i>
                                    </div>
                                    <span class="truncate">{{ $p->asal_sekolah }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-gray-600">
                                    <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <span>{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-4 border-t border-gray-100">
                                <a href="{{ route('admin.pendaftaran.show', $p->id_pendaftaran) }}" 
                                   class="flex items-center justify-center py-2.5 rounded-xl bg-indigo-50 text-indigo-600 text-xs font-bold transition active:scale-95">
                                    Detail
                                </a>
                                @if(strtolower($p->status) === 'pending')
                                    <button onclick="handleAction(this, '{{ route('admin.pendaftaran.approve', $p->id_pendaftaran) }}', 'setuju')"
                                            class="flex items-center justify-center py-2.5 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold transition active:scale-95">
                                        Setuju
                                    </button>
                                @else
                                    <button disabled class="flex items-center justify-center py-2.5 rounded-xl bg-gray-50 text-gray-300 text-xs font-bold opacity-50">
                                        Setuju
                                    </button>
                                @endif
                                
                                @if(strtolower($p->status) === 'pending')
                                    <button onclick="handleAction(this, '{{ route('admin.pendaftaran.reject', $p->id_pendaftaran) }}', 'tolak')"
                                            class="col-span-2 flex items-center justify-center py-2.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold transition active:scale-95">
                                        Tolak Pendaftaran
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-50 p-10 text-center rounded-2xl border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 text-sm">Tidak ada data pendaftar ditemukan.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    {{-- JS --}}
    @vite(['resources/js/pendaftaran-script.js', 'resources/js/pendaftaran-action.js'])

@endsection