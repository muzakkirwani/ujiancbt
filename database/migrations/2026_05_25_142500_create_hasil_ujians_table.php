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
        if (!Schema::hasTable('hasil_ujians')) {
            Schema::create('hasil_ujians', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ujian_id')->constrained('ujian')->onDelete('cascade');
                $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
                $table->integer('benar')->default(0);
                $table->integer('salah')->default(0);
                $table->integer('kosong')->default(0);
                $table->decimal('nilai', 5, 2)->default(0);
                $table->json('jawaban_detail')->nullable(); // Stores detailed answers {soal_id: 'A'}
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_ujians');
    }
};
