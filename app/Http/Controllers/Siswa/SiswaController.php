<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        $siswa = Auth::guard('siswa')->user()->load('kelas');

        $exams = Ujian::with('sesi')
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $hasil_ujians = \App\Models\HasilUjian::where('siswa_id', $siswa->id)->get()->keyBy('ujian_id');

        return view('siswa.dashboard', compact('settings', 'siswa', 'exams', 'hasil_ujians'));
    }

    public function exam($id)
    {
        $settings = Setting::first();
        $ujian = Ujian::with(['sesi', 'bankSoal.soals'])->findOrFail($id);

        // Check if student belongs to the classroom scheduled for this exam
        $siswa = Auth::guard('siswa')->user();
        if ($siswa->kelas_id != $ujian->kelas_id) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda tidak memiliki akses ke ujian ini.');
        }

        // Check Time
        $now = time();
        $tanggal_str = $ujian->tanggal instanceof \Carbon\Carbon ? $ujian->tanggal->format('Y-m-d') : $ujian->tanggal;
        $exam_start = strtotime($tanggal_str . ' ' . $ujian->sesi->jam_mulai);
        $exam_end = strtotime($tanggal_str . ' ' . $ujian->sesi->jam_berakhir);

        if ($now < $exam_start || $now > $exam_end) {
            return redirect()->route('siswa.dashboard')->with('error', 'Waktu ujian telah berakhir atau belum dimulai.');
        }

        // Check if already submitted and completed
        $existing = \App\Models\HasilUjian::where('ujian_id', $id)->where('siswa_id', $siswa->id)->first();
        if ($existing && $existing->status == 'selesai') {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        $savedAnswers = [];
        $isResuming = false;
        if ($existing && $existing->status == 'berjalan') {
            $savedAnswers = $existing->jawaban_detail ?? [];
            $isResuming = true;
        }

        return view('siswa.exam', compact('settings', 'ujian', 'savedAnswers', 'isResuming'));
    }

    public function submitExam(Request $request, $id)
    {
        $siswa = Auth::guard('siswa')->user();
        $ujian = Ujian::with('bankSoal.soals')->findOrFail($id);
        
        if ($siswa->kelas_id != $ujian->kelas_id) {
            return redirect()->route('siswa.dashboard')->with('error', 'Akses ditolak.');
        }

        $benar = 0;
        $salah = 0;
        $kosong = 0;
        $total_soal_pg = 0;
        $jawaban_detail = [];

        foreach ($ujian->bankSoal->soals as $soal) {
            $jawabanSiswa = $request->input('jawaban_' . $soal->id);
            $jawaban_detail[$soal->id] = $jawabanSiswa;

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

        $nilai = $total_soal_pg > 0 ? ($benar / $total_soal_pg) * 100 : 0;

        // Update or create HasilUjian
        $hasil = \App\Models\HasilUjian::updateOrCreate(
            ['ujian_id' => $ujian->id, 'siswa_id' => $siswa->id],
            [
                'benar' => $benar,
                'salah' => $salah,
                'kosong' => $kosong,
                'nilai' => $nilai,
                'jawaban_detail' => $jawaban_detail,
                'status' => 'selesai'
            ]
        );

        return redirect()->route('siswa.dashboard')->with('success', 'Jawaban berhasil dikirim! Nilai Anda telah disimpan.');
    }

    public function autoSave(Request $request, $id)
    {
        $siswa = Auth::guard('siswa')->user();
        
        $hasil = \App\Models\HasilUjian::firstOrCreate(
            ['ujian_id' => $id, 'siswa_id' => $siswa->id],
            [
                'benar' => 0,
                'salah' => 0,
                'kosong' => 0,
                'nilai' => 0,
                'jawaban_detail' => [],
                'status' => 'berjalan'
            ]
        );

        if ($hasil->status == 'selesai') {
            return response()->json(['success' => false, 'message' => 'Ujian sudah selesai.']);
        }

        $jawaban_detail = $hasil->jawaban_detail ?? [];
        if ($hasil->status == 'reset') {
            // First save after reset: clear old answers
            $jawaban_detail = [];
        }

        $jawaban_detail[$request->soal_id] = $request->jawaban;

        $hasil->jawaban_detail = $jawaban_detail;
        $hasil->status = 'berjalan';
        $hasil->save();

        return response()->json(['success' => true]);
    }
}
