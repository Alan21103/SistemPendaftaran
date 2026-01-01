<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    // Nama tabel sesuai dengan migrasi
    protected $table = 'pendaftaran'; 
    
    // Primary key sesuai dengan migrasi
    protected $primaryKey = 'id_pendaftaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'id_admin',
        'nisn',
        'nama_siswa',
        'tempat_tgl_lahir',
        'jenis_kelamin',
        'agama',
        'asal_sekolah',
        'alamat',
        'status',
        
        // Atribut Orang Tua
        'nama_ayah',
        'no_telp',
        'nama_ibu',
        'pendidikan_terakhir_ayah',
        'pendidikan_terakhir_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',

        // Atribut Dokumen
        'kk',
        'akte',
        'foto',
        'ijazah_sk',
        'bukti_bayar',
    ];

    /**
     * Relasi ke User (Siswa)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Admin yang memproses
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    /**
     * Relasi ke Tagihan
     * Digunakan untuk menyambungkan Pendaftaran ke data keuangan
     */
   public function tagihan()
{
    // Jika di tabel tagihan kolomnya adalah id_pendaftaran
    return $this->hasOne(Tagihan::class, 'id_pendaftaran', 'id_pendaftaran');
}

    /**
     * Relasi ke Pembayaran (Melalui Tagihan)
     * Ini memungkinkan Anda mengakses $pendaftaran->pembayaran langsung
     */
   public function pembayaran()
{
    return $this->hasOneThrough(
        Pembayaran::class,
        Tagihan::class,
        'id_pendaftaran', // Foreign key di tabel tagihan
        'tagihan_id',     // Foreign key di tabel pembayaran
        'id_pendaftaran', // Local key di tabel pendaftaran
        'id'              // Local key di tabel tagihan
    );
}
}