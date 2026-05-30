<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    protected $table = 'ruang';

    public $timestamps = false;

    protected $fillable = [
        'nama_ruang',
    ];

    public function ruangSiswa()
    {
        return $this->hasMany(RuangSiswa::class, 'ruang_id');
    }

    public function ruangPengawas()
    {
        return $this->hasMany(RuangPengawas::class, 'ruang_id');
    }
}
