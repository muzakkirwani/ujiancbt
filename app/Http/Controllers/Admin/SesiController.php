<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index()
    {
        $sesi_list = Sesi::orderBy('nama_sesi', 'asc')->get();
        return view('admin.sesi', compact('sesi_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:50',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
        ]);

        Sesi::create($request->only('nama_sesi', 'jam_mulai', 'jam_berakhir'));

        return redirect()->route('admin.sesi.index')->with('success', 'Sesi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:50',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
        ]);

        $sesi = Sesi::findOrFail($id);
        $sesi->update($request->only('nama_sesi', 'jam_mulai', 'jam_berakhir'));

        return redirect()->route('admin.sesi.index')->with('success', 'Sesi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->delete();

        return redirect()->route('admin.sesi.index')->with('success', 'Sesi berhasil dihapus!');
    }
}
