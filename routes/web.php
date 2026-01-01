<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminPendaftaranController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AdminKontenController;
use App\Http\Controllers\Admin\AdminPembayaranController;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;

/*
|--------------------------------------------------------------------------
| Halaman Utama (Landing Page)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');


/*
|--------------------------------------------------------------------------
| Dashboard User
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {

    if (session()->has('pending_pendaftaran')) {
        $pendingData = session('pending_pendaftaran');
        session()->forget('pending_pendaftaran');

        if (Pendaftaran::where('id_user', Auth::id())->exists()) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah pernah mendaftar sebelumnya.');
        }

        $pendaftaran = Pendaftaran::create(array_merge(
            ['id_user' => Auth::id()],
            $pendingData
        ));

        return redirect()->route('pendaftaran.show', $pendaftaran->id_pendaftaran)
            ->with('success', 'Pendaftaran Anda berhasil diselesaikan!');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| ROUTE USER (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pendaftaran
    Route::get('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])
        ->name('pendaftaran.show');

    Route::get('/create', [PendaftaranController::class, 'create'])
        ->name('pendaftaran.create');

    Route::post('/store', [PendaftaranController::class, 'store'])
        ->name('pendaftaran.store');

    Route::get('/pendaftaran/{id}/export-pdf', [PendaftaranController::class, 'exportPdf'])
        ->name('pendaftaran.pdf');

    Route::get('/pendaftaran/{id}/preview', [PendaftaranController::class, 'previewPdf'])
        ->name('pendaftaran.previewPdf');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/submit', [PembayaranController::class, 'submit'])->name('pembayaran.submit');

});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| ROUTE ADMIN (Multi-Auth)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| ROUTE ADMIN & TATA TUHA (Multi-Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // 1. KELOMPOK AKSES: ADMIN & TATA TUHA
    // Masukkan rute yang boleh dibuka oleh keduanya di sini
    Route::middleware('auth:admin,tata_usaha')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Konten Website (CMS) - Sesuai permintaan Anda, TU bisa akses ini
        Route::get('/konten', [AdminKontenController::class, 'index'])->name('konten.index');
        Route::get('/konten/json/{id}', [AdminKontenController::class, 'json'])->name('konten.json');
        Route::post('/konten/create', [AdminKontenController::class, 'store'])->name('konten.store');
        Route::put('/konten/{id}', [AdminKontenController::class, 'update'])->name('konten.update');
        Route::delete('/konten/{id}', [AdminKontenController::class, 'destroy'])->name('konten.destroy');

        // Media Konten
        Route::post('/konten_media', [AdminKontenController::class, 'storeMedia'])->name('konten_media.store');
        Route::delete('/konten_media/{id}', [AdminKontenController::class, 'destroyMedia'])->name('konten_media.destroy');
    });

    // 2. KELOMPOK AKSES: KHUSUS ADMIN SAJA
    // TU tidak akan bisa membuka rute di bawah ini (Otomatis 403/Redirect)
    Route::middleware('auth:admin')->group(function () {

        // Pendaftaran
        Route::resource('pendaftaran', AdminPendaftaranController::class)
            ->only(['index', 'show'])
            ->names('pendaftaran');

        Route::get('pendaftaran/{pendaftaran}/download/{field}', [AdminPendaftaranController::class, 'download'])->name('pendaftaran.download');
        Route::post('pendaftaran/{pendaftaran}/approve', [AdminPendaftaranController::class, 'approve'])->name('pendaftaran.approve');
        Route::post('pendaftaran/{pendaftaran}/reject', [AdminPendaftaranController::class, 'reject'])->name('pendaftaran.reject');
        Route::get('/export/pendaftaran', [AdminExportController::class, 'export'])->name('export.pendaftaran');
        Route::post('/pendaftaran/{id}/status', [AdminPendaftaranController::class, 'update'])->name('pendaftaran.updateStatus');

        // Pembayaran
        Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/detail/{tagihan}', [AdminPembayaranController::class, 'show'])->name('pembayaran.show');
        Route::post('/pembayaran/verify/{id}', [AdminPembayaranController::class, 'verify'])->name('pembayaran.verify');
        Route::post('/pembayaran/reject/{id}', [AdminPembayaranController::class, 'reject'])->name('pembayaran.reject');
        Route::get('/pembayaran/bukti/{pembayaran}', [AdminPembayaranController::class, 'viewBukti'])->name('pembayaran.view-bukti');
        Route::get('/pembayaran/export', [AdminExportController::class, 'exportPembayaran'])->name('export.pembayaran');
        Route::post('/pembayaran/update-kwitansi/{id}', [AdminPembayaranController::class, 'updateKwitansi'])
            ->name('pembayaran.update-kwitansi');
    });
});

