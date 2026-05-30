<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    public function index()
    {
        $bank_soals = BankSoal::forTeacher(auth()->id())
            ->withCount('soals')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $user = auth()->user();
        if ($user && $user->mata_pelajaran) {
            $assignedSubjects = array_map('trim', explode(',', $user->mata_pelajaran));
            $mata_pelajarans = MataPelajaran::whereIn('nama_mapel', $assignedSubjects)
                ->orderBy('nama_mapel', 'asc')
                ->get();
        } else {
            $mata_pelajarans = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        }
        
        $kelas_list = Kelas::orderBy('nama_kelas', 'asc')->get();
        
        return view('pengawas.bank_soal.index', compact('bank_soals', 'mata_pelajarans', 'kelas_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bank' => 'required|string|max:50|unique:bank_soals,kode_bank',
            'mata_pelajaran' => 'required|string|max:100',
            'kelas' => 'nullable|array',
        ]);

        $data = $request->except('kelas');
        if ($request->has('kelas') && is_array($request->kelas)) {
            $kelasArray = array_filter($request->kelas);
            $data['kelas'] = count($kelasArray) > 0 ? implode(', ', $kelasArray) : null;
        } else {
            $data['kelas'] = null;
        }

        $data['user_id'] = auth()->id();

        BankSoal::create($data);

        return redirect()->route('pengawas.bank_soal.index')->with('success', 'Bank Soal berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $bankSoal = BankSoal::forTeacher(auth()->id())->findOrFail($id);

        $request->validate([
            'kode_bank' => 'required|string|max:50|unique:bank_soals,kode_bank,' . $id,
            'mata_pelajaran' => 'required|string|max:100',
            'kelas' => 'nullable|array',
        ]);

        $data = $request->except('kelas');
        if ($request->has('kelas') && is_array($request->kelas)) {
            $kelasArray = array_filter($request->kelas);
            $data['kelas'] = count($kelasArray) > 0 ? implode(', ', $kelasArray) : null;
        } else {
            $data['kelas'] = null;
        }

        $bankSoal->update($data);

        return redirect()->route('pengawas.bank_soal.index')->with('success', 'Bank Soal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $bankSoal = BankSoal::forTeacher(auth()->id())->findOrFail($id);
        $bankSoal->delete();

        return redirect()->route('pengawas.bank_soal.index')->with('success', 'Bank Soal berhasil dihapus!');
    }
}
