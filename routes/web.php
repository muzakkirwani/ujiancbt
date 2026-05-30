<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SesiController;
use App\Http\Controllers\Admin\UjianController;
use App\Http\Controllers\Admin\RuangController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ApkController;
use App\Http\Controllers\Admin\BankSoalController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\SoalController;
use App\Http\Controllers\Pengawas\PengawasController;
use App\Http\Controllers\Siswa\SiswaController as PortalSiswaController;

// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginForm']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout']); // legacy support

// Temporary Migration Route for Ujian Table
Route::get('/run-migrations-ujian', function () {
    try {
        $messages = [];
        
        if (!\Illuminate\Support\Facades\Schema::hasColumn('ujian', 'jenis_ujian')) {
            \Illuminate\Support\Facades\Schema::table('ujian', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('jenis_ujian')->default('googleform');
                $table->foreignId('bank_soal_id')->nullable()->constrained('bank_soals')->onDelete('set null');
            });
            $messages[] = "Kolom 'jenis_ujian' dan 'bank_soal_id' berhasil ditambahkan!";
        } else {
            $messages[] = "Kolom sudah ada.";
        }

        // Pakai raw query agar tidak butuh doctrine/dbal untuk mengubah kolom menjadi nullable
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE ujian MODIFY link_ujian VARCHAR(255) NULL");
            $messages[] = "Kolom 'link_ujian' berhasil diubah menjadi nullable.";
        } catch (\Exception $e) {
            $messages[] = "Gagal mengubah nullable: " . $e->getMessage();
        }

        // Hasil Ujian migration
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('hasil_ujians')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => 'database/migrations/2026_05_25_142500_create_hasil_ujians_table.php']);
                $messages[] = "Tabel 'hasil_ujians' berhasil dibuat.";
            } else {
                $messages[] = "Tabel 'hasil_ujians' sudah ada.";
            }

            if (!\Illuminate\Support\Facades\Schema::hasColumn('hasil_ujians', 'status')) {
                \Illuminate\Support\Facades\Schema::table('hasil_ujians', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('status')->default('selesai')->after('nilai');
                });
                $messages[] = "Kolom 'status' berhasil ditambahkan ke tabel hasil_ujians.";
            }
        } catch (\Exception $e) {
            $messages[] = "Gagal membuat tabel hasil_ujians: " . $e->getMessage();
        }

        return implode("<br>", $messages) . "<br><br>Silakan kembali ke halaman Admin dan Tambah Jadwal lagi!";
    } catch (\Exception $e) {
        return "Gagal update tabel: " . $e->getMessage();
    }
});

