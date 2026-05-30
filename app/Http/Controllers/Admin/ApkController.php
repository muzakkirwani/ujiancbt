<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ApkController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        
        // Detect current server IP address for helper
        $server_ip = request()->server('SERVER_ADDR') ?? '192.168.1.xxx';
        if ($server_ip === '::1' || $server_ip === '127.0.0.1') {
            $server_ip = gethostbyname(gethostname());
        }
        $suggested_url = "http://" . $server_ip . "/cbt";
        $current_server_ip = $server_ip;

        return view('admin.apk', compact('settings', 'suggested_url', 'current_server_ip'));
    }

    public function downloadZip()
    {
        $zip_file = tempnam(sys_get_temp_dir(), 'cbt-apk-') . '.zip';
        $source_dir = base_path('android-app');

        if (!is_dir($source_dir)) {
            return back()->with('error', 'Folder source code Android tidak ditemukan!');
        }

        if (!class_exists('ZipArchive')) {
            return back()->with('error', 'Ekstensi PHP ZipArchive tidak aktif pada server ini!');
        }

        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source_dir),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($source_dir) + 1);
                    $relativePath = str_replace('\\', '/', $relativePath);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            return response()->download($zip_file, 'cbt-exam-browser.zip')->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Gagal membuat file ZIP!');
    }
}
