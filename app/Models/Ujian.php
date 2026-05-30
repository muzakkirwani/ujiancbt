<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Ujian extends Model
{
    protected $table = 'ujian';

    public $timestamps = false;

    protected $fillable = [
        'mapel',
        'kelas_id',
        'tanggal',
        'sesi_id',
        'jenis_ujian',
        'bank_soal_id',
        'link_ujian',
        'token',
        'token_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function getTokenAttribute($value)
    {
        if (!$this->id) return $value;

        // Pastikan kolom token_updated_at terbuat di DB
        static $columnChecked = false;
        if (!$columnChecked) {
            try {
                DB::statement("ALTER TABLE ujian ADD COLUMN token_updated_at TIMESTAMP NULL");
            } catch (\Exception $e) {
                // Kolom sudah ada, abaikan error
            }
            $columnChecked = true;
        }

        // Ambil data terbaru langsung dari database agar sinkron
        $rawUjian = DB::table('ujian')
            ->select('token', 'token_updated_at')
            ->where('id', $this->id)
            ->first();

        if (!$rawUjian) return $value;

        $token = $rawUjian->token;
        $updatedAt = $rawUjian->token_updated_at;

        $needsUpdate = false;
        if (empty($token) || empty($updatedAt)) {
            $needsUpdate = true;
        } else {
            $lastUpdated = \Carbon\Carbon::parse($updatedAt);
            if ($lastUpdated->addMinutes(15)->isPast()) {
                $needsUpdate = true;
            }
        }

        if ($needsUpdate) {
            $newToken = strtoupper(substr(md5(uniqid('', true)), 0, 5));
            
            DB::table('ujian')
                ->where('id', $this->id)
                ->update([
                    'token' => $newToken,
                    'token_updated_at' => now(),
                ]);
            
            return $newToken;
        }

        return $token;
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
