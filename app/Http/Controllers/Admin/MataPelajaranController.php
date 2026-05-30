<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mata_pelajaran_list = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        return view('admin.mata_pelajaran', compact('mata_pelajaran_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mata_pelajarans,nama_mapel',
        ]);

        MataPelajaran::create($request->only('nama_mapel'));

        return redirect()->route('admin.mata_pelajaran.index')->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mata_pelajarans,nama_mapel,' . $id,
        ]);

        $mata_pelajaran = MataPelajaran::findOrFail($id);
        $mata_pelajaran->update($request->only('nama_mapel'));

        return redirect()->route('admin.mata_pelajaran.index')->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $mata_pelajaran = MataPelajaran::findOrFail($id);
        $mata_pelajaran->delete();

        return redirect()->route('admin.mata_pelajaran.index')->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}
