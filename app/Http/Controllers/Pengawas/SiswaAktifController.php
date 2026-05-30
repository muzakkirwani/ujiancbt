<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\HasilUjian;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiswaAktifController extends Controller
{
    private function checkAccess($hasilUjian)
    {
        $userId = auth()->id();
        $ujianId = $hasilUjian->ujian_id;
        
        // Own bank soal
        $hasBankSoal = \App\Models\Ujian::where('id', $ujianId)
            ->whereHas('bankSoal', function($q) use ($userId) {
                $q->forTeacher($userId);
            })->exists();

        if ($hasBankSoal) {
            return true;
        }

        // Supervised
        $assignments = \App\Models\RuangPengawas::where('user_id', $userId)->get();
        if ($assignments->isNotEmpty()) {
            foreach ($assignments as $assignment) {
                $siswaIds = \App\Models\RuangSiswa::where('ruang_id', $assignment->ruang_id)->pluck('user_id');
                $kelasIds = \App\Models\Siswa::whereIn('id', $siswaIds)->pluck('kelas_id')->unique()->toArray();
                $tanggalStr = $assignment->tanggal instanceof \Carbon\Carbon 
                    ? $assignment->tanggal->format('Y-m-d') 
                    : $assignment->tanggal;

                $match = \App\Models\Ujian::where('id', $ujianId)
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
        $settings = Setting::first();
        $today = date('Y-m-d');
        $nowTime = date('H:i:s');
        $userId = auth()->id();
        
        $siswa_aktif = HasilUjian::whereHas('ujian', function ($query) use ($today, $nowTime, $userId) {
            $query->whereDate('tanggal', $today)
                  ->whereHas('sesi', function ($q) use ($nowTime) {
                      $q->where('jam_mulai', '<=', $nowTime)
                        ->where('jam_berakhir', '>=', $nowTime);
                  })
                  ->where(function($q) use ($userId) {
                      // Own bank soal
                      $q->whereHas('bankSoal', function($subQ) use ($userId) {
                          $subQ->forTeacher($userId);
                      })
                      // Or supervised
                      ->orWhere(function($subQ) use ($userId) {
                          $assignments = \App\Models\RuangPengawas::where('user_id', $userId)->get();
                          if ($assignments->isNotEmpty()) {
                              $subQ->where(function($innerQ) use ($assignments) {
                                  foreach ($assignments as $assignment) {
                                      $siswaIds = \App\Models\RuangSiswa::where('ruang_id', $assignment->ruang_id)->pluck('user_id');
                                      $kelasIds = \App\Models\Siswa::whereIn('id', $siswaIds)->pluck('kelas_id')->unique()->toArray();
                                      $tanggalStr = $assignment->tanggal instanceof \Carbon\Carbon 
                                          ? $assignment->tanggal->format('Y-m-d') 
                                          : $assignment->tanggal;
                                      
                                      $innerQ->orWhere(function($finalQ) use ($assignment, $kelasIds, $tanggalStr) {
                                          $finalQ->whereDate('tanggal', $tanggalStr)
                                                 ->where('sesi_id', $assignment->sesi_id)
                                                 ->whereIn('kelas_id', $kelasIds);
                                      });
                                  }
                              });
                          } else {
                              $subQ->whereRaw('1 = 0');
                          }
                      });
                  });
        })
        ->with(['siswa.kelas', 'ujian.bankSoal.soals', 'ujian.sesi'])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('pengawas.siswa_aktif', compact('settings', 'siswa_aktif'));
    }

    public function reset($id)
    {
        $hasil = HasilUjian::findOrFail($id);
        
        if (!$this->checkAccess($hasil)) {
            abort(403, 'Anda tidak memiliki akses untuk me-reset ujian siswa ini.');
        }
        
        $hasil->update([
            'benar' => 0,
            'salah' => 0,
            'kosong' => 0,
            'nilai' => 0,
            'jawaban_detail' => [],
            'status' => 'reset'
        ]);

        return redirect()->route('pengawas.siswa_aktif.index')->with('success', 'Ujian siswa berhasil di-reset. Siswa dapat memulai ulang pengerjaan dari awal.');
    }

    public function selesai($id)
    {
        $hasil = HasilUjian::with('ujian.bankSoal.soals')->findOrFail($id);
        
        if (!$this->checkAccess($hasil)) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan ujian siswa ini.');
        }

        $ujian = $hasil->ujian;

        $benar = 0;
        $salah = 0;
        $kosong = 0;
        $total_soal_pg = 0;
        $jawaban_detail = $hasil->jawaban_detail ?? [];

        if ($ujian && $ujian->bankSoal) {
            foreach ($ujian->bankSoal->soals as $soal) {
                $jawabanSiswa = $jawaban_detail[$soal->id] ?? null;

                if ($soal->jenis_soal !== 'esai') {
                    $total_soal_pg++;
                    if (empty($jawabanSiswa)) {
                        $kosong++;
                    } else if (strtoupper($jawabanSiswa) === strtoupper($soal->kunci_jawaban)) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                }
            }
        }

        $nilai = $total_soal_pg > 0 ? ($benar / $total_soal_pg) * 100 : 0;

        $hasil->update([
            'benar' => $benar,
            'salah' => $salah,
            'kosong' => $kosong,
            'nilai' => $nilai,
            'status' => 'selesai'
        ]);

        return redirect()->route('pengawas.siswa_aktif.index')->with('success', 'Ujian siswa berhasil diselesaikan secara paksa. Hasil pengerjaan telah dikunci dan dinilai.');
    }
}
