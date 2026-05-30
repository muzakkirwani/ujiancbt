<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('soals')) {
            Schema::create('soals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bank_soal_id')->constrained('bank_soals')->onDelete('cascade');
                $table->string('jenis_soal')->default('pilihan_ganda');
                $table->text('teks_soal');
                $table->string('gambar_soal')->nullable();
                $table->text('opsi_a');
                $table->text('opsi_b');
                $table->text('opsi_c');
                $table->text('opsi_d');
                $table->text('opsi_e')->nullable();
                $table->char('kunci_jawaban', 1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
