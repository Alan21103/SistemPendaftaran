<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminExportController extends Controller
{
    /**
     * Ekspor data pendaftaran ke file Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $fileName = 'data_pendaftaran_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new \App\Exports\PendaftaranExport, $fileName);
    }

    public function exportPembayaran()
    {
        $fileName = 'data_pembayaran_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new \App\Exports\PembayaranExport, $fileName);
    }
}
