<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\Kelas;
use App\Models\Sesi;
use App\Models\BankSoal;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    public function index()
    {
        $ujian_list = Ujian::with(['kelas', 'sesi'])
            ->orderBy('tanggal', 'desc')
            ->get();
        $kelas_list = Kelas::orderBy('nama_kelas', 'asc')->get();
        $sesi_list = Sesi::orderBy('jam_mulai', 'asc')->get();
        $bank_soal_list = BankSoal::orderBy('mata_pelajaran', 'asc')->get();

        return view('admin.ujian', compact('ujian_list', 'kelas_list', 'sesi_list', 'bank_soal_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mapel' => 'required|string|max:100',
            'kelas_id' => 'required|array|min:1',
            'kelas_id.*' => 'exists:kelas,id',
            'tanggal' => 'required|date',
            'sesi_id' => 'required|exists:sesi,id',
            'jenis_ujian' => 'required|in:googleform,pilihan_ganda',
            'bank_soal_id' => 'required_if:jenis_ujian,pilihan_ganda|nullable|exists:bank_soals,id',
            'link_ujian' => 'required_if:jenis_ujian,googleform|nullable|url',
        ]);

        $baseData = $request->only('mapel', 'tanggal', 'sesi_id', 'jenis_ujian');
        if ($baseData['jenis_ujian'] == 'pilihan_ganda') {
            $baseData['bank_soal_id'] = $request->bank_soal_id;
            $baseData['link_ujian'] = null;
        } else {
            $baseData['link_ujian'] = $request->link_ujian;
            $baseData['bank_soal_id'] = null;
        }

        foreach ($request->kelas_id as $kId) {
            $data = $baseData;
            $data['kelas_id'] = $kId;
            $data['token'] = strtoupper(substr(md5(uniqid('', true)), 0, 5));
            $data['token_updated_at'] = now();
            Ujian::create($data);
        }

        return redirect()->route('admin.ujian.index')->with('success', 'Jadwal ujian berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'mapel' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'sesi_id' => 'required|exists:sesi,id',
            'jenis_ujian' => 'required|in:googleform,pilihan_ganda',
            'bank_soal_id' => 'required_if:jenis_ujian,pilihan_ganda|nullable|exists:bank_soals,id',
            'link_ujian' => 'required_if:jenis_ujian,googleform|nullable|url',
        ]);

        $ujian = Ujian::findOrFail($id);
        
        $data = $request->only('mapel', 'kelas_id', 'tanggal', 'sesi_id', 'jenis_ujian');
        if ($data['jenis_ujian'] == 'pilihan_ganda') {
            $data['bank_soal_id'] = $request->bank_soal_id;
            $data['link_ujian'] = null;
        } else {
            $data['link_ujian'] = $request->link_ujian;
            $data['bank_soal_id'] = null;
        }

        $ujian->update($data);

        return redirect()->route('admin.ujian.index')->with('success', 'Jadwal ujian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return redirect()->route('admin.ujian.index')->with('success', 'Jadwal ujian berhasil dihapus!');
    }

    public function generateToken($id)
    {
        $ujian = Ujian::findOrFail($id);
        
        $newToken = strtoupper(substr(md5(uniqid('', true)), 0, 5));
        \Illuminate\Support\Facades\DB::table('ujian')
            ->where('id', $id)
            ->update([
                'token' => $newToken,
                'token_updated_at' => now(),
            ]);

        return redirect()->route('admin.ujian.index')->with('success', 'Token ujian berhasil diperbarui: ' . $newToken);
    }
}
