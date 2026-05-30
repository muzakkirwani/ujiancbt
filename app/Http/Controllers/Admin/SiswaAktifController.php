<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilUjian;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiswaAktifController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        $today = date('Y-m-d');
        $nowTime = date('H:i:s');
        
        // Fetch exam records where exam date is today and currently within the session's active timeframe
        $siswa_aktif = HasilUjian::whereHas('ujian', function ($query) use ($today, $nowTime) {
            $query->whereDate('tanggal', $today)
                  ->whereHas('sesi', function ($q) use ($nowTime) {
                      $q->where('jam_mulai', '<=', $nowTime)
                        ->where('jam_berakhir', '>=', $nowTime);
                  });
        })
        ->with(['siswa.kelas', 'ujian.bankSoal.soals', 'ujian.sesi'])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('admin.siswa_aktif', compact('settings', 'siswa_aktif'));
    }

    public function reset($id)
    {
        $hasil = HasilUjian::findOrFail($id);
        
        $hasil->update([
            'benar' => 0,
            'salah' => 0,
            'kosong' => 0,
            'nilai' => 0,
            'jawaban_detail' => [],
            'status' => 'reset'
        ]);

        return redirect()->route('admin.siswa_aktif.index')->with('success', 'Ujian siswa berhasil di-reset. Siswa dapat memulai ulang pengerjaan dari awal.');
    }

    public function selesai($id)
    {
        $hasil = HasilUjian::with('ujian.bankSoal.soals')->findOrFail($id);
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

        return redirect()->route('admin.siswa_aktif.index')->with('success', 'Ujian siswa berhasil diselesaikan secara paksa. Hasil pengerjaan telah dikunci dan dinilai.');
    }
}
