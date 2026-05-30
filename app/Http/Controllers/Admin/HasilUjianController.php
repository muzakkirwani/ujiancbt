<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilUjian;
use App\Models\Ujian;
use Illuminate\Http\Request;

class HasilUjianController extends Controller
{
    public function index()
    {
        // Get all exams that are native CBT
        $ujians = Ujian::with(['kelas', 'sesi', 'bankSoal'])
            ->where('jenis_ujian', 'pilihan_ganda')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.hasil_ujian.index', compact('ujians'));
    }

    public function show($id)
    {
        $ujian = Ujian::with(['kelas', 'sesi', 'bankSoal'])->findOrFail($id);
        
        $hasil = HasilUjian::with('siswa')
            ->where('ujian_id', $id)
            ->get()
            ->sortByDesc('nilai');
            
        $settings = \App\Models\Setting::first();

        return view('admin.hasil_ujian.show', compact('ujian', 'hasil', 'settings'));
    }

    public function reset($id)
    {
        $hasil = HasilUjian::findOrFail($id);
        $ujian_id = $hasil->ujian_id;
        
        $hasil->status = 'reset';
        $hasil->save();

        return redirect()->route('admin.hasil_ujian.show', $ujian_id)->with('success', 'Akses ujian siswa berhasil dibuka kembali tanpa menghapus nilai sebelumnya.');
    }
}
