<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran; 
use App\Models\Tagihan; // Tambahkan ini

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- Reporting Pendaftaran ---
        $totalPendaftaran = Pendaftaran::count();
        $diterimaCount = Pendaftaran::where('status', 'diterima')->count();
        $ditolakCount = Pendaftaran::where('status', 'ditolak')->count();
        $pendingCount = Pendaftaran::where('status', 'pending')->count();

        // --- Reporting Pembayaran (Baru) ---
        // 1. Lunas
        $pembayaranLunas = Tagihan::where('status_pembayaran', 'lunas')->count();
        
        // 2. Belum Lunas (Cicilan/Belum Bayar sama sekali)
        $pembayaranBelumLunas = Tagihan::where('status_pembayaran', 'belum lunas')->count();

        // 3. Menunggu Konfirmasi (Siswa sudah upload bukti, admin belum verifikasi)
        // Kita cek dari relasi 'pembayaran' yang memiliki status 'Menunggu Verifikasi'
        $pembayaranPending = Tagihan::whereHas('pembayaran', function($query) {
            $query->where('status_konfirmasi', 'Menunggu Verifikasi');
        })->count();

        // 4. Total Uang Masuk (Opsional, tapi sangat berguna untuk dashboard)
        $totalUangMasuk = Tagihan::all()->sum(function($t) {
            return $t->total_tagihan - $t->sisa_tagihan;
        });

        return view('admin.dashboard', [
            'totalPendaftaran' => $totalPendaftaran,
            'diterimaCount' => $diterimaCount,
            'ditolakCount' => $ditolakCount,
            'pendingCount' => $pendingCount,
            // Data Pembayaran
            'pembayaranLunas' => $pembayaranLunas,
            'pembayaranBelumLunas' => $pembayaranBelumLunas,
            'pembayaranPending' => $pembayaranPending,
            'totalUangMasuk' => $totalUangMasuk,
        ]);
    }
}