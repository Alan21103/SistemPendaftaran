<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminPendaftaranController extends Controller
{
    /**
     * Array nama kolom file dokumen.
     * @var array
     */
    protected array $documentFields = ['kk', 'akte', 'foto', 'ijazah_sk', 'bukti_bayar'];

    /**
     * Menampilkan daftar semua pendaftaran.
     */
    public function index(Request $request)
    {
        $pendaftarQuery = Pendaftaran::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $pendaftarQuery->where(function ($query) use ($search) {
                $query->where('nama_siswa', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhere('asal_sekolah', 'like', "%$search%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $pendaftarQuery->where('status', strtolower($request->input('status')));
        }

        // Asal sekolah filter
        if ($request->filled('asal_sekolah') && $request->input('asal_sekolah') !== 'all') {
            $pendaftarQuery->where('asal_sekolah', $request->input('asal_sekolah'));
        }

        // Sort / urutan toggle
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'nama_asc':
                $pendaftarQuery->orderBy('nama_siswa', 'asc');
                break;
            case 'nama_desc':
                $pendaftarQuery->orderBy('nama_siswa', 'desc');
                break;
            case 'tanggal_asc':
                $pendaftarQuery->orderBy('created_at', 'asc');
                break;
            case 'tanggal_desc':
                $pendaftarQuery->orderBy('created_at', 'desc');
                break;
            default:
                $pendaftarQuery->latest('id_pendaftaran');
        }

        $pendaftarans = $pendaftarQuery->paginate(15)->withQueryString();

        // Daftar unik asal sekolah
        $list_sekolah = Pendaftaran::select('asal_sekolah')
            ->distinct()
            ->whereNotNull('asal_sekolah')
            ->pluck('asal_sekolah')
            ->sort()
            ->toArray();

        $list_status = ['pending', 'diterima', 'ditolak'];

        return view('admin.data_pendaftar', compact('pendaftarans', 'list_sekolah', 'list_status', 'sortBy'));
    }


    /**
     * Menampilkan detail lengkap satu pendaftaran.
     */
    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load('user');
        return view('admin.detail_pendaftar', compact('pendaftaran'));
    }

    /**
     * Mengunduh atau menampilkan file dokumen dari pendaftaran berdasarkan parameter 'action'.
     */
    public function download(Request $request, Pendaftaran $pendaftaran, string $field)
    {
        if (!in_array($field, $this->documentFields)) {
            abort(404, 'Dokumen yang diminta tidak valid.');
        }

        $filePath = $pendaftaran->$field;

        if (empty($filePath) || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ada atau telah dihapus.');
        }

        $action = $request->query('action', 'download');

        if ($action === 'view') {
            return Storage::disk('public')->response($filePath);
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Approve pendaftaran
     */
    public function approve(Pendaftaran $pendaftaran)
    {
        // Set ID Admin yang melakukan perubahan
        $pendaftaran->id_admin = Auth::id();

        $pendaftaran->status = 'diterima';
        $pendaftaran->save();

        // Tambahkan 'message' agar dibaca oleh SweetAlert
        return response()->json([
            'success' => true,
            'status' => 'diterima',
            'message' => 'Status berhasil diubah menjadi: Diterima'
        ]);
    }

    /**
     * Reject pendaftaran
     */
    public function reject(Pendaftaran $pendaftaran, Request $request)
    {
        $request->validate([
            'alasan' => 'nullable|string'
        ]);

        // Set ID Admin yang melakukan perubahan
        $pendaftaran->id_admin = Auth::id();

        $pendaftaran->status = 'ditolak';

        if ($request->filled('alasan')) {
            $pendaftaran->alasan = $request->alasan;
        }

        $pendaftaran->save();

        // Tambahkan 'message' agar dibaca oleh SweetAlert
        return response()->json([
            'success' => true,
            'status' => 'ditolak',
            'message' => 'Status berhasil diubah menjadi: Ditolak'
        ]);
    }


    /**
     * Update status pendaftaran (dari form atau AJAX)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Diterima,Ditolak',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);

        // Cek jika status berubah (atau jika tidak ingin mencatat admin saat status tetap 'Pending')
        if ($pendaftaran->status !== $validated['status']) {
            // 1. Set ID Admin yang melakukan perubahan
            $pendaftaran->id_admin = Auth::id(); // Ambil ID pengguna (Admin) yang sedang login
        }

        $pendaftaran->status = $validated['status'];
        $pendaftaran->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pendaftaran berhasil diperbarui',
                'status' => $pendaftaran->status,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Status pendaftaran berhasil diubah menjadi ' . $pendaftaran->status);
    }
}