<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\HasilUjian;
use App\Models\Ujian;
use App\Models\Setting;
use Illuminate\Http\Request;

class HasilUjianController extends Controller
{
    private function checkAccess($ujianId)
    {
        $userId = auth()->id();
        
        // Check if exam uses their own bank soal
        $hasBankSoal = Ujian::where('id', $ujianId)
            ->whereHas('bankSoal', function($q) use ($userId) {
                $q->forTeacher($userId);
            })->exists();

        if ($hasBankSoal) {
            return true;
        }

        // Check if teacher is assigned to supervise this session
        $assignments = \App\Models\RuangPengawas::where('user_id', $userId)->get();
        if ($assignments->isNotEmpty()) {
            foreach ($assignments as $assignment) {
                $siswaIds = \App\Models\RuangSiswa::where('ruang_id', $assignment->ruang_id)->pluck('user_id');
                $kelasIds = \App\Models\Siswa::whereIn('id', $siswaIds)->pluck('kelas_id')->unique()->toArray();
                $tanggalStr = $assignment->tanggal instanceof \Carbon\Carbon 
                    ? $assignment->tanggal->format('Y-m-d') 
                    : $assignment->tanggal;

                $match = Ujian::where('id', $ujianId)
                    ->whereDate('tanggal', $tanggalStr)
                    ->where('sesi_id', $assignment->sesi_id)
                    ->whereIn('kelas_id', $kelasIds)
                    ->exists();

                if ($match) {
                    return true;
                }
            }
        }

        return false;
    }

    public function index()
    {
        $userId = auth()->id();
        
        $ujians = Ujian::with(['kelas', 'sesi', 'bankSoal'])
            ->where('jenis_ujian', 'pilihan_ganda')
            ->where(function($query) use ($userId) {
                // Own bank soal
                $query->whereHas('bankSoal', function($q) use ($userId) {
                    $q->forTeacher($userId);
                })
                // Or supervised
                ->orWhere(function($q) use ($userId) {
                    $assignments = \App\Models\RuangPengawas::where('user_id', $userId)->get();
                    if ($assignments->isNotEmpty()) {
                        $q->where(function($subQ) use ($assignments) {
                            foreach ($assignments as $assignment) {
                                $siswaIds = \App\Models\RuangSiswa::where('ruang_id', $assignment->ruang_id)->pluck('user_id');
                                $kelasIds = \App\Models\Siswa::whereIn('id', $siswaIds)->pluck('kelas_id')->unique()->toArray();
                                $tanggalStr = $assignment->tanggal instanceof \Carbon\Carbon 
                                    ? $assignment->tanggal->format('Y-m-d') 
                                    : $assignment->tanggal;
                                
                                $subQ->orWhere(function($finalQ) use ($assignment, $kelasIds, $tanggalStr) {
                                    $finalQ->whereDate('tanggal', $tanggalStr)
                                           ->where('sesi_id', $assignment->sesi_id)
                                           ->whereIn('kelas_id', $kelasIds);
                                });
                            }
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                });
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pengawas.hasil_ujian.index', compact('ujians'));
    }

    public function show($id)
    {
        if (!$this->checkAccess($id)) {
            abort(403, 'Anda tidak memiliki akses ke data hasil ujian ini.');
        }

        $ujian = Ujian::with(['kelas', 'sesi', 'bankSoal'])->findOrFail($id);
        
        $hasil = HasilUjian::with('siswa')
            ->where('ujian_id', $id)
            ->get()
            ->sortByDesc('nilai');
            
        $settings = Setting::first();

        return view('pengawas.hasil_ujian.show', compact('ujian', 'hasil', 'settings'));
    }

    public function reset($id)
    {
        $hasil = HasilUjian::findOrFail($id);
        
        if (!$this->checkAccess($hasil->ujian_id)) {
            abort(403, 'Anda tidak memiliki akses untuk me-reset hasil ujian ini.');
        }

        $ujian_id = $hasil->ujian_id;
        
        $hasil->status = 'reset';
        $hasil->save();

        return redirect()->route('pengawas.hasil_ujian.show', $ujian_id)->with('success', 'Akses ujian siswa berhasil dibuka kembali tanpa menghapus nilai sebelumnya.');
    }
}
