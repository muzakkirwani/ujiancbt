<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role_filter = $request->query('role', 'pengawas');
        
        $users_list = User::where('role', $role_filter)
            ->where('role', '!=', 'siswa')
            ->orderBy('nama', 'asc')
            ->get();

        $subjects = \App\Models\MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('admin.users', compact('users_list', 'role_filter', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:30',
            'role' => 'required|in:admin,pengawas',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:4',
            'mata_pelajaran' => 'nullable|array',
        ]);

        $mata_pelajaran_str = $request->has('mata_pelajaran') && is_array($request->mata_pelajaran)
            ? implode(', ', $request->mata_pelajaran)
            : null;

        User::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'role' => $request->role,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'mata_pelajaran' => $mata_pelajaran_str,
        ]);

        return redirect()->route('admin.users.index', ['role' => $request->role])
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:30',
            'role' => 'required|in:admin,pengawas',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:4',
            'mata_pelajaran' => 'nullable|array',
        ]);

        $mata_pelajaran_str = $request->has('mata_pelajaran') && is_array($request->mata_pelajaran)
            ? implode(', ', $request->mata_pelajaran)
            : null;

        $data = [
            'nama' => $request->nama,
            'nip' => $request->nip,
            'role' => $request->role,
            'username' => $request->username,
            'mata_pelajaran' => $mata_pelajaran_str,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index', ['role' => $request->role])
            ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $role = $user->role;
        $user->delete();

        return redirect()->route('admin.users.index', ['role' => $role])
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}
