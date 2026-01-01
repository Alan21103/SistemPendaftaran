<x-guest-layout>
    @php
        // 1. Logika Tahun Pelajaran Otomatis (Kunci Minimal 2026)
        $bulanSekarang = (int)date('n');
        $tahunSekarang = (int)date('Y');
        $tahunDasar = ($bulanSekarang < 7) ? ($tahunSekarang - 1) : $tahunSekarang;
        $tahunMulai = max(2026, $tahunDasar);
        $tahunSelesai = $tahunMulai + 1;
        $tahunAjaran = $tahunMulai . '/' . $tahunSelesai;

        // --- PERBAIKAN DI SINI: MENGURUTKAN TANGGAL TERLAMA KE TERBARU ---
        if (isset($riwayat_cicilan)) {
            $riwayat_cicilan = $riwayat_cicilan->sortBy('tanggal_bayar')->values();
        }
        // ---------------------------------------------------------------

        // 2. Logika Perhitungan & Warna Tagihan
        if (isset($tagihan)) {
            $total_seharusnya = $rincian_biaya->sum('jumlah_biaya');
            
            // Total yang muncul di ringkasan (Confirmed + Menunggu agar sesuai dengan riwayat)
            $sudah_dibayar_riwayat = $riwayat_cicilan->whereIn('status_konfirmasi', ['Dikonfirmasi', 'Menunggu Verifikasi'])->sum('nominal_bayar');
            
            // Sisa tagihan riil (Total - yang sudah dikonfirmasi saja untuk accounting)
            $sudah_dikonfirmasi = $riwayat_cicilan->where('status_konfirmasi', 'Dikonfirmasi')->sum('nominal_bayar');
            $sisa_tagihan_riil = $total_seharusnya - $sudah_dikonfirmasi;

            // Cek status untuk warna
            $ada_menunggu = $riwayat_cicilan->where('status_konfirmasi', '!=', 'Dikonfirmasi')->isNotEmpty();

            // Logika Warna berdasarkan permintaan:
            if ($sudah_dibayar_riwayat == 0) {
                $warnaStatus = 'text-red-600'; // Belum ada bayar sama sekali = Merah
            } elseif ($ada_menunggu) {
                $warnaStatus = 'text-yellow-500'; // Ada yang menunggu = Kuning
            } else {
                $warnaStatus = 'text-green-600'; // Sudah dikonfirmasi atau Lunas = Hijau
            }
        }
    @endphp

    <div class="min-h-screen bg-gray-100 py-12 px-4">
        {{-- HEADER --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-black">Pembayaran Biaya Sekolah</h1>
            <p class="text-lg text-gray-600 mt-2">SD Muhammadiyah 2 Ambarketawang - Tahun Pelajaran {{ $tahunAjaran }}</p>
        </div>

        {{-- CARD PUTIH --}}
        <div class="max-w-4xl mx-auto bg-white rounded-[2rem] shadow-xl p-8 lg:p-12">
            <h2 class="text-lg font-bold text-gray-800 mb-6">Unggah Bukti Pembayaran</h2>

            @if(isset($tagihan))
                {{-- 1. RINCIAN ADMINISTRASI --}}
                <div class="mb-10">
                    <h3 class="text-md font-bold text-gray-800 mb-4">Rincian Administrasi Sistem Penerimaan Murid Baru {{ $tahunAjaran }} :</h3>
                    <p class="text-center font-bold text-gray-700 mb-4 text-sm">
                        Siswa: {{ $rincian_biaya->where('jenis_kelamin', '!=', 'Umum')->first()->jenis_kelamin ?? '—' }}
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-gray-700">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 text-left w-12">No</th>
                                    <th class="py-2 text-left">Uraian</th>
                                    <th class="py-2 text-right">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($rincian_biaya as $index => $item)
                                    <tr>
                                        <td class="py-3">{{ $index + 1 }}</td>
                                        <td class="py-3">{{ $item->uraian }}</td>
                                        <td class="py-3 text-right font-medium">Rp. {{ number_format($item->jumlah_biaya, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-bold text-gray-900 border-t">
                                    <td colspan="2" class="py-4 text-right pr-4">Jumlah</td>
                                    <td class="py-4 text-right">Rp. {{ number_format($total_seharusnya, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- 2. GRID RINGKASAN & RIWAYAT --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div class="bg-white border border-gray-100 shadow-md rounded-xl p-6">
                        <h4 class="font-bold text-gray-800 mb-4">Ringkasan Tagihan</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-500">
                                <span>Total Tagihan</span>
                                <span class="font-bold">Rp. {{ number_format($total_seharusnya, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between {{ $warnaStatus }}">
                                <span class="font-medium">Total Sudah Dibayar</span>
                                <span class="font-bold">Rp. {{ number_format($sudah_dibayar_riwayat, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between bg-gray-100 p-3 rounded-lg font-bold mt-4">
                                <span>Sisa Tagihan</span>
                                <span>Rp. {{ number_format($sisa_tagihan_riil, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 shadow-md rounded-xl p-6">
                        <h4 class="font-bold text-gray-800 mb-4">Riwayat Cicilan</h4>
                        <div class="max-h-40 overflow-y-auto space-y-3">
                            @forelse($riwayat_cicilan as $index => $cicilan)
                                <div class="border-b pb-2 text-xs">
                                    <div class="flex justify-between items-start mb-1">
                                        <div>
                                            <p class="font-bold text-gray-800">Cicilan {{ $index + 1 }}</p>
                                            <p class="text-gray-400">{{ \Carbon\Carbon::parse($cicilan->tanggal_bayar)->format('d/m/Y') }}</p>
                                        </div>
                                        <span class="{{ $cicilan->status_konfirmasi == 'Dikonfirmasi' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} px-2 py-0.5 rounded-full text-[10px] font-bold">
                                            {{ $cicilan->status_konfirmasi == 'Dikonfirmasi' ? 'Diterima' : 'Menunggu' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-bold text-sm">Rp. {{ number_format($cicilan->nominal_bayar, 0, ',', '.') }}</span>
                                        <button type="button" onclick="openModalBukti('{{ asset('storage/' . $cicilan->bukti_transfer) }}')" class="text-blue-500 hover:underline">Lihat Bukti</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-400 italic py-4 text-xs">Belum ada cicilan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 3. FORM PEMBAYARAN --}}
                @if($sisa_tagihan_riil > 0)
                    <div class="border-t pt-8">
                        <h3 class="text-md font-bold text-gray-800 mb-4">Bayar Cicilan Berikutnya</h3>
                        <div class="mb-6 ml-1 text-sm">
                            <p class="text-gray-500 mb-1">Transfer ke : <span class="font-bold text-gray-700 underline">BCA A.N Muhammad Hanif</span></p>
                            <p class="text-gray-500">No. Rekening : <span class="font-bold text-gray-700 underline">0374371025</span></p>
                        </div>

                        <form id="paymentForm" action="{{ route('pembayaran.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validatePaymentForm()">
                            @csrf
                            <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Pembayaran</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">Rp.</span>
                                    </div>
                                    <input type="text" id="nominal_display" required
                                        class="block w-full rounded-md border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 font-normal text-gray-800" 
                                        placeholder="0"
                                        onkeyup="formatRupiah(this)">
                                    <input type="hidden" name="nominal_bayar" id="nominal_asli">
                                </div>
                                <p class="mt-1.5 text-[11px] text-gray-400 italic font-medium">
                                    * Sisa tagihan: Rp. {{ number_format($sisa_tagihan_riil, 0, ',', '.') }}
                                </p>
                                <p id="error-nominal" class="hidden mt-1 text-[11px] text-red-600 font-bold italic">! Mohon isi nominal.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Upload Bukti Transfer</label>
                                <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50 group cursor-pointer" onclick="document.getElementById('bukti_input').click()">
                                    <p id="file_name" class="text-xs text-gray-400 uppercase tracking-tighter mb-3">Format : Jpg, Jpeg, Png, Pdf</p>
                                    <button type="button" class="bg-[#003366] text-white px-5 py-1.5 rounded-md text-xs font-medium hover:bg-blue-900 transition">Pilih File</button>
                                    <input type="file" name="bukti_transfer" id="bukti_input" class="hidden" accept="image/*,.pdf" onchange="handleFileSelect(this)">
                                </div>
                                <p id="error-file" class="hidden mt-2 text-[11px] text-red-600 font-bold italic text-center">! Bukti transfer wajib diunggah.</p>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="bg-[#003366] text-white px-10 py-2 rounded-lg font-bold shadow-lg hover:bg-blue-950 transition">
                                    Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-green-600 p-8 rounded-2xl text-center text-white shadow-lg animate-pulse">
                        <h3 class="text-xl font-bold mb-2">Administrasi Lunas</h3>
                        <p class="opacity-90">Terima kasih, pembayaran telah terpenuhi.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- MODAL PREVIEW --}}
    <div id="modalBukti" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm" onclick="closeModalBukti()">
        <div class="bg-white w-full max-w-xl rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
            <div class="p-4 bg-gray-50 flex justify-center" id="modalBody"></div>
            <div class="p-4 text-right border-t">
                <button onclick="closeModalBukti()" class="px-6 py-2 bg-gray-800 text-white rounded-lg text-sm font-bold">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function validatePaymentForm() {
            const fileInput = document.getElementById('bukti_input');
            const nominalInput = document.getElementById('nominal_asli');
            const errorFile = document.getElementById('error-file');
            const errorNominal = document.getElementById('error-nominal');
            const dropArea = document.getElementById('drop-area');
            let isValid = true;

            if (!nominalInput.value || nominalInput.value <= 0) {
                errorNominal.classList.remove('hidden');
                isValid = false;
            } else { errorNominal.classList.add('hidden'); }

            if (fileInput.files.length === 0) {
                errorFile.classList.remove('hidden');
                dropArea.classList.add('border-red-500', 'bg-red-50');
                isValid = false;
            } else { 
                errorFile.classList.add('hidden');
                dropArea.classList.remove('border-red-500', 'bg-red-50');
            }
            return isValid;
        }

        function handleFileSelect(input) {
            const fileNameText = document.getElementById('file_name');
            if (input.files.length > 0) {
                fileNameText.innerText = 'File: ' + input.files[0].name;
                fileNameText.classList.replace('text-gray-400', 'text-blue-700');
                document.getElementById('error-file').classList.add('hidden');
                document.getElementById('drop-area').classList.remove('border-red-500', 'bg-red-50');
                document.getElementById('drop-area').classList.add('border-green-500', 'bg-green-50');
            }
        }

        function formatRupiah(input) {
            let value = input.value.replace(/[^,\d]/g, "").toString();
            let split = value.split(",");
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                let separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }
            input.value = rupiah;
            document.getElementById('nominal_asli').value = value;
            if(value > 0) document.getElementById('error-nominal').classList.add('hidden');
        }

        function openModalBukti(fileUrl) {
            const body = document.getElementById('modalBody');
            const isPdf = fileUrl.toLowerCase().endsWith('.pdf');
            body.innerHTML = isPdf ? `<embed src="${fileUrl}" type="application/pdf" class="w-full h-[60vh]">` : `<img src="${fileUrl}" class="max-w-full h-auto max-h-[70vh] rounded-lg">`;
            document.getElementById('modalBukti').classList.replace('hidden', 'flex');
        }
        function closeModalBukti() { document.getElementById('modalBukti').classList.replace('flex', 'hidden'); }
    </script>
</x-guest-layout>