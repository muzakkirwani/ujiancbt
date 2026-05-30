<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class SiswaController extends Controller
{
    public function index()
    {
        $users_list = Siswa::with('kelas')->orderBy('nama', 'asc')->get();
        $kelas_list = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.siswa', compact('users_list', 'kelas_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nisn' => 'required|string|max:20|unique:siswa,nisn',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'kelas_id' => 'required|exists:kelas,id',
            'username' => 'required|string|max:50|unique:siswa,username',
            'password' => 'required|string|min:4',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('nama', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'kelas_id', 'username');
        $data['password'] = Hash::make($request->password);
        $data['password_view'] = $request->password;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $request->username . '.' . $foto->getClientOriginalExtension();
            
            $destinationPath = public_path('assets/uploads/users');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            
            $foto->move($destinationPath, $filename);
            $data['foto'] = $filename;
        }

        Siswa::create($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'nisn' => 'required|string|max:20|unique:siswa,nisn,' . $id,
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'kelas_id' => 'required|exists:kelas,id',
            'username' => 'required|string|max:50|unique:siswa,username,' . $id,
            'password' => 'nullable|string|min:4',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('nama', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'kelas_id', 'username');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_view'] = $request->password;
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $request->username . '.' . $foto->getClientOriginalExtension();
            
            $destinationPath = public_path('assets/uploads/users');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            if ($siswa->foto && File::exists($destinationPath . '/' . $siswa->foto)) {
                File::delete($destinationPath . '/' . $siswa->foto);
            }
            
            $foto->move($destinationPath, $filename);
            $data['foto'] = $filename;
        }

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $destinationPath = public_path('assets/uploads/users');
        if ($siswa->foto && File::exists($destinationPath . '/' . $siswa->foto)) {
            File::delete($destinationPath . '/' . $siswa->foto);
        }
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.csv"',
        ];

        $callback = function() {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Nama Lengkap', 'NISN', 'Username', 'Password', 'Nama Kelas']);
            fputcsv($output, ['Siswa Contoh 1', '1234567890', 'siswa1', 'pass123', 'X RPL 1']);
            fputcsv($output, ['Siswa Contoh 2', '0987654321', 'siswa2', 'pass456', 'XI TKJ 2']);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_siswa' => 'required|file',
        ]);

        $file = $request->file('file_siswa');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $success = 0;
        $failed = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) < 5) continue;

            $nama = trim($data[0]);
            $nisn = trim($data[1]);
            $username = trim($data[2]);
            $password_plain = trim($data[3]);
            $nama_kelas = trim($data[4]);

            // Find kelas_id
            $kelas = Kelas::where('nama_kelas', $nama_kelas)->first();

            if ($kelas) {
                try {
                    // Check duplicate username or nisn
                    if (!Siswa::where('username', $username)->orWhere('nisn', $nisn)->exists()) {
                        Siswa::create([
                            'nama' => $nama,
                            'nisn' => $nisn,
                            'username' => $username,
                            'password' => Hash::make($password_plain),
                            'password_view' => $password_plain,
                            'kelas_id' => $kelas->id,
                        ]);
                        $success++;
                    } else {
                        $failed++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }
        fclose($handle);

        return redirect()->route('admin.siswa.index')
            ->with('success', "Import selesai: $success berhasil, $failed gagal.");
    }

    public function export()
    {
        $filename = "data_siswa_" . date('Ymd_His') . ".xls";
        $data = Siswa::with('kelas')->get()->sortBy(function($siswa) {
            return ($siswa->kelas->nama_kelas ?? '') . $siswa->nama;
        });

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($data) {
            echo '<table border="1">';
            echo '<tr>
                    <th style="background-color: #4f46e5; color: white;">No</th>
                    <th style="background-color: #4f46e5; color: white;">Nama Lengkap</th>
                    <th style="background-color: #4f46e5; color: white;">NISN</th>
                    <th style="background-color: #4f46e5; color: white;">Kelas</th>
                    <th style="background-color: #4f46e5; color: white;">Username</th>
                    <th style="background-color: #4f46e5; color: white;">Password (Plain)</th>
                  </tr>';

            foreach ($data as $i => $row) {
                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . htmlspecialchars($row->nama) . '</td>';
                echo '<td>\'' . htmlspecialchars($row->nisn) . '</td>';
                echo '<td>' . htmlspecialchars($row->kelas->nama_kelas ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($row->username) . '</td>';
                echo '<td>' . htmlspecialchars($row->password_view) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cetakKartu($id)
    {
        $settings = \App\Models\Setting::first();
        $s = Siswa::with('kelas')->findOrFail($id);
        return view('admin.print.kartu_single', compact('settings', 's'));
    }
}
