<x-guest-layout>
    {{-- Container Utama Konten --}}
    <div class="max-w-7xl mx-auto py-12 px-6 lg:px-8">

        {{-- Header Halaman --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-black">Status Pendaftaran PPDB</h1>
            {{-- Logika Otomatis: Menampilkan Tahun Sekarang / Tahun Depan --}}
            <p class="text-lg text-gray-600 mt-2">
                SD Muhammadiyah 2 Ambarketawang - Tahun Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}
            </p>
        </div>

        {{-- Logika Tampilan Data Pendaftaran --}}
        @if(isset($pendaftaran) && $pendaftaran)
            @php
                // Logika Penentuan Warna dan Icon berdasarkan Status
                $statusData = match ($pendaftaran->status ?? 'Pending') {
                    'Diterima' => [
                        'color' => 'bg-green-100 text-green-700 border-green-200',
                        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        'label' => 'Diterima'
                    ],
                    'Ditolak' => [
                        'color' => 'bg-red-100 text-red-700 border-red-200',
                        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        'label' => 'Ditolak'
                    ],
                    default => [
                        'color' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        'label' => 'Pending / Diproses'
                    ],
                };
            @endphp

            <div class="max-w-4xl mx-auto">
                
                {{-- CARD UTAMA --}}
                <div class="bg-white shadow-2xl rounded-3xl border border-gray-100 overflow-hidden">
                    
                    {{-- Bagian Atas Card --}}
                    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                        <h2 class="text-xl font-bold text-gray-800">Detail Pendaftaran</h2>
                        
                        {{-- Badge dengan Icon --}}
                        <div class="flex items-center gap-2 px-5 py-2 font-bold text-sm rounded-full border-2 {{ $statusData['color'] }} shadow-sm">
                            {!! $statusData['icon'] !!}
                            <span>{{ $statusData['label'] }}</span>
                        </div>
                    </div>

                    {{-- Bagian Isi Card: Detail Data --}}
                    <div class="p-8 lg:p-10 text-gray-800">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-10">
                            
                            {{-- Kolom Kiri (Label) --}}
                            <div class="font-semibold text-[#002060] space-y-3">
                                <p>Nomor Pendaftaran</p>
                                <p>Nama Lengkap</p>
                                <p>NISN</p>
                                <p>Tanggal Daftar</p>
                                <p>Asal Sekolah</p>
                                <p>Tempat, Tanggal Lahir</p>
                                <p>Jenis Kelamin</p>
                                <p>Alamat</p>
                                <p>Nama Orang Tua</p>
                                <p>Nomor Telepon</p>
                            </div>

                            {{-- Kolom Kanan (Nilai) --}}
                            <div class="space-y-3 text-gray-700">
                                <p class="font-bold text-black">: {{ $pendaftaran->id_pendaftaran ?? '—' }}</p>
                                <p class="font-bold text-black">: {{ $pendaftaran->nama_siswa }}</p>
                                <p>: {{ $pendaftaran->nisn ?? '—' }}</p>
                                <p>: {{ $pendaftaran->created_at ? $pendaftaran->created_at->translatedFormat('l, d F Y') : '—' }}</p>
                                <p>: {{ $pendaftaran->asal_sekolah }}</p>
                                <p>: {{ $pendaftaran->tempat_tgl_lahir }}</p>
                                <p>: {{ $pendaftaran->jenis_kelamin }}</p>
                                <p>: {{ $pendaftaran->alamat }}</p>
                                <p>: {{ $pendaftaran->nama_ayah ?? $pendaftaran->nama_ibu ?? '—' }}</p>
                                <p>: {{ $pendaftaran->no_telp ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-10 pt-8 border-t border-gray-100 flex flex-wrap gap-4 justify-center md:justify-start">
                            @if(($pendaftaran->status ?? 'Pending') == 'Diterima')
                                {{-- Tombol Unduh Bukti Daftar --}}
                                <a href="{{ route('pendaftaran.pdf', $pendaftaran->id_pendaftaran) }}"
                                    class="inline-flex items-center px-8 py-3 bg-[#002060] rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-900 transition shadow-md active:scale-95">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Unduh Bukti Daftar
                                </a>

                                @php
                                    // Ambil ID Tagihan
                                    $tagihanId = $pendaftaran->tagihan->id ?? null;
                                    
                                    // Cari pembayaran yang punya foto_kwitansi berdasarkan tagihan_id tersebut
                                    $pembayaranLunas = null;
                                    if ($tagihanId) {
                                        $pembayaranLunas = \App\Models\Pembayaran::where('tagihan_id', $tagihanId)
                                            ->whereNotNull('foto_kwitansi')
                                            ->where('foto_kwitansi', '!=', '')
                                            ->first();
                                    }
                                @endphp

                                @if($pembayaranLunas)
                                    {{-- Tombol Unduh Kwitansi Lunas --}}
                                    <a href="{{ asset('storage/' . $pembayaranLunas->foto_kwitansi) }}" target="_blank"
                                        class="inline-flex items-center px-8 py-3 bg-emerald-600 rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition shadow-md active:scale-95">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Unduh Kwitansi Lunas
                                    </a>
                                @else
                                    {{-- Tombol Lanjutkan Pembayaran --}}
                                    <a href="{{ route('pembayaran.index', $pendaftaran->id_pendaftaran) }}"
                                        class="inline-flex items-center px-8 py-3 bg-green-600 rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition shadow-md active:scale-95">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                        </svg>
                                        Lanjutkan Pembayaran
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- DATA TIDAK DITEMUKAN --}}
            <div class="max-w-2xl mx-auto p-12 bg-white rounded-3xl shadow-xl text-center border border-gray-100">
                <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Data Tidak Ditemukan</h3>
                <p class="text-gray-600 mt-3 mb-8">Sepertinya Anda belum mengisi formulir pendaftaran PPDB online.</p>
                <a href="{{ route('pendaftaran.create') }}"
                    class="bg-[#002060] hover:bg-blue-950 text-white font-bold py-3 px-10 rounded-full transition shadow-lg active:scale-95">
                    Daftar Sekarang
                </a>
            </div>
        @endif
    </div>
</x-guest-layout>