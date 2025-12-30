<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembayaranExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    /**
     * Mengambil data tagihan beserta relasi pendaftaran.
     */
    public function collection()
    {
        // Mengambil data tagihan dengan eager loading pendaftaran
        return Tagihan::with('pendaftaran')->get();
    }

    /**
     * Mapping data agar nominal mudah dibaca dan kolom relasi muncul.
     */
    public function map($tagihan): array
    {
        return [
            $tagihan->pendaftaran->nama_siswa ?? '-',
            $tagihan->pendaftaran->nisn ?? '-',
            $tagihan->pendaftaran->no_telp ?? '-',
            $tagihan->pendaftaran->asal_sekolah ?? '-',
            $tagihan->total_tagihan,
            $tagihan->total_tagihan - $tagihan->sisa_tagihan, // Sudah dibayar
            $tagihan->sisa_tagihan,
            strtoupper($tagihan->status_pembayaran),
            $tagihan->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Menentukan header kolom.
     */
    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NISN',
            'No. Telepon',
            'Asal Sekolah',
            'Total Tagihan (Rp)',
            'Sudah Dibayar (Rp)',
            'Sisa Tagihan (Rp)',
            'Status',
            'Tanggal Bayar',
        ];
    }

    /**
     * Styling Excel (Biru Elegan).
     */
    public function styles(Worksheet $sheet)
    {
        // Style Header (A1 sampai I1)
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => '10B981'], // Hijau Emerald (identik dengan uang/pembayaran)
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Border untuk seluruh data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '808080'],
                ],
            ],
        ]);

        // Format Ribuan (Currency) untuk kolom E, F, G
        $sheet->getStyle('E2:G' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(25);
    }
}