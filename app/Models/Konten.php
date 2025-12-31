<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konten extends Model
{
    use HasFactory;

    protected $table = 'konten';

    protected $fillable = [
        'id_admin',
        'kategori_konten_id',
        'judul',
        'sub_judul',
        'isi',
        'urutan',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriKonten::class, 'kategori_konten_id');
    }

    public function media()
    {
        return $this->hasMany(KontenMedia::class, 'konten_id');
    }

    public function list()
    {
        return $this->hasMany(KontenList::class, 'konten_id');
    }
}
