<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminPembayaranController extends Controller
{
    /**
     * Menampilkan daftar semua tagihan (Kelola Pembayaran).
     * Disesuaikan dengan pola AdminPendaftaranController@index
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan Relasi (Eager Loading)
        $pembayaranQuery = Tagihan::with(['pendaftaran.user', 'pembayaran']);

        // 2. Filter Search (Nama Siswa, NISN, atau Asal Sekolah)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $pembayaranQuery->whereHas('pendaftaran', function ($query) use ($search) {
                $query->where('nama_siswa', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhere('asal_sekolah', 'like', "%$search%")
                    ->orWhere('no_telp', 'like', "%$search%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Filter berdasarkan range tanggal
            $pembayaranQuery->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            // Jika hanya isi tanggal awal, cari yang sama dengan tanggal tersebut
            $pembayaranQuery->whereDate('created_at', $request->start_date);
        }

        // 3. Filter Status (Lunas, Belum Lunas)
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $pembayaranQuery->where('status_pembayaran', strtolower($request->input('status')));
        }

        // 4. Filter Asal Sekolah (Mengambil dari tabel pendaftaran)
        if ($request->filled('asal_sekolah') && $request->input('asal_sekolah') !== 'all') {
            $pembayaranQuery->whereHas('pendaftaran', function ($query) use ($request) {
                $query->where('asal_sekolah', $request->input('asal_sekolah'));
            });
        }

        // 5. Sort / Urutan Toggle
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'nama_asc':
                $pembayaranQuery->whereHas('pendaftaran', function ($q) {
                    $q->orderBy('nama_siswa', 'asc');
                });
                break;
            case 'nama_desc':
                $pembayaranQuery->whereHas('pendaftaran', function ($q) {
                    $q->orderBy('nama_siswa', 'desc');
                });
                break;
            case 'tagihan_high':
                $pembayaranQuery->orderBy('total_tagihan', 'desc');
                break;
            case 'tagihan_low':
                $pembayaranQuery->orderBy('total_tagihan', 'asc');
                break;
            default:
                $pembayaranQuery->latest('id');
        }

        // 6. Pagination (Mengikuti pola pendaftaran: 15 per halaman)
        $datas = $pembayaranQuery->paginate(15)->withQueryString();

        // 7. Data pendukung untuk Filter Dropdown
        $list_sekolah = Pendaftaran::select('asal_sekolah')
            ->distinct()
            ->whereNotNull('asal_sekolah')
            ->pluck('asal_sekolah')
            ->sort()
            ->toArray();

        $list_status = ['Lunas', 'Belum Lunas'];

        return view('admin.pembayaran', compact('datas', 'list_sekolah', 'list_status', 'sortBy'));
    }

    /**
     * Menampilkan detail lengkap satu tagihan dan riwayat pembayarannya.
     */
    public function show($id)
    {
        $tagihan = Tagihan::with(['pendaftaran', 'pembayaran.admin'])->findOrFail($id);
        return view('admin.pembayaran.detail', compact('tagihan'));
    }

    /**
     * Approve/Verifikasi Pembayaran (Pola AJAX seperti approve pendaftaran)
     */
    public function verify($id)
    {
        DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::findOrFail($id);

            // 1. Update status_konfirmasi pada tabel Pembayaran
            $pembayaran->status_konfirmasi = 'Dikonfirmasi';
            $pembayaran->id_admin = Auth::id();
            $pembayaran->save();

            // 2. Ambil data Tagihan terkait
            $tagihan = Tagihan::findOrFail($pembayaran->tagihan_id);

            // 3. Update status_pembayaran jika sisa sudah nol
            if ($tagihan->sisa_tagihan <= 0) {
                $tagihan->status_pembayaran = 'Lunas';
                $tagihan->save();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reject/Tolak Pembayaran (Pola AJAX seperti reject pendaftaran)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $tagihan = Tagihan::findOrFail($pembayaran->tagihan_id);

            // Logika Keamanan: Kembalikan sisa tagihan karena pembayaran ditolak
            $tagihan->increment('sisa_tagihan', $pembayaran->nominal_bayar);
            $tagihan->update(['status_pembayaran' => 'Belum Lunas']);

            $pembayaran->id_admin = Auth::id();
            $pembayaran->status_konfirmasi = 'Ditolak';
            $pembayaran->keterangan_admin = $request->alasan;
            $pembayaran->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran telah ditolak dan saldo dikembalikan.',
                'status' => 'Ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menolak pembayaran.'], 500);
        }
    }

    /**
     * Fungsi Download/View Bukti Transfer (Pola sama dengan download pendaftaran)
     */
    public function downloadBukti(Request $request, Pembayaran $pembayaran)
    {
        $filePath = $pembayaran->bukti_transfer;

        if (empty($filePath) || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File bukti transfer tidak ditemukan.');
        }

        $action = $request->query('action', 'view');

        if ($action === 'view') {
            return Storage::disk('public')->response($filePath);
        }

        return Storage::disk('public')->download($filePath);
    }

    public function viewBukti(Pembayaran $pembayaran)
    {
        $filePath = $pembayaran->bukti_transfer;

        // Cek file di disk public
        if (empty($filePath) || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Bukti transfer tidak ditemukan.');
        }

        // Ambil path lengkap ke folder storage/app/public/...
        $absolutePath = storage_path('app/public/' . $filePath);

        // Menggunakan response file agar browser menampilkan (preview) bukan download
        return response()->file($absolutePath);
    }
}