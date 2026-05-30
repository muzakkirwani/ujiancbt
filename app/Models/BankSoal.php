<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    use HasFactory;

    protected $table = 'bank_soals';

    protected $fillable = [
        'user_id',
        'kode_bank',
        'mata_pelajaran',
        'kelas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'bank_soal_id');
    }

    public function scopeForTeacher($query, $userId)
    {
        $user = \App\Models\User::find($userId);
        if ($user && $user->mata_pelajaran) {
            $assignedSubjects = array_map('trim', explode(',', $user->mata_pelajaran));
            return $query->where(function($q) use ($assignedSubjects) {
                foreach ($assignedSubjects as $subject) {
                    $cleaned = rtrim($subject, ' .');
                    if (empty($cleaned)) continue;
                    
                    $q->orWhere('mata_pelajaran', 'LIKE', $cleaned . '%')
                      ->orWhere('mata_pelajaran', 'LIKE', '%' . $cleaned)
                      ->orWhereRaw("TRIM(TRAILING '.' FROM mata_pelajaran) = ?", [$cleaned]);
                }
            });
        }
        
        return $query->where('user_id', $userId);
    }
}
