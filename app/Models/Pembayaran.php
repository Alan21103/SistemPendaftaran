<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    
    protected $fillable = [
        'tagihan_id',
        'nominal_bayar',
        'tanggal_bayar',
        'keterangan_cicilan',
        'bukti_transfer',
        'foto_kwitansi',
        'status_konfirmasi',
    ];

    // Kolom tanggal yang harus di-cast
    protected $casts = [
        'tanggal_bayar' => 'datetime',
    ];

    // Relasi: Satu Pembayaran dimiliki oleh satu Tagihan
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'id');
    }
}