<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilUjian extends Model
{
    use HasFactory;

    protected $table = 'hasil_ujians';

    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'benar',
        'salah',
        'kosong',
        'nilai',
        'jawaban_detail',
        'status',
    ];

    protected $casts = [
        'jawaban_detail' => 'array',
        'nilai' => 'decimal:2',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
