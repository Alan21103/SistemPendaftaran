@extends('admin.layouts.app')

@section('title', 'Detail Pendaftaran Siswa')

@section('content')

    {{-- Panggil CSS Scrollbar dari file terpisah --}}
    @vite(['resources/css/style-hide-scrollbar.css'])

    <div class="flex min-h-screen bg-gray-50 font-sans">
        

        {{-- Class 'no-scrollbar' diterapkan di sini (pastikan ada di file CSS Anda) --}}
        <main class="w-full overflow-y-auto p-6 lg:p-6 no-scrollbar h-screen">
            <div class="max-w-7xl mx-auto pb-20">

                {{-- Header Halaman --}}
                <x-pageheaderdua title="Kelola Pendaftaran" description="Kelola persetujuan pendaftaran siswa baru" />

                {{-- Tombol Kembali & Judul --}}
                <div class="flex items-center gap-3 mb-8">
                    <a href="{{ route('admin.pendaftaran.index') }}" class="text-gray-500 hover:text-gray-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Data Pendaftar</h1>
                </div>

                {{-- CARD INFO UTAMA (FOTO & STATUS) --}}
                <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0 mx-auto md:mx-0">
                            <img src="{{ asset('storage/' . $pendaftaran->foto) }}" alt="Foto Siswa"
                                class="w-[200px] h-[240px] object-cover rounded-2xl shadow-md bg-gray-200"
                                onerror="this.onerror=null; this.src='https://placehold.co/200x240/e2e8f0/94a3b8?text=No+Photo';">
                        </div>

                        <div class="flex-grow w-full">
                            <div class="flex flex-wrap items-center gap-4 mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $pendaftaran->nama_siswa }}</h2>

                                @php
                                    $statusColor = match (strtolower($pendaftaran->status)) {
                                        'disetujui', 'diterima' => 'bg-teal-100 text-teal-700 border-teal-500',
                                        'ditolak' => 'bg-red-100 text-red-700 border-red-500',
                                        default => 'bg-yellow-100 text-yellow-700 border-yellow-500'
                                    };
                                @endphp

                                <span class="px-4 py-1 rounded-full text-sm font-bold border-2 {{ $statusColor }}">
                                    {{ ucfirst($pendaftaran->status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-[180px_20px_auto] gap-y-3 text-sm md:text-base">
                                <div class="font-bold text-gray-700">NISN</div>
                                <div class="hidden md:block">:</div>
                                <div class="text-gray-900 font-medium">{{ $pendaftaran->nisn ?? '-' }}</div>

                                <div class="font-bold text-gray-700">Asal Sekolah</div>
                                <div class="hidden md:block">:</div>
                                <div class="text-gray-900 font-medium">{{ $pendaftaran->asal_sekolah ?? '-' }}</div>

                                <div class="font-bold text-gray-700">Tanggal Daftar</div>
                                <div class="hidden md:block">:</div>
                                <div class="text-gray-900 font-medium">
                                    {{ \Carbon\Carbon::parse($pendaftaran->created_at)->translatedFormat('l, d F Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD INFO LENGKAP --}}
                <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Informasi Lengkap</h3>
                    <div class="h-px bg-gray-200 w-full mb-6"></div>

                    <div class="grid grid-cols-1 md:grid-cols-[220px_20px_auto] gap-y-4 text-sm md:text-base">
                        {{-- Data Diri --}}
                        <div class="font-bold text-gray-700">Nama Lengkap</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->nama_siswa }}</div>

                        <div class="font-bold text-gray-700">Tempat, Tanggal Lahir</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->tempat_tgl_lahir ?? '-' }}</div>

                        <div class="font-bold text-gray-700">Jenis Kelamin</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->jenis_kelamin ?? '-' }}</div>

                        <div class="font-bold text-gray-700">Agama</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->agama ?? '-' }}</div>

                        <div class="font-bold text-gray-700">Alamat</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->alamat ?? '-' }}</div>

                        {{-- Data Ortu --}}
                        <div class="font-bold text-gray-700 mt-2">Nama Ayah</div>
                        <div class="hidden md:block mt-2">:</div>
                        <div class="text-gray-900 mt-2">{{ $pendaftaran->nama_ayah ?? '...' }}</div>

                        <div class="font-bold text-gray-700">Pendidikan Terakhir Ayah</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->pendidikan_ayah ?? '...' }}</div>

                        <div class="font-bold text-gray-700">Pekerjaan Ayah</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->pekerjaan_ayah ?? '-' }}</div>

                        <div class="font-bold text-gray-700 mt-2">Nama Ibu</div>
                        <div class="hidden md:block mt-2">:</div>
                        <div class="text-gray-900 mt-2">{{ $pendaftaran->nama_ibu ?? '...' }}</div>

                        <div class="font-bold text-gray-700">Pendidikan Terakhir Ibu</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->pendidikan_ibu ?? '...' }}</div>

                        <div class="font-bold text-gray-700">Pekerjaan Ibu</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->pekerjaan_ibu ?? '-' }}</div>

                        <div class="font-bold text-gray-700">Nomor Telepon</div>
                        <div class="hidden md:block">:</div>
                        <div class="text-gray-900">{{ $pendaftaran->no_telp ?? '-' }}</div>
                    </div>
                </div>

                {{-- CARD DOKUMEN PERSYARATAN --}}
                <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Dokumen Persyaratan</h3>
                    <div class="h-px bg-gray-200 w-full mb-2"></div>

                    <div class="flex flex-col">
                        @php
                            $documents = [
                                'Kartu Keluarga' => 'kk',
                                'Akte Kelahiran' => 'akte',
                                'Ijazah TK' => 'ijazah_sk',
                                'Bukti Pembayaran' => 'bukti_bayar'
                            ];
                        @endphp

                        @foreach ($documents as $label => $field)
                            <div
                                class="py-5 border-b border-gray-100 last:border-0 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">{{ $label }}</h4>
                                    @if ($pendaftaran->$field)
                                        <div class="flex items-center gap-2 mt-1 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium">Sudah Diunggah</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 mt-1 text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium">Belum Diunggah</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($pendaftaran->$field)
                                    <div class="flex items-center gap-3">
                                        <button
                                            onclick="openDocumentModal('{{ route('admin.pendaftaran.download', ['pendaftaran' => $pendaftaran->id_pendaftaran, 'field' => $field, 'action' => 'view']) }}')"
                                            class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat
                                        </button>

                                        <a href="{{ route('admin.pendaftaran.download', ['pendaftaran' => $pendaftaran->id_pendaftaran, 'field' => $field, 'action' => 'download']) }}"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Unduh
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- TOMBOL AKSI FOOTER (Hanya jika Pending) --}}
                <div class="flex justify-end gap-4 mb-10">
                    @if(strtolower($pendaftaran->status) === 'pending')
                        <button id="btnSetujui" data-id="{{ $pendaftaran->id_pendaftaran }}" type="button"
                            class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white text-base font-medium rounded-lg transition shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Setujui Pendaftaran
                        </button>

                        <button id="btnTolak" data-id="{{ $pendaftaran->id_pendaftaran }}" type="button"
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-base font-medium rounded-lg transition shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tolak Pendaftaran
                        </button>
                    @endif
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL DOCUMENT --}}
    <div id="documentModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900 bg-opacity-75 transition-opacity duration-300">
        <div class="bg-white rounded-lg shadow-2xl w-11/12 h-5/6 max-w-5xl flex flex-col">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-800">Pratinjau Dokumen</h4>
                <button onclick="closeDocumentModal()"
                    class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 p-2 bg-gray-100">
                <iframe id="documentFrame" src="" frameborder="0"
                    class="w-full h-full rounded-md bg-white border border-gray-200"></iframe>
            </div>
        </div>
    </div>

    {{-- SCRIPT LENGKAP (MODAL & TOMBOL AKSI) --}}
    <script>
        // --- 1. SCRIPT UNTUK MODAL ---
        function openDocumentModal(url) {
            const modal = document.getElementById('documentModal');
            const iframe = document.getElementById('documentFrame');
            iframe.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDocumentModal() {
            const modal = document.getElementById('documentModal');
            const iframe = document.getElementById('documentFrame');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            iframe.src = '';
        }

        // --- 2. SCRIPT UNTUK TOMBOL SETUJUI/TOLAK ---
        document.addEventListener('DOMContentLoaded', () => {
            const btnSetujui = document.getElementById('btnSetujui');
            const btnTolak = document.getElementById('btnTolak');

            // Event Listener untuk Setujui
            if (btnSetujui) {
                btnSetujui.addEventListener('click', function () {
                    handleAction(this.dataset.id, 'approve');
                });
            }

            // Event Listener untuk Tolak
            if (btnTolak) {
                btnTolak.addEventListener('click', function () {
                    handleAction(this.dataset.id, 'reject');
                });
            }
        });

        // Fungsi Utama Handle Request
        function handleAction(id, action) {
            const label = action === 'approve' ? 'menyetujui' : 'menolak';
            // Sesuaikan route URL ini jika perlu (pastikan /admin/pendaftaran ada di routes)
            const url = action === 'approve'
                ? `{{ url('admin/pendaftaran') }}/${id}/approve`
                : `{{ url('admin/pendaftaran') }}/${id}/reject`;

            if (!confirm(`Apakah Anda yakin ingin ${label} pendaftaran ini?`)) return;

            // Tampilkan Loading pada tombol yang diklik
            const btn = action === 'approve' ? document.getElementById('btnSetujui') : document.getElementById('btnTolak');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            btn.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan sistem');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }
    </script>
@endsection