// Admin Routes (role: admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboard::class, 'index']);

    // Kelas Management
    Route::resource('kelas', KelasController::class);

    // Mata Pelajaran Management
    Route::resource('mata_pelajaran', MataPelajaranController::class)->except(['create', 'show', 'edit']);

    // Siswa Management
    Route::get('siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
    Route::get('siswa/download-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.download_template');
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('siswa/{id}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetak_kartu');
    Route::resource('siswa', SiswaController::class);

    // Users Management
    Route::resource('users', UserController::class);

    // Sesi Management
    Route::resource('sesi', SesiController::class);

    // Ujian Management
    Route::post('ujian/{id}/generate-token', [UjianController::class, 'generateToken'])->name('ujian.generate_token');
    Route::resource('ujian', UjianController::class);

    // Hasil Ujian Management
    Route::get('hasil_ujian', [\App\Http\Controllers\Admin\HasilUjianController::class, 'index'])->name('hasil_ujian.index');
    Route::get('hasil_ujian/{id}', [\App\Http\Controllers\Admin\HasilUjianController::class, 'show'])->name('hasil_ujian.show');
    Route::delete('hasil_ujian/{id}/reset', [\App\Http\Controllers\Admin\HasilUjianController::class, 'reset'])->name('hasil_ujian.reset');

    // Siswa Aktif Ujian Management
    Route::get('siswa_aktif', [\App\Http\Controllers\Admin\SiswaAktifController::class, 'index'])->name('siswa_aktif.index');
    Route::post('siswa_aktif/{id}/reset', [\App\Http\Controllers\Admin\SiswaAktifController::class, 'reset'])->name('siswa_aktif.reset');
    Route::post('siswa_aktif/{id}/selesai', [\App\Http\Controllers\Admin\SiswaAktifController::class, 'selesai'])->name('siswa_aktif.selesai');

    // Bank Soal Management
    Route::resource('bank_soal', BankSoalController::class)->except(['create', 'show', 'edit']);
    Route::get('bank_soal/{bank_soal}/soal/download-template', [SoalController::class, 'downloadTemplate'])->name('bank_soal.soal.download_template');
    Route::post('bank_soal/{bank_soal}/soal/import', [SoalController::class, 'import'])->name('bank_soal.soal.import');
    Route::get('bank_soal/{bank_soal}/soal', [SoalController::class, 'index'])->name('bank_soal.soal.index');
    Route::post('bank_soal/{bank_soal}/soal', [SoalController::class, 'store'])->name('bank_soal.soal.store');
    Route::put('bank_soal/{bank_soal}/soal/{soal}', [SoalController::class, 'update'])->name('bank_soal.soal.update');
    Route::delete('bank_soal/{bank_soal}/soal/{soal}', [SoalController::class, 'destroy'])->name('bank_soal.soal.destroy');

    // Ruang Management
    Route::post('ruang/plot-siswa', [RuangController::class, 'plotSiswa'])->name('ruang.plot_siswa');
    Route::post('ruang/plot-pengawas', [RuangController::class, 'plotPengawas'])->name('ruang.plot_pengawas');
    Route::put('ruang/tugas/{id}', [RuangController::class, 'updateTugas'])->name('ruang.tugas.update');
    Route::delete('ruang/tugas/{id}', [RuangController::class, 'destroyTugas'])->name('ruang.tugas.destroy');
    Route::get('ruang/{id}/cetak-kartu', [RuangController::class, 'cetakKartu'])->name('ruang.cetak_kartu');
    Route::get('ruang/{id}/cetak-daftar-hadir', [RuangController::class, 'cetakDaftarHadir'])->name('ruang.cetak_daftar_hadir');
    Route::resource('ruang', RuangController::class);

    // Settings Management
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // APK Management
    Route::get('apk', [ApkController::class, 'index'])->name('apk.index');
    Route::post('apk/download', [ApkController::class, 'downloadZip'])->name('apk.download');

    // Attendance Lists (Daftar Hadir)
    Route::get('daftar-hadir', [RuangController::class, 'daftarHadirList'])->name('daftar_hadir.index');
    Route::get('daftar-hadir/cetak', [RuangController::class, 'cetakHadirUjian'])->name('daftar_hadir.cetak');
});

