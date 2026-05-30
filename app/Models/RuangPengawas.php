<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuangPengawas extends Model
{
    protected $table = 'ruang_pengawas';

    public $timestamps = false;

    protected $fillable = [
        'ruang_id',
        'user_id',
        'sesi_id',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'ruang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }
}
