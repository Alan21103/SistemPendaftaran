@extends('admin.layouts.app')

@section('title', 'Kelola Konten')

@section('content')

    {{-- LOAD SWEETALERT2 & CSS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/custom-dropdown.css', 'resources/css/animations.css'])

    <style>
        /* --- RESPONSIVE MOBILE OPTIMIZATION (MAX 768px) --- */
        @media (max-width: 768px) {
            /* 1. Mengurangi padding utama agar konten lebih lebar */
            main {
                padding: 1rem !important;
            }

            /* 2. Header Halaman */
            h1.text-3xl {
                font-size: 1.5rem !important;
            }

            /* 3. Card Container */
            .rounded-2xl.bg-white.p-8 {
                padding: 1.25rem !important;
                margin-bottom: 1.5rem !important;
            }

            /* 4. Header Card (Judul Halaman & Tombol Tambah) */
            .flex.items-center.gap-4.mb-6 {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem !important;
            }

            .flex.items-center.gap-4.mb-6 h2 {
                font-size: 1.1rem !important;
                line-height: 1.4;
            }

            /* 5. Tenaga Pengajar (Foto & Nama) */
            .grid-cols-1.md\:grid-cols-2.gap-8 {
                gap: 1rem !important;
            }

            .flex.items-center.gap-6.p-4 {
                flex-direction: column; /* Foto di atas, teks di bawah */
                text-align: center;
                gap: 1rem !important;
            }

            .w-28.h-28.flex-shrink-0 {
                width: 100px !important;
                height: 100px !important;
                margin: 0 auto;
            }

            .flex.items-center.gap-6.p-4 .flex-1 .flex.gap-2 {
                justify-content: center; /* Tombol edit/hapus guru ke tengah */
            }

            /* 6. Layout Beranda & Ekstrakurikuler (Teks & Gambar) */
            .flex.flex-col.md\:flex-row.justify-between.gap-8 {
                gap: 1.5rem !important;
            }

            .w-full.md\:w-80.flex-shrink-0 {
                order: -1; /* Gambar muncul di atas teks pada mobile */
                width: 100% !important;
            }

            /* 7. Modal (Agar tidak terlalu mepet layar) */
            #modalTambahContent, #modalEditContent {
                margin: 1rem;
                padding: 1.5rem !important;
                max-height: 90vh;
                overflow-y: auto;
            }

            /* 8. Penyesuaian teks konten */
            .text-justify {
                text-align: left !important; /* Justify di mobile seringkali membuat teks renggang aneh */
            }

            .text-base, .text-lg {
                font-size: 0.95rem !important;
            }

            /* 9. Tombol Aksi Mobile */
            .flex.gap-3 button, .flex.gap-3 a {
                flex: 1; /* Tombol memenuhi lebar jika sejajar */
                justify-content: center;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
        }
        /* Kustomisasi tombol SweetAlert agar persis seperti di gambar */
        .swal2-styled.swal2-confirm {
            background-color: #EF4444 !important; /* Merah */
            color: white !important;
            border-radius: 0.5rem !important;
            padding: 0.6rem 2rem !important;
            font-weight: 700 !important;
            font-family: 'Inter', sans-serif !important;
        }
        .swal2-styled.swal2-cancel {
            background-color: white !important;
            color: #1E3A8A !important; /* Biru Gelap */
            border: 2px solid #1E3A8A !important;
            border-radius: 0.5rem !important;
            padding: 0.6rem 2rem !important;
            font-weight: 700 !important;
            font-family: 'Inter', sans-serif !important;
        }
        .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 800 !important;
            color: #000 !important;
        }
        .swal2-html-container {
            font-weight: 600 !important;
            color: #000 !important;
        }
    </style>

   <div class="flex min-h-screen bg-gray-50">
        
       

        {{-- KONTEN UTAMA --}}
        <main class="flex-1 w-full overflow-y-auto p-6 lg:p-6 animate-fade-in-up">

            {{-- HEADER HALAMAN --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-black">Kelola Konten</h1>
                <p class="mt-2 text-gray-600">Kelola seluruh konten halaman website sekolah di sini.</p>
                <hr class="my-5 border-gray-300">
            </div>

            {{-- ALERT BERHASIL --}}
            @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
            @endif

            {{-- LOOP KATEGORI --}}
            @foreach($kategori as $kat)
            <div class="rounded-2xl bg-white p-8 shadow-sm border border-gray-100 mb-8 hover:shadow-md transition-shadow duration-300">
                
                <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                    <h2 class="text-xl font-extrabold text-black">
                        @if($kat->nama === 'Beranda') Halaman Beranda
                        @elseif($kat->nama === 'Tentang Sekolah') Halaman Tentang Sekolah
                        @elseif($kat->nama === 'Informasi PPDB') Halaman Informasi PPDB
                        @else Halaman {{ $kat->nama }} @endif
                    </h2>

                    @php
                        $opsiTersedia = []; 
                        if ($kat->nama === 'Tentang Sekolah') {
                            $sudahAda = $kat->konten->pluck('judul')->map(fn($i) => strtolower(trim($i)))->toArray();
                            $semuaOpsi = ['Sejarah', 'Visi', 'Misi'];
                            foreach ($semuaOpsi as $o) { 
                                if (!in_array(strtolower($o), $sudahAda)) $opsiTersedia[] = $o; 
                            }
                        }
                    @endphp

                    @if(
                        ($kat->nama === 'Beranda' && $kat->konten->count() == 0) || 
                        ($kat->nama === 'Tentang Sekolah' && count($opsiTersedia) > 0) ||
                        ($kat->nama === 'Ekstrakurikuler') ||
                        ($kat->nama === 'Tenaga Pengajar') ||
                        ($kat->nama === 'Informasi PPDB' && $kat->konten->count() == 0)
                    )
                    <button class="bg-[#007BFF] hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition active:scale-95 flex items-center gap-2"
                        onclick='openTambahModal({{ $kat->id }}, "{{ $kat->nama }}", @json($opsiTersedia))'>
                        <span class="text-lg">+</span> Tambah
                    </button>
                    @endif
                </div>

                <div class="px-4"> 
                {{-- 1. TENAGA PENGAJAR --}}
                @if($kat->nama === 'Tenaga Pengajar')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($kat->konten as $konten)
                            <div class="flex items-center gap-6 p-4 rounded-xl border border-gray-50 bg-gray-50/30">
                                <div class="relative w-28 h-28 flex-shrink-0 group">
                                    @php $foto = $konten->media->where('urutan', 0)->first(); @endphp
                                    @if($foto)
                                        <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-full object-cover rounded-xl shadow-sm border border-white">
                                        <form action="{{ route('admin.konten_media.destroy', $foto->id) }}" method="POST" class="absolute -top-2 -right-2 hidden group-hover:block" onsubmit="return confirmDelete(event, 'Foto Guru')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.konten_media.store') }}" method="POST" enctype="multipart/form-data" id="quick-upload-guru-{{ $konten->id }}">
                                            @csrf
                                            <input type="hidden" name="konten_id" value="{{ $konten->id }}">
                                            <input type="file" name="file_path" id="input-guru-{{ $konten->id }}" class="hidden" accept="image/*" onchange="document.getElementById('quick-upload-guru-{{ $konten->id }}').submit()">
                                            <div onclick="document.getElementById('input-guru-{{ $konten->id }}').click()" class="w-full h-full bg-white border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center cursor-pointer hover:border-blue-400 transition group/plus">
                                                <span class="text-2xl text-gray-300 group-hover/plus:text-blue-500 font-light">+</span>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-black">{{ $konten->judul }}</h3>
                                    <p class="text-gray-500 text-sm mb-4">{{ $konten->isi }}</p>
                                    <div class="flex gap-2">
                                        <button onclick="openEditModal('{{ $konten->id }}', '{{ addslashes($konten->judul) }}', {{ json_encode($konten->isi) }}, '{{ $kat->nama }}', '')" class="bg-[#F9A825] hover:bg-orange-600 text-white px-5 py-1.5 rounded-md text-sm font-bold flex items-center gap-2 transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.konten.destroy', $konten->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Data Guru')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-[#FF3B30] hover:bg-red-700 text-white px-5 py-1.5 rounded-md text-sm font-bold flex items-center gap-2 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                {{-- 2. INFORMASI PPDB --}}
                @elseif($kat->nama === 'Informasi PPDB')
                    @foreach($kat->konten as $konten)
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold text-black">{{ $konten->judul }}</h3>
                            <div>
                                <h4 class="text-lg font-bold text-black mb-2">Syarat Pendaftaran :</h4>
                                <div class="text-gray-700 leading-relaxed whitespace-pre-line text-base">
                                    {{ $konten->isi }}
                                </div>
                            </div>
                            <div class="pt-4">
                                <button onclick="openEditModal('{{ $konten->id }}', '{{ addslashes($konten->judul) }}', {{ json_encode($konten->isi) }}, '{{ $kat->nama }}', '')" 
                                        class="bg-[#FFB300] hover:bg-orange-500 text-white px-6 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition shadow-sm">
                                    Edit
                                </button>
                            </div>
                        </div>
                    @endforeach

                {{-- 3. KHUSUS BERANDA (STRUKTUR GAMBAR DI KANAN) --}}
                @elseif($kat->nama === 'Beranda')
                    <div class="space-y-10">
                        @foreach($kat->konten as $konten)
                            <div class="flex flex-col md:flex-row justify-between items-start gap-8 py-6 border-b last:border-0">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-black mb-2">{{ $konten->judul }}</h3>
                                    <p class="text-gray-600 text-base leading-relaxed mb-6 whitespace-pre-line text-justify">
                                        {{ $konten->isi }}
                                    </p>
                                    <button onclick="openEditModal('{{ $konten->id }}', '{{ addslashes($konten->judul) }}', {{ json_encode($konten->isi) }}, '{{ $kat->nama }}', '{{ $konten->media->first() ? asset('storage/' . $konten->media->first()->file_path) : '' }}')" 
                                            class="bg-[#F9A825] hover:bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition shadow-sm">
                                        Edit
                                    </button>
                                </div>

                                <div class="w-full md:w-80 flex-shrink-0">
                                    @php $fotoBeranda = $konten->media->first(); @endphp
                                    @if($fotoBeranda)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $fotoBeranda->file_path) }}" class="w-full h-auto rounded-xl shadow-md border border-gray-100 object-cover">
                                            <form action="{{ route('admin.konten_media.destroy', $fotoBeranda->id) }}" method="POST" class="absolute -top-2 -right-2 hidden group-hover:block" onsubmit="return confirmDelete(event, 'Gambar Beranda')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg">&times;</button>
                                            </form>
                                        </div>
                                    @else
                                        <form action="{{ route('admin.konten_media.store') }}" method="POST" enctype="multipart/form-data" id="upload-beranda-{{ $konten->id }}">
                                            @csrf
                                            <input type="hidden" name="konten_id" value="{{ $konten->id }}">
                                            <input type="file" name="file_path" class="hidden" id="input-beranda-{{ $konten->id }}" onchange="this.form.submit()">
                                            <div onclick="document.getElementById('input-beranda-{{ $konten->id }}').click()" class="w-full h-44 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer hover:border-blue-400 transition">
                                                <span class="text-3xl text-gray-300">+</span>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                {{-- 4. KATEGORI UMUM LAINNYA (EKSTRAKURIKULER, TENTANG SEKOLAH, DLL) --}}
                @else
                    <div class="space-y-10"> 
                        @foreach($kat->konten as $konten)
                            <div class="flex flex-col md:flex-row justify-between items-start gap-8 py-6 border-b last:border-0">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-black mb-1">{{ $konten->judul }}</h3>
                                    <p class="text-gray-500 text-sm leading-relaxed text-justify mb-6 whitespace-pre-line">
                                        {{ $konten->isi }}
                                    </p>
                                    
                                    <div class="flex gap-3">
                                        <button onclick="openEditModal('{{ $konten->id }}', '{{ addslashes($konten->judul) }}', {{ json_encode($konten->isi) }}, '{{ $kat->nama }}', '')" class="bg-[#F9A825] hover:bg-orange-600 text-white px-4 py-1.5 rounded-md text-sm font-bold flex items-center gap-2 transition">
                                            Edit
                                        </button>
                                        @if($kat->nama !== 'Tentang Sekolah')
                                        <form action="{{ route('admin.konten.destroy', $konten->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $kat->nama }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-[#FF3B30] hover:bg-red-700 text-white px-4 py-1.5 rounded-md text-sm font-bold flex items-center gap-2 transition">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>

                                @if($kat->nama === 'Ekstrakurikuler')
                                <div class="flex flex-wrap gap-4 mt-4 md:mt-0">
                                    @foreach($konten->media as $m)
                                        <div class="w-24 h-32 relative group">
                                            <img src="{{ asset('storage/' . $m->file_path) }}" class="w-full h-full object-cover rounded-lg border shadow-sm">
                                            <form action="{{ route('admin.konten_media.destroy', $m->id) }}" method="POST" class="absolute -top-2 -right-2 hidden group-hover:block" onsubmit="return confirmDelete(event, 'Gambar')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md">&times;</button>
                                            </form>
                                        </div>
                                    @endforeach
                                    <form action="{{ route('admin.konten_media.store') }}" method="POST" enctype="multipart/form-data" id="quick-upload-{{ $konten->id }}">
                                        @csrf
                                        <input type="hidden" name="konten_id" value="{{ $konten->id }}">
                                        <input type="file" name="file_path" id="input-quick-{{ $konten->id }}" class="hidden" onchange="document.getElementById('quick-upload-{{ $konten->id }}').submit()">
                                        <div onclick="document.getElementById('input-quick-{{ $konten->id }}').click()" class="w-24 h-32 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-blue-400 transition text-gray-400 text-3xl">+</div>
                                    </form>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>
            @endforeach
        </main>
    </div>

    {{-- MODAL TAMBAH & EDIT (TIDAK ADA PERUBAHAN) --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div id="modalTambahBackdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="modalTambahContent" class="bg-white w-full max-w-2xl rounded-2xl p-8 shadow-2xl relative transform scale-95 opacity-0 transition-all duration-300 z-10">
            <h2 id="modalTambahTitle" class="text-2xl font-bold text-gray-900 text-center mb-8">Tambah Konten</h2>
            <form action="{{ route('admin.konten.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="kategori_konten_id" id="tambahKategoriID">
                <div>
                    <label id="labelJudul" class="block text-gray-900 text-sm font-bold mb-2">Judul</label>
                    <input type="text" id="inputJudul" name="judul" class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100">
                    <div id="customDropdownContainer" class="hidden relative">
                        <input type="hidden" id="hiddenSelectValue" name="judul_select">
                        <div class="border border-gray-300 rounded-xl px-4 py-3 flex justify-between items-center cursor-pointer bg-white" id="customSelectTrigger">
                            <span id="customSelectText">-- Pilih Bagian --</span>
                            <svg id="dropdownArrow" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div id="customSelectOptions" class="absolute w-full z-30 bg-white border border-gray-200 rounded-xl mt-2 shadow-xl hidden"></div>
                    </div>
                </div>
                <div>
                    <label id="labelIsi" class="block text-gray-900 text-sm font-bold mb-2">Isi Penjelasan</label>
                    <textarea name="isi" id="inputIsi" rows="8" class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none outline-none focus:ring-2 focus:ring-blue-100" required></textarea>
                </div>
                <div id="tambahFotoGroup">
                    <label id="labelFoto" class="block text-gray-900 text-sm font-bold mb-2">Foto Utama</label>
                    <input type="file" name="file_utama" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModalAnimation('modalTambah')" class="px-6 py-2.5 border border-gray-300 rounded-xl font-bold hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-[#003366] text-white rounded-xl font-bold hover:bg-blue-900 transition shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div id="modalEditBackdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="modalEditContent" class="bg-white w-full max-w-2xl rounded-2xl p-8 shadow-2xl relative transform scale-95 opacity-0 transition-all duration-300 z-10">
            <h2 id="modalEditTitle" class="text-2xl font-bold text-gray-900 text-center mb-8">Edit Konten</h2>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div>
                    <label id="labelEditJudul" class="block text-gray-900 text-sm font-bold mb-2">Judul</label>
                    <input type="text" id="editJudul" name="judul" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-100 outline-none">
                </div>
                <div>
                    <label id="labelEditIsi" class="block text-gray-900 text-sm font-bold mb-2">Isi Penjelasan</label>
                    <textarea name="isi" id="editIsi" rows="8" class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none outline-none focus:ring-2 focus:ring-blue-100" required></textarea>
                </div>
                <div id="editFotoGroup" class="space-y-3">
                    <div id="previewContainer" class="hidden">
                        <label id="labelEditFotoLama" class="block text-gray-900 text-sm font-bold mb-2">Foto Saat Ini</label>
                        <img id="imgPreview" src="" class="w-40 h-auto rounded-lg border shadow-sm mb-2">
                    </div>
                    <label id="labelEditFoto" class="block text-gray-900 text-sm font-bold mb-2">Ganti Foto (Opsional)</label>
                    <input type="file" name="file_utama" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModalAnimation('modalEdit')" class="px-6 py-2.5 border border-gray-300 rounded-xl font-bold hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-[#003366] text-white rounded-xl font-bold hover:bg-blue-900 transition shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT (TIDAK ADA PERUBAHAN) --}}
    <script>
    function confirmDelete(event, itemName) {
        event.preventDefault();
        const form = event.target;
        Swal.fire({
            title: 'Hapus ' + itemName,
            text: 'Yakin untuk menghapus ' + itemName.toLowerCase() + '?',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: false, 
            buttonsStyling: true,
            customClass: {}
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    function openModalAnimation(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        setTimeout(() => {
            document.getElementById(modalId + 'Backdrop').classList.remove('opacity-0');
            const content = document.getElementById(modalId + 'Content');
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModalAnimation(modalId) {
        const modal = document.getElementById(modalId);
        if(!modal) return;
        document.getElementById(modalId + 'Backdrop').classList.add('opacity-0');
        const content = document.getElementById(modalId + 'Content');
        content.classList.replace('scale-100', 'scale-95');
        content.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function enableAutoNumbering(textareaId) {
        const textarea = document.getElementById(textareaId);
        if(!textarea) return;
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const cursorPos = this.selectionStart;
                const textBeforeCursor = this.value.substring(0, cursorPos);
                const lines = textBeforeCursor.split('\n');
                const lastLine = lines[lines.length - 1];
                const match = lastLine.match(/^(\d+)\.\s*/);
                if (match) {
                    e.preventDefault();
                    const nextNum = parseInt(match[1]) + 1;
                    const newLineStr = `\n${nextNum}. `;
                    const textAfterCursor = this.value.substring(cursorPos);
                    this.value = textBeforeCursor + newLineStr + textAfterCursor;
                    const newPos = cursorPos + newLineStr.length;
                    this.setSelectionRange(newPos, newPos);
                }
            }
        });
    }
    enableAutoNumbering('inputIsi');
    enableAutoNumbering('editIsi');

    function openTambahModal(id, kategoriNama, options = []) {
        document.getElementById('tambahKategoriID').value = id;
        const modalTitle = document.getElementById('modalTambahTitle');
        const labelJudul = document.getElementById('labelJudul');
        const labelIsi = document.getElementById('labelIsi');
        const labelFoto = document.getElementById('labelFoto');
        const fotoGroup = document.getElementById('tambahFotoGroup');
        const inputIsi = document.getElementById('inputIsi');
        const dropdown = document.getElementById('customDropdownContainer');
        const inputTeks = document.getElementById('inputJudul');
        const hiddenInput = document.getElementById('hiddenSelectValue');

        modalTitle.innerText = "Tambah Konten";
        labelJudul.innerText = "Judul";
        labelIsi.innerText = "Isi Penjelasan";
        labelFoto.innerText = "Foto Utama";
        fotoGroup.classList.remove('hidden');
        inputIsi.value = '';

        if (kategoriNama === 'Tenaga Pengajar') {
            modalTitle.innerText = "Tambah Tenaga Pengajar";
            labelJudul.innerText = "Nama Guru";
            labelIsi.innerText = "Jabatan";
            labelFoto.innerText = "Foto Guru";
        } else if (kategoriNama === 'Ekstrakurikuler') {
            modalTitle.innerText = "Tambah Ekstrakurikuler";
            labelJudul.innerText = "Nama Ekstrakurikuler";
            labelIsi.innerText = "Deskripsi";
        } else if (kategoriNama === 'Informasi PPDB') {
            modalTitle.innerText = "Tambah Informasi PPDB";
            labelJudul.innerText = "Judul";
            labelIsi.innerText = "Syarat Pendaftaran";
            fotoGroup.classList.add('hidden'); 
            inputIsi.value = "1. "; 
        }

        if (kategoriNama === 'Tentang Sekolah') {
            inputTeks.classList.add('hidden');
            inputTeks.removeAttribute('name');
            dropdown.classList.remove('hidden');
            hiddenInput.setAttribute('name', 'judul');
            fotoGroup.classList.add('hidden');
            const optionsList = document.getElementById('customSelectOptions');
            const selectText = document.getElementById('customSelectText');
            const arrow = document.getElementById('dropdownArrow');
            optionsList.innerHTML = '';
            options.forEach(opt => {
                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer text-gray-700 border-b last:border-0';
                item.innerText = opt;
                item.onclick = (e) => {
                    e.stopPropagation();
                    hiddenInput.value = opt;
                    selectText.innerText = opt;
                    optionsList.classList.add('hidden');
                    arrow.style.transform = 'rotate(0deg)';
                };
                optionsList.appendChild(item);
            });
            document.getElementById('customSelectTrigger').onclick = (e) => {
                e.stopPropagation();
                const isHidden = optionsList.classList.toggle('hidden');
                arrow.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
            };
        } else {
            inputTeks.classList.remove('hidden');
            inputTeks.setAttribute('name', 'judul');
            dropdown.classList.add('hidden');
            hiddenInput.removeAttribute('name');
        }
        openModalAnimation('modalTambah');
    }

    function openEditModal(id, judul, isi, kategoriNama, imageUrl) {
        const form = document.getElementById('formEdit');
        form.action = `/admin/konten/${id}`; 
        const modalTitle = document.getElementById('modalEditTitle');
        const labelJudul = document.getElementById('labelEditJudul');
        const labelIsi = document.getElementById('labelEditIsi');
        const fotoGroup = document.getElementById('editFotoGroup');
        const inputJudul = document.getElementById('editJudul');
        const inputIsi = document.getElementById('editIsi');

        fotoGroup.classList.add('hidden');
        inputJudul.readOnly = false;
        inputJudul.classList.remove('bg-gray-100');

        if (kategoriNama === 'Tenaga Pengajar') {
            modalTitle.innerText = "Edit Tenaga Pengajar";
            labelJudul.innerText = "Nama Guru";
            labelIsi.innerText = "Jabatan";
        } else if (kategoriNama === 'Informasi PPDB') {
            modalTitle.innerText = "Edit Informasi PPDB";
            labelJudul.innerText = "Judul";
            labelIsi.innerText = "Syarat Pendaftaran";
        } else if (kategoriNama === 'Ekstrakurikuler') {
            modalTitle.innerText = "Edit Ekstrakurikuler";
            labelJudul.innerText = "Nama Ekstrakurikuler";
            labelIsi.innerText = "Deskripsi";
        } else if (kategoriNama === 'Tentang Sekolah') {
            modalTitle.innerText = "Edit Bagian " + judul;
            inputJudul.readOnly = true;
            inputJudul.classList.add('bg-gray-100');
        } else if (kategoriNama === 'Beranda') {
            modalTitle.innerText = "Edit Konten Beranda";
            fotoGroup.classList.remove('hidden');
            const previewContainer = document.getElementById('previewContainer');
            const imgPreview = document.getElementById('imgPreview');
            if (imageUrl) {
                imgPreview.src = imageUrl;
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
            }
        }
        inputJudul.value = judul;
        inputIsi.value = isi;
        openModalAnimation('modalEdit');
    }

    window.onclick = (e) => {
        if (e.target.id && e.target.id.includes('Backdrop')) {
            closeModalAnimation('modalTambah');
            closeModalAnimation('modalEdit');
        }
    };
    </script>

@endsection