// Pengawas Routes (role: pengawas)
Route::middleware(['auth', 'role:pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
    Route::get('/', [PengawasController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [PengawasController::class, 'index']);
    Route::post('/token/refresh', [PengawasController::class, 'updateToken'])->name('token.refresh');

    // Bank Soal Management
    Route::resource('bank_soal', \App\Http\Controllers\Pengawas\BankSoalController::class)->except(['create', 'show', 'edit']);
    Route::get('bank_soal/{bank_soal}/soal/download-template', [\App\Http\Controllers\Pengawas\SoalController::class, 'downloadTemplate'])->name('bank_soal.soal.download_template');
    Route::post('bank_soal/{bank_soal}/soal/import', [\App\Http\Controllers\Pengawas\SoalController::class, 'import'])->name('bank_soal.soal.import');
    Route::get('bank_soal/{bank_soal}/soal', [\App\Http\Controllers\Pengawas\SoalController::class, 'index'])->name('bank_soal.soal.index');
    Route::post('bank_soal/{bank_soal}/soal', [\App\Http\Controllers\Pengawas\SoalController::class, 'store'])->name('bank_soal.soal.store');
    Route::put('bank_soal/{bank_soal}/soal/{soal}', [\App\Http\Controllers\Pengawas\SoalController::class, 'update'])->name('bank_soal.soal.update');
    Route::delete('bank_soal/{bank_soal}/soal/{soal}', [\App\Http\Controllers\Pengawas\SoalController::class, 'destroy'])->name('bank_soal.soal.destroy');

    // Hasil Ujian Management
    Route::get('hasil_ujian', [\App\Http\Controllers\Pengawas\HasilUjianController::class, 'index'])->name('hasil_ujian.index');
    Route::get('hasil_ujian/{id}', [\App\Http\Controllers\Pengawas\HasilUjianController::class, 'show'])->name('hasil_ujian.show');
    Route::delete('hasil_ujian/{id}/reset', [\App\Http\Controllers\Pengawas\HasilUjianController::class, 'reset'])->name('hasil_ujian.reset');

    // Siswa Aktif Ujian Management
    Route::get('siswa_aktif', [\App\Http\Controllers\Pengawas\SiswaAktifController::class, 'index'])->name('siswa_aktif.index');
    Route::post('siswa_aktif/{id}/reset', [\App\Http\Controllers\Pengawas\SiswaAktifController::class, 'reset'])->name('siswa_aktif.reset');
    Route::post('siswa_aktif/{id}/selesai', [\App\Http\Controllers\Pengawas\SiswaAktifController::class, 'selesai'])->name('siswa_aktif.selesai');
});

// Siswa Routes (guard: siswa)
Route::middleware(['auth:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [PortalSiswaController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [PortalSiswaController::class, 'index']);
    Route::get('/exam/{id}', [PortalSiswaController::class, 'exam'])->name('exam');
    Route::post('/exam/{id}/submit', [PortalSiswaController::class, 'submitExam'])->name('exam.submit');
    Route::post('/exam/{id}/autosave', [PortalSiswaController::class, 'autoSave'])->name('exam.autosave');
    Route::get('/heartbeat/{ujian_id}', function ($ujian_id) {
        $siswa = auth()->guard('siswa')->user();
        if ($siswa) {
            $hasil = \App\Models\HasilUjian::where('ujian_id', $ujian_id)
                ->where('siswa_id', $siswa->id)
                ->first();
            if ($hasil) {
                if ($hasil->status === 'berjalan') {
                    $hasil->update(['updated_at' => now()]);
                }
                return response()->json(['status' => $hasil->status]);
            }
        }
        return response()->json(['status' => 'none']);
    })->name('heartbeat');
});

// Route to run optimizations (Local & Production Server)
Route::get('/run-laravel-optimizations', function () {
    try {
        $messages = [];
        
        // 1. Clear compiled files and caches to start fresh
        \Illuminate\Support\Facades\Artisan::call('clear-compiled');
        $messages[] = "Compiled files cleared.";
        
        // 2. Cache configuration
        \Illuminate\Support\Facades\Artisan::call('config:cache');
        $messages[] = "Configuration cached successfully.";
        
        // 3. Clear route cache (disabled cache because Laravel routes file contains Closure routes)
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        $messages[] = "Route cache cleared to keep dynamic routes running smoothly.";
        
        // 4. Cache views (Blade compiler)
        \Illuminate\Support\Facades\Artisan::call('view:cache');
        $messages[] = "Blade views pre-compiled & cached successfully.";
        
        return "<h2>Laravel Application Optimization Console</h2><br>✅ " . implode("<br>✅ ", $messages) . "<br><br><strong>Aplikasi sekarang jauh lebih ringan, stabil, dan cepat diakses di localhost & internet!</strong>";
    } catch (\Exception $e) {
        return "Gagal melakukan optimasi: " . $e->getMessage();
    }
});

