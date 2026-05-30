<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix the soals table charset to utf8mb4 to support special characters
     * like smart quotes (""), em dashes, etc.
     */
    public function up(): void
    {
        if (Schema::hasTable('soals')) {
            DB::statement('ALTER TABLE soals CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }

        // Also fix bank_soals if it exists
        if (Schema::hasTable('bank_soals')) {
            DB::statement('ALTER TABLE bank_soals CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed — utf8mb4 is the correct charset
    }
};
