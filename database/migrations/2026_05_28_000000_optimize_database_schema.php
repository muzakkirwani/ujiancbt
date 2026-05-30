<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Upgrade soals table columns to LONGTEXT to support base64 images
        if (Schema::hasTable('soals')) {
            try {
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `teks_soal` LONGTEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_a` LONGTEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_b` LONGTEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_c` LONGTEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_d` LONGTEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_e` LONGTEXT NULL");
            } catch (\Exception $e) {
                // Skip if columns cannot be modified
            }
        }

        // 2. Add indexes to performance-critical columns
        $indexes = [
            'hasil_ujians' => ['ujian_id', 'siswa_id', 'status', 'updated_at'],
            'ujian' => ['kelas_id', 'sesi_id', 'tanggal', 'bank_soal_id'],
            'ruang_siswa' => ['ruang_id', 'user_id'],
            'ruang_pengawas' => ['ruang_id', 'user_id', 'sesi_id', 'tanggal'],
            'siswa' => ['kelas_id', 'username'],
            'soals' => ['bank_soal_id'],
        ];

        foreach ($indexes as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    try {
                        $indexName = "idx_{$table}_{$column}";
                        
                        // Check if index already exists to avoid errors
                        $existingIndexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");
                        if (empty($existingIndexes)) {
                            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` (`{$column}`)");
                        }
                    } catch (\Exception $e) {
                        // Skip if index already exists or fails
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert columns to original TEXT size if needed
        if (Schema::hasTable('soals')) {
            try {
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `teks_soal` TEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_a` TEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_b` TEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_c` TEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_d` TEXT NOT NULL");
                DB::statement("ALTER TABLE `soals` MODIFY COLUMN `opsi_e` TEXT NULL");
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // 2. Drop indexes
        $indexes = [
            'hasil_ujians' => ['ujian_id', 'siswa_id', 'status', 'updated_at'],
            'ujian' => ['kelas_id', 'sesi_id', 'tanggal', 'bank_soal_id'],
            'ruang_siswa' => ['ruang_id', 'user_id'],
            'ruang_pengawas' => ['ruang_id', 'user_id', 'sesi_id', 'tanggal'],
            'siswa' => ['kelas_id', 'username'],
            'soals' => ['bank_soal_id'],
        ];

        foreach ($indexes as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    try {
                        $indexName = "idx_{$table}_{$column}";
                        DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
                    } catch (\Exception $e) {
                        // Ignore if index doesn't exist
                    }
                }
            }
        }
    }
};
