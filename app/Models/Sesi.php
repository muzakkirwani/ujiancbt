<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    protected $table = 'sesi';

    public $timestamps = false;

    protected $fillable = [
        'nama_sesi',
        'jam_mulai',
        'jam_berakhir',
    ];

    public function ruangPengawas()
    {
        return $this->hasMany(RuangPengawas::class, 'sesi_id');
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'sesi_id');
    }
}
