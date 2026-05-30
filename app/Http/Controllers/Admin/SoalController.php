<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SoalController extends Controller
{
    public function index($bank_soal_id)
    {
        $bankSoal = BankSoal::findOrFail($bank_soal_id);
        $soals = Soal::where('bank_soal_id', $bank_soal_id)->orderBy('id', 'asc')->get();

        return view('admin.bank_soal.soal', compact('bankSoal', 'soals'));
    }

    public function store(Request $request, $bank_soal_id)
    {
        $rules = [
            'jenis_soal' => 'required|in:pilihan_ganda,esai',
            'teks_soal' => 'required|string',
            'gambar_soal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->jenis_soal == 'pilihan_ganda') {
            $rules['opsi_a'] = 'required|string';
            $rules['opsi_b'] = 'required|string';
            $rules['opsi_c'] = 'required|string';
            $rules['opsi_d'] = 'required|string';
            $rules['opsi_e'] = 'nullable|string';
            $rules['kunci_jawaban'] = 'required|in:A,B,C,D,E';
        }

        $request->validate($rules);

        $bankSoal = BankSoal::findOrFail($bank_soal_id);
        $data = $request->except('gambar_soal');
        $data['bank_soal_id'] = $bankSoal->id;

        // Sanitize all text fields to handle smart quotes & special characters
        $textFields = ['teks_soal', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'];
        foreach ($textFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->sanitizeText($data[$field]);
            }
        }

        if ($request->jenis_soal == 'esai') {
            $data['opsi_a'] = '-';
            $data['opsi_b'] = '-';
            $data['opsi_c'] = '-';
            $data['opsi_d'] = '-';
            $data['opsi_e'] = null;
            $data['kunci_jawaban'] = '-';
        }

        if ($request->hasFile('gambar_soal')) {
            $file = $request->file('gambar_soal');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/uploads/soal');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            $file->move($destinationPath, $filename);
            $data['gambar_soal'] = $filename;
        }

        Soal::create($data);

        return redirect()->route('admin.bank_soal.soal.index', $bankSoal->id)->with('success', 'Soal berhasil ditambahkan!');
    }

    public function update(Request $request, $bank_soal_id, $id)
    {
        $soal = Soal::where('bank_soal_id', $bank_soal_id)->findOrFail($id);

        $rules = [
            'jenis_soal' => 'required|in:pilihan_ganda,esai',
            'teks_soal' => 'required|string',
            'gambar_soal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->jenis_soal == 'pilihan_ganda') {
            $rules['opsi_a'] = 'required|string';
            $rules['opsi_b'] = 'required|string';
            $rules['opsi_c'] = 'required|string';
            $rules['opsi_d'] = 'required|string';
            $rules['opsi_e'] = 'nullable|string';
            $rules['kunci_jawaban'] = 'required|in:A,B,C,D,E';
        }

        $request->validate($rules);

        $data = $request->except('gambar_soal');

        // Sanitize all text fields to handle smart quotes & special characters
        $textFields = ['teks_soal', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'];
        foreach ($textFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->sanitizeText($data[$field]);
            }
        }

        if ($request->jenis_soal == 'esai') {
            $data['opsi_a'] = '-';
            $data['opsi_b'] = '-';
            $data['opsi_c'] = '-';
            $data['opsi_d'] = '-';
            $data['opsi_e'] = null;
            $data['kunci_jawaban'] = '-';
        }

        if ($request->hasFile('gambar_soal')) {
            $file = $request->file('gambar_soal');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/uploads/soal');
            
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            if ($soal->gambar_soal && File::exists($destinationPath . '/' . $soal->gambar_soal)) {
                File::delete($destinationPath . '/' . $soal->gambar_soal);
            }

            $file->move($destinationPath, $filename);
            $data['gambar_soal'] = $filename;
        }

        $soal->update($data);

        return redirect()->route('admin.bank_soal.soal.index', $bank_soal_id)->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy($bank_soal_id, $id)
    {
        $soal = Soal::where('bank_soal_id', $bank_soal_id)->findOrFail($id);
        $destinationPath = public_path('assets/uploads/soal');
        
        if ($soal->gambar_soal && File::exists($destinationPath . '/' . $soal->gambar_soal)) {
            File::delete($destinationPath . '/' . $soal->gambar_soal);
        }

        $soal->delete();

        return redirect()->route('admin.bank_soal.soal.index', $bank_soal_id)->with('success', 'Soal berhasil dihapus!');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_soal.csv"',
        ];

        $callback = function() {
            $output = fopen('php://output', 'w');
            // Header Row
            fputcsv($output, ['Teks Soal', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'Kunci Jawaban']);
            // Example Row
            fputcsv($output, ['Ibukota negara Indonesia adalah...', 'Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Medan', 'A']);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request, $bank_soal_id)
    {
        $request->validate([
            'file_soal' => 'required|file|mimes:csv,txt',
        ]);

        $bankSoal = BankSoal::findOrFail($bank_soal_id);
        $file = $request->file('file_soal');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $success = 0;
        $failed = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Check minimum columns (Teks, A, B, C, D, Kunci) -> 6 columns
            if (count($data) < 6) continue;

            $teks_soal = $this->sanitizeText(trim($data[0] ?? ''));
            $opsi_a = $this->sanitizeText(trim($data[1] ?? ''));
            $opsi_b = $this->sanitizeText(trim($data[2] ?? ''));
            $opsi_c = $this->sanitizeText(trim($data[3] ?? ''));
            $opsi_d = $this->sanitizeText(trim($data[4] ?? ''));
            $opsi_e = $this->sanitizeText(trim($data[5] ?? ''));
            
            // Kunci Jawaban might be in index 6 if Opsi E is present, or index 5 if no Opsi E.
            // Let's assume standard 7 columns format as per template.
            $kunci_jawaban = strtoupper(trim($data[6] ?? ''));

            if ($teks_soal && $opsi_a && $opsi_b && $opsi_c && $opsi_d && in_array($kunci_jawaban, ['A','B','C','D','E'])) {
                Soal::create([
                    'bank_soal_id' => $bankSoal->id,
                    'jenis_soal' => 'pilihan_ganda',
                    'teks_soal' => $teks_soal,
                    'opsi_a' => $opsi_a,
                    'opsi_b' => $opsi_b,
                    'opsi_c' => $opsi_c,
                    'opsi_d' => $opsi_d,
                    'opsi_e' => $opsi_e,
                    'kunci_jawaban' => $kunci_jawaban,
                ]);
                $success++;
            } else {
                $failed++;
            }
        }
        fclose($handle);

        return redirect()->route('admin.bank_soal.soal.index', $bankSoal->id)
            ->with('success', "Import selesai: $success berhasil, $failed gagal (pastikan format CSV benar dan Kunci Jawaban A/B/C/D/E).");
    }
    /**
     * Sanitize text by converting Windows-1252 smart quotes and special
     * characters to their standard UTF-8 equivalents.
     */
    private function sanitizeText(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        // Try to detect and convert from Windows-1252 encoding
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        }

        // Replace common smart/curly quotes and special chars with ASCII equivalents
        $replacements = [
            "\xC2\x93" => '"',  // Windows-1252 left double quote (as UTF-8 bytes)
            "\xC2\x94" => '"',  // Windows-1252 right double quote (as UTF-8 bytes)
            "\xC2\x91" => "'",  // Windows-1252 left single quote
            "\xC2\x92" => "'",  // Windows-1252 right single quote
            "\xC2\x96" => '-',  // Windows-1252 en-dash
            "\xC2\x97" => '--', // Windows-1252 em-dash
            "\xE2\x80\x9C" => '"',  // UTF-8 left double quotation mark
            "\xE2\x80\x9D" => '"',  // UTF-8 right double quotation mark
            "\xE2\x80\x98" => "'",  // UTF-8 left single quotation mark
            "\xE2\x80\x99" => "'",  // UTF-8 right single quotation mark
            "\xE2\x80\x93" => '-',  // UTF-8 en-dash
            "\xE2\x80\x94" => '--', // UTF-8 em-dash
            "\xE2\x80\xA6" => '...', // UTF-8 ellipsis
        ];

        $text = strtr($text, $replacements);

        // Remove any remaining non-UTF-8 characters
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        return $text;
    }
}
