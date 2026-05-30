<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruang;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Sesi;
use App\Models\Kelas;
use App\Models\RuangSiswa;
use App\Models\RuangPengawas;
use Illuminate\Http\Request;

class RuangController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'ruang');

        $ruang_list = Ruang::withCount(['ruangSiswa as siswa_count', 'ruangPengawas as pengawas_count'])
            ->orderBy('nama_ruang', 'asc')
            ->get();
        $pengawas_list = User::where('role', 'pengawas')->orderBy('nama', 'asc')->get();
        $sesi_list = Sesi::orderBy('jam_mulai', 'asc')->get();
        $kelas_list = Kelas::orderBy('nama_kelas', 'asc')->get();

        // Get unplotted students
        $unplotted = Siswa::with('kelas')
            ->whereNotIn('id', function($query) {
                $query->select('user_id')->from('ruang_siswa');
            })
            ->orderBy('nama', 'asc')
            ->get();

        // Get plotting list
        $plots = RuangSiswa::with(['siswa.kelas', 'ruang'])
            ->get()
            ->sortBy(function($plot) {
                return ($plot->ruang->nama_ruang ?? '') . ($plot->siswa->nama ?? '');
            });

        // Get proctor assignments
        $tugas = RuangPengawas::with(['user', 'ruang', 'sesi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.ruang', compact(
            'tab',
            'ruang_list',
            'pengawas_list',
            'sesi_list',
            'kelas_list',
            'unplotted',
            'plots',
            'tugas'
        ));
    }

    /**
     * Standard resource methods — delegated to named methods below.
     * Required because Route::resource() maps to store/update/destroy by convention.
     */
    public function store(Request $request)
    {
        return $this->storeRuang($request);
    }

    public function update(Request $request, $id)
    {
        return $this->updateRuang($request, $id);
    }

    public function destroy($id)
    {
        return $this->destroyRuang($id);
    }

    public function storeRuang(Request $request)
    {
        $request->validate([
            'nama_ruang' => 'required|string|max:50',
        ]);

        Ruang::create($request->only('nama_ruang'));

        return redirect()->route('admin.ruang.index', ['tab' => 'ruang'])->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function updateRuang(Request $request, $id)
    {
        $request->validate([
            'nama_ruang' => 'required|string|max:50',
        ]);

        $ruang = Ruang::findOrFail($id);
        $ruang->update($request->only('nama_ruang'));

        return redirect()->route('admin.ruang.index', ['tab' => 'ruang'])->with('success', 'Ruangan berhasil diperbarui!');
    }

    public function destroyRuang($id)
    {
        $ruang = Ruang::findOrFail($id);
        $ruang->delete();

        return redirect()->route('admin.ruang.index', ['tab' => 'ruang'])->with('success', 'Ruangan berhasil dihapus!');
    }

    public function plotSiswa(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'ruang_id' => 'required|exists:ruang,id',
        ]);

        $siswa_list = Siswa::where('kelas_id', $request->kelas_id)->get();
        $plotted_count = 0;

        foreach ($siswa_list as $siswa) {
            // Avoid duplicate plotting
            if (!RuangSiswa::where('user_id', $siswa->id)->exists()) {
                RuangSiswa::create([
                    'ruang_id' => $request->ruang_id,
                    'user_id' => $siswa->id,
                ]);
                $plotted_count++;
            }
        }

        return redirect()->route('admin.ruang.index')->with('success', "Berhasil mem-plot {$plotted_count} siswa dari kelas ke ruangan!");
    }

    public function updatePlotting(Request $request, $id)
    {
        $request->validate([
            'ruang_id' => 'required|exists:ruang,id',
        ]);

        $plot = RuangSiswa::findOrFail($id);
        $plot->update($request->only('ruang_id'));

        return redirect()->route('admin.ruang.index', ['tab' => 'plotting'])->with('success', 'Plotting siswa berhasil diperbarui!');
    }

    public function destroyPlotting($id)
    {
        $plot = RuangSiswa::findOrFail($id);
        $plot->delete();

        return redirect()->route('admin.ruang.index', ['tab' => 'plotting'])->with('success', 'Plotting siswa berhasil dihapus!');
    }

    public function plotPengawas(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ruang_id' => 'required|exists:ruang,id',
            'sesi_id' => 'required|exists:sesi,id',
            'tanggal' => 'required|date',
        ]);

        RuangPengawas::create($request->only('user_id', 'ruang_id', 'sesi_id', 'tanggal'));

        return redirect()->route('admin.ruang.index', ['tab' => 'tugas'])->with('success', 'Pengawas berhasil ditugaskan!');
    }

    public function updateTugas(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ruang_id' => 'required|exists:ruang,id',
            'sesi_id' => 'required|exists:sesi,id',
            'tanggal' => 'required|date',
        ]);

        $tugas = RuangPengawas::findOrFail($id);
        $tugas->update($request->only('user_id', 'ruang_id', 'sesi_id', 'tanggal'));

        return redirect()->route('admin.ruang.index', ['tab' => 'tugas'])->with('success', 'Tugas pengawas berhasil diperbarui!');
    }

    public function destroyTugas($id)
    {
        $tugas = RuangPengawas::findOrFail($id);
        $tugas->delete();

        return redirect()->route('admin.ruang.index', ['tab' => 'tugas'])->with('success', 'Tugas pengawas berhasil dihapus!');
    }

    public function cetakKartu($id)
    {
        $settings = \App\Models\Setting::first();
        $ruang = Ruang::findOrFail($id);
        
        $siswa_list = Siswa::with('kelas')
            ->whereIn('id', function($query) use ($id) {
                $query->select('user_id')->from('ruang_siswa')->where('ruang_id', $id);
            })
            ->orderBy('nama', 'asc')
            ->get();

        return view('admin.print.kartu', compact('settings', 'ruang', 'siswa_list'));
    }

    public function cetakDaftarHadir($id)
    {
        $settings = \App\Models\Setting::first();
        $ruang = Ruang::findOrFail($id);
        $sesi_id = request()->query('sesi_id');

        $siswa_list = Siswa::with('kelas')
            ->whereIn('id', function($query) use ($id) {
                $query->select('user_id')->from('ruang_siswa')->where('ruang_id', $id);
            })
            ->orderBy('nama', 'asc')
            ->get();

        // Kelas IDs dari siswa di ruang ini
        $kelas_ids = $siswa_list->pluck('kelas_id')->unique()->filter()->values();

        if ($sesi_id) {
            // Single sesi mode
            $tugasPengawas = RuangPengawas::where('ruang_id', $id)
                ->where('sesi_id', $sesi_id)
                ->orderByDesc('tanggal')->first();
            $pengawas     = $tugasPengawas ? User::find($tugasPengawas->user_id) : null;
            $sesi         = Sesi::find($sesi_id);
            $semua_pengawas = null;
            $tanggal_ujian  = $tugasPengawas?->tanggal;

            // Cari mata pelajaran dari ujian pada tanggal + sesi + kelas yang relevan
            $mapel_list = \App\Models\Ujian::whereIn('kelas_id', $kelas_ids)
                ->where('sesi_id', $sesi_id)
                ->when($tanggal_ujian, fn($q) => $q->where('tanggal', $tanggal_ujian))
                ->pluck('mapel')->unique()->values();
        } else {
            // All sessions mode
            $pengawas     = null;
            $sesi         = null;
            $semua_pengawas = RuangPengawas::with(['user', 'sesi'])
                ->where('ruang_id', $id)
                ->orderBy('sesi_id')
                ->get()
                ->groupBy('sesi_id');

            $firstTugas    = RuangPengawas::where('ruang_id', $id)->orderBy('tanggal')->first();
            $tanggal_ujian = $firstTugas?->tanggal;

            // Cari semua mapel di tanggal tersebut untuk kelas yang ada di ruang ini
            $mapel_list = \App\Models\Ujian::whereIn('kelas_id', $kelas_ids)
                ->when($tanggal_ujian, fn($q) => $q->where('tanggal', $tanggal_ujian))
                ->pluck('mapel')->unique()->values();
        }

        $ujian = null;

        return view('admin.print.hadir_ruang', compact(
            'settings', 'ruang', 'siswa_list', 'pengawas', 'sesi',
            'semua_pengawas', 'ujian', 'tanggal_ujian', 'mapel_list'
        ));
    }

    public function daftarHadirList()
    {
        $settings = \App\Models\Setting::first();
        // Only rooms that have plotted students
        $ruang_list = Ruang::withCount('ruangSiswa as siswa_count')
            ->whereHas('ruangSiswa')
            ->orderBy('nama_ruang', 'asc')
            ->get();
        $sesi_list = Sesi::orderBy('jam_mulai', 'asc')->get();
        return view('admin.daftar_hadir', compact('settings', 'ruang_list', 'sesi_list'));
    }

    public function cetakHadirUjian(Request $request)
    {
        $settings = \App\Models\Setting::first();
        $ujian_id = $request->query('ujian_id');
        $ruang_id = $request->query('ruang_id');

        $ujian = \App\Models\Ujian::with(['kelas', 'sesi'])->findOrFail($ujian_id);
        
        $ruang = null;
        if ($ruang_id) {
            $ruang = Ruang::findOrFail($ruang_id);
            // Show ALL students in this room — no class filter
            $siswa_list = Siswa::with('kelas')
                ->whereIn('id', function($query) use ($ruang_id) {
                    $query->select('user_id')->from('ruang_siswa')->where('ruang_id', $ruang_id);
                })
                ->orderBy('nama', 'asc')
                ->get();
        } else {
            // No room selected — show only plotted students from the ujian's class
            $siswa_list = Siswa::with('kelas')
                ->where('kelas_id', $ujian->kelas_id)
                ->whereIn('id', function($query) {
                    $query->select('user_id')->from('ruang_siswa');
                })
                ->orderBy('nama', 'asc')
                ->get();
        }

        $pengawas = null;
        if ($ruang) {
            $pengawas = User::whereIn('id', function($query) use ($ruang, $ujian) {
                $query->select('user_id')
                    ->from('ruang_pengawas')
                    ->where('ruang_id', $ruang->id)
                    ->where('sesi_id', $ujian->sesi_id)
                    ->where('tanggal', $ujian->tanggal);
            })->first();
        }

        return view('admin.print.hadir', compact('settings', 'ujian', 'ruang', 'siswa_list', 'pengawas'));
    }
}

