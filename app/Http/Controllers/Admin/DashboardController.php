<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Ruang;
use App\Models\Ujian;

class DashboardController extends Controller
{
    public function index()
    {
        $total_siswa = Siswa::count();
        $total_kelas = Kelas::count();
        $total_ruang = Ruang::count();
        $total_ujian_today = Ujian::whereDate('tanggal', today())->count();

        // Get upcoming exams (top 5 starting from today)
        $upcoming_exams = Ujian::with(['kelas', 'sesi'])
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'total_siswa',
            'total_kelas',
            'total_ruang',
            'total_ujian_today',
            'upcoming_exams'
        ));
    }
}
