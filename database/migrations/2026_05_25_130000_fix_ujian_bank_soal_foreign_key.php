<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop the old broken FK that points to 'bank_soal' (singular, old native table)
        try {
            DB::statement('ALTER TABLE `ujian` DROP FOREIGN KEY `fk_ujian_bank_soal`');
        } catch (\Exception $e) {
            // FK may already be gone or named differently — ignore
        }

        // Step 2: Drop any other duplicate FK on bank_soal_id just in case
        try {
            DB::statement('ALTER TABLE `ujian` DROP FOREIGN KEY `ujian_bank_soal_id_foreign`');
        } catch (\Exception $e) {
            // ignore
        }

        // Step 3: Add correct FK pointing to 'bank_soals' (plural, Laravel table)
        try {
            DB::statement('ALTER TABLE `ujian` ADD CONSTRAINT `ujian_bank_soal_id_foreign` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soals` (`id`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // If FK already correct, ignore
        }

        // Step 4: Make link_ujian nullable if not already
        try {
            DB::statement('ALTER TABLE `ujian` MODIFY `link_ujian` VARCHAR(255) NULL');
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `ujian` DROP FOREIGN KEY `ujian_bank_soal_id_foreign`');
        } catch (\Exception $e) {}
    }
};
