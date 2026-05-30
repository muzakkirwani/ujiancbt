<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'kelas_id');
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'kelas_id');
    }
}
