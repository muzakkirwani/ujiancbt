<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuangSiswa extends Model
{
    protected $table = 'ruang_siswa';

    public $timestamps = false;

    protected $fillable = [
        'ruang_id',
        'user_id',
    ];

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'ruang_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'user_id');
    }
}
