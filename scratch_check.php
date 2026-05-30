<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\BankSoal;

echo "=== USERS ===\n";
foreach (User::all() as $user) {
    echo "ID: {$user->id} | Name: {$user->nama} | Role: {$user->role} | Mapel: '{$user->mata_pelajaran}'\n";
}

echo "\n=== MATA PELAJARAN ===\n";
foreach (MataPelajaran::all() as $mp) {
    echo "ID: {$mp->id} | Name: {$mp->nama_mapel}\n";
}

echo "\n=== BANK SOAL ===\n";
foreach (BankSoal::all() as $bs) {
    echo "ID: {$bs->id} | Kode: {$bs->kode_bank} | Mapel: '{$bs->mata_pelajaran}'\n";
}
