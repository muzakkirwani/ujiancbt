<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'nama',
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'foto',
        'role',
        'kelas_id',
        'mata_pelajaran',
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

    public function ruangPengawas()
    {
        return $this->hasMany(RuangPengawas::class, 'user_id');
    }

    public function bankSoals()
    {
        return $this->hasMany(BankSoal::class, 'user_id');
    }
}
