<?php

namespace App\Http\Controllers\Admin;

use App\Models\Konten;
use App\Models\KontenMedia;
use App\Models\KontenList;
use App\Models\KategoriKonten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AdminKontenController extends Controller
{
    /**
     * Tampilkan halaman admin (Blade)
     */
    public function index()
    {
        // Load kategori dengan relasi konten, media (diurutkan), dan list
        $kategori = KategoriKonten::with(['konten' => function ($query) {
            $query->orderBy('urutan', 'asc');
        }, 'konten.media' => function ($query) {
            $query->orderBy('urutan', 'asc');
        }, 'konten.list'])->get();

        return view('admin.konten', compact('kategori'));
    }

    /**
     * Endpoint JSON untuk ambil data konten tunggal (Untuk modal edit)
     */
    public function json($id)
    {
        // Pastikan relasi 'kategori' dimuat!
        $konten = Konten::with(['kategori', 'media', 'list'])->findOrFail($id);
        
        $media_utama = $konten->media->where('urutan', 0)->first();
        
        $response = $konten->toArray();
        
        // TAMBAHAN PENTING: 
        // Kadang toArray() tidak menyertakan relasi jika modelnya hidden. 
        // Kita paksa masukkan nama kategorinya.
        $response['kategori_nama'] = $konten->kategori ? $konten->kategori->nama : ''; 
        
        $response['file_utama_url'] = $media_utama ? asset('storage/' . $media_utama->file_path) : null;

        return response()->json($response);
    }

    // =======================================================
    // ======== OPERASI KONTEN UTAMA (STORE, UPDATE, DESTROY) ========
    // =======================================================

    /**
     * Simpan konten baru + media utama (file_utama)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_konten_id' => 'required|exists:kategori_konten,id',
            'judul' => 'required|string|max:255',
            'sub_judul' => 'nullable|string|max:255',
            'isi' => 'required|string',
            'file_utama' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048', 
        ]);

        $urutan_baru = Konten::where('kategori_konten_id', $validated['kategori_konten_id'])->max('urutan') + 1;

        $konten = Konten::create([
            'kategori_konten_id' => $validated['kategori_konten_id'],
            'judul' => $validated['judul'],
            'sub_judul' => $validated['sub_judul'] ?? null,
            'isi' => $validated['isi'],
            'urutan' => $urutan_baru,
            'id_admin' => auth()->id(),
        ]);

        // SIMPAN MEDIA UTAMA (urutan 0)
        if ($request->hasFile('file_utama')) {
            $file = $request->file('file_utama');
            $path = $file->store('konten_media', 'public');

            KontenMedia::create([
                'konten_id' => $konten->id,
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'urutan' => 0 // Urutan 0 untuk foto utama
            ]);
        }
        
        return redirect()->back()->with('success', 'Konten berhasil ditambahkan.');
    }

    /**
     * Update konten + ganti media utama
     */
    public function update(Request $request, $id)
    {
        $konten = Konten::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'sub_judul' => 'nullable|string|max:255',
            'isi' => 'required|string',
            'file_utama' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048', 
        ]);

        $konten->update([
            'judul' => $validated['judul'],
            'sub_judul' => $validated['sub_judul'] ?? null,
            'isi' => $validated['isi'],
        ]);

        // GANTI MEDIA UTAMA (urutan 0)
        if ($request->hasFile('file_utama')) {
            $file = $request->file('file_utama');
            $media_utama = $konten->media()->where('urutan', 0)->first();
            
            // Hapus media lama
            if ($media_utama) {
                if (Storage::disk('public')->exists($media_utama->file_path)) {
                    Storage::disk('public')->delete($media_utama->file_path);
                }
                $media_utama->delete();
            }

            // Simpan media baru
            $path = $file->store('konten_media', 'public');
            KontenMedia::create([
                'konten_id' => $konten->id,
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'urutan' => 0 
            ]);
        }

        return redirect()->back()->with('success', 'Konten berhasil diupdate.');
    }

    /**
     * Hapus konten (dan semua media/list terkait)
     */
    public function destroy($id)
    {
        $konten = Konten::findOrFail($id);

        // Hapus semua media terkait
        foreach ($konten->media as $media) {
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }
        }
        
        $konten->list()->delete();
        $konten->delete();

        return redirect()->back()->with('success', 'Konten berhasil dihapus.');
    }
    
    // =======================================================
    // ======== OPERASI MEDIA TAMBAHAN (STORE & DESTROY) ========
    // =======================================================

    /**
     * Simpan media tambahan untuk konten.
     */
    public function storeMedia(Request $request)
    {
        $validated = $request->validate([
            'konten_id' => 'required|exists:konten,id',
            'file_path' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:10000', 
            'urutan' => 'nullable|integer|min:1', // Urutan media tambahan harus > 0
        ]);
        
        $file = $request->file('file_path');
        $path = $file->store('konten_media', 'public');
        
        // Cari urutan tertinggi di konten ini (urutan > 0) + 1 jika urutan kosong/0
        $max_urutan = KontenMedia::where('konten_id', $validated['konten_id'])->where('urutan', '>', 0)->max('urutan');
        $urutan_baru = ($max_urutan ?? 0) + 1;

        KontenMedia::create([
            'konten_id' => $validated['konten_id'],
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            // Gunakan urutan yang dikirim jika > 0, jika tidak gunakan urutan otomatis
            'urutan' => $validated['urutan'] ?? $urutan_baru, 
        ]);

        return redirect()->back()->with('success', 'Media tambahan berhasil ditambahkan.');
    }

    /**
     * Hapus media tambahan.
     */
    public function destroyMedia($id)
    {
        $media = KontenMedia::findOrFail($id);
        
        // Cek dan hapus file fisik
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        // Hapus record dari database
        $media->delete();

        return redirect()->back()->with('success', 'Media tambahan berhasil dihapus.');
    }
}