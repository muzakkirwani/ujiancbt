<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\Setting;
use App\Models\RuangPengawas;
use App\Models\RuangSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PengawasController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        $userId = auth()->id();

        // Ambil semua jadwal penugasan pengawas untuk user yang sedang login
        $assignments = RuangPengawas::where('user_id', $userId)->get();

        if ($assignments->isEmpty()) {
            $ujian_list = collect();
        } else {
            $ujianQuery = Ujian::with(['kelas', 'sesi']);
            
            $ujianQuery->where(function ($query) use ($assignments) {
                foreach ($assignments as $assignment) {
                    // Ambil ID siswa yang berada di ruangan pengawasan ini
                    $siswaIds = RuangSiswa::where('ruang_id', $assignment->ruang_id)->pluck('user_id');
                    
                    // Ambil ID kelas dari siswa-siswa tersebut
                    $kelasIds = Siswa::whereIn('id', $siswaIds)->pluck('kelas_id')->unique()->toArray();
                    
                    $tanggalStr = $assignment->tanggal instanceof \Carbon\Carbon 
                        ? $assignment->tanggal->format('Y-m-d') 
                        : $assignment->tanggal;

                    $query->orWhere(function ($q) use ($assignment, $kelasIds, $tanggalStr) {
                        $q->whereDate('tanggal', $tanggalStr)
                          ->where('sesi_id', $assignment->sesi_id)
                          ->whereIn('kelas_id', $kelasIds);
                    });
                }
            });

            $ujian_list = $ujianQuery->orderBy('tanggal', 'desc')->get();
        }

        return view('pengawas.dashboard', compact('settings', 'ujian_list'));
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
        ]);

        $ujian = Ujian::findOrFail($request->ujian_id);
        $new_token = strtoupper(substr(md5(uniqid()), 0, 5));
        
        $ujian->update([
            'token' => $new_token,
            'token_updated_at' => now(),
        ]);

        return redirect()->route('pengawas.dashboard')->with('success', 'Token berhasil diperbarui!');
    }
}
