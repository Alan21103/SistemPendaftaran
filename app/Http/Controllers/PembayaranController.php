<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pendaftaran;
use App\Models\InformasiPembayaran;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login untuk mengakses halaman pembayaran.');
        }

        $id_user = Auth::id();
        $siswa = Pendaftaran::where('id_user', $id_user)->first();

        if (!$siswa) {
            return redirect()->route('pendaftaran.create')->with('warning', 'Anda belum melengkapi formulir pendaftaran.');
        }

        $id_pendaftaran = $siswa->id_pendaftaran;

        // 1. Konversi Jenis Kelamin untuk Rincian Biaya
        $jenis_kelamin_raw = $siswa->jenis_kelamin;
        $jenis_kelamin_key = (strtolower($jenis_kelamin_raw) == 'laki-laki') ? 'Putra' : 'Putri';

        // 2. Mengambil Rincian Biaya
        $rincian_biaya = InformasiPembayaran::where('jenis_kelamin', $jenis_kelamin_key)
            ->orWhere('jenis_kelamin', 'Umum')
            ->orderBy('id')
            ->get();

        if ($rincian_biaya->isEmpty()) {
            return view('pembayaran.index', [
                'rincian_biaya' => collect(),
                'tagihan' => (object) ['total_tagihan' => 0, 'sisa_tagihan' => 0, 'status_pembayaran' => 'Belum Lunas', 'id' => null],
                'riwayat_cicilan' => collect(),
            ])->with('warning', 'Rincian biaya administrasi belum diatur oleh Admin.');
        }

        // 3. Mengambil/Membuat Tagihan
        $tagihan = Tagihan::where('id_pendaftaran', $id_pendaftaran)->first();
        if (!$tagihan) {
            $total_tagihan_default = $rincian_biaya->sum('jumlah_biaya');
            $tagihan = Tagihan::create([
                'id_pendaftaran' => $id_pendaftaran,
                'total_tagihan' => $total_tagihan_default,
                'sisa_tagihan' => $total_tagihan_default,
                'status_pembayaran' => 'Belum Lunas',
            ]);
        } 
        
        // 4. Mengambil Riwayat Cicilan
        $riwayat_cicilan = Pembayaran::where('tagihan_id', $tagihan->id)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        return view('pembayaran.index', compact('rincian_biaya', 'tagihan', 'riwayat_cicilan'));
    }

    protected $documentFields = ['bukti_transfer'];

    public function submit(Request $request)
    {
        // 1. Validasi Input dengan Pesan Bahasa Indonesia
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'nominal_bayar' => 'required|numeric|min:1000',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'nominal_bayar.required' => 'Nominal pembayaran wajib diisi.',
            'nominal_bayar.numeric'  => 'Nominal harus berupa angka.',
            'nominal_bayar.min'      => 'Minimal pembayaran adalah Rp. 1.000.',
            'bukti_transfer.required' => 'Silakan unggah bukti transfer Anda.',
            'bukti_transfer.mimes'    => 'Format file harus JPG, PNG, atau PDF.',
            'bukti_transfer.max'      => 'Ukuran file maksimal adalah 2MB.',
        ]);

        $siswa = Pendaftaran::where('id_user', Auth::id())->first();
        $tagihan = Tagihan::find($request->tagihan_id);

        if (!$siswa || !$tagihan || $tagihan->id_pendaftaran != $siswa->id_pendaftaran) {
            return redirect()->back()->with('error', 'Data tagihan tidak valid.');
        }

        $nominal_bayar = $request->nominal_bayar;
        $sisa_baru = $tagihan->sisa_tagihan - $nominal_bayar;

        if ($sisa_baru < 0) {
            return redirect()->back()->with('error', 'Nominal bayar melebihi sisa tagihan. Sisa Anda: Rp. ' . number_format($tagihan->sisa_tagihan, 0, ',', '.'));
        }

        DB::beginTransaction();
        try {
            $filePaths = $this->uploadDocuments($request);

            Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'nominal_bayar' => $nominal_bayar,
                'tanggal_bayar' => now(),
                'keterangan_cicilan' => ($sisa_baru == 0) ? 'Pelunasan' : 'Cicilan',
                'bukti_transfer' => $filePaths['bukti_transfer'],
                'status_konfirmasi' => 'Menunggu Verifikasi',
            ]);

            $tagihan->update([
                'sisa_tagihan' => $sisa_baru,
                'status_pembayaran' => ($sisa_baru == 0) ? 'Lunas' : 'Belum Lunas'
            ]);

            DB::commit();

            $pesan = ($sisa_baru == 0)
                ? 'Alhamdulillah! Pembayaran Anda sudah LUNAS. Menunggu verifikasi admin.'
                : 'Pembayaran berhasil dikirim. Sisa tagihan Anda: Rp. ' . number_format($sisa_baru, 0, ',', '.');

            return redirect()->route('pembayaran.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollback();
            if (isset($filePaths['bukti_transfer'])) {
                Storage::disk('public')->delete($filePaths['bukti_transfer']);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses data.');
        }
    }

    protected function uploadDocuments(Request $request): array
    {
        $filePaths = [];
        foreach ($this->documentFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileNameToStore = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bukti_pembayaran', $fileNameToStore, 'public');
                $filePaths[$field] = $path;
            }
        }
        return $filePaths;
    }
}