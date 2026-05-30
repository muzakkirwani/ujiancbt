<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'siswa';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'foto',
        'username',
        'password',
        'kelas_id',
        'password_view',
        'nisn',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function ruangSiswa()
    {
        return $this->hasOne(RuangSiswa::class, 'user_id');
    }
}
