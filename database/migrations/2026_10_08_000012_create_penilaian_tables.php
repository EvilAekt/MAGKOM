<?php

declare(strict_types=1);

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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penempatan_id')->unique()->constrained('penempatan')->cascadeOnDelete();
            $table->foreignId('pembimbing_id')->constrained('users')->restrictOnDelete();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->char('predikat', 1)->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 10)->default('draft');
            $table->timestamps();

            $table->index(['pembimbing_id', 'status']);
        });

        Schema::create('penilaian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian')->cascadeOnDelete();
            $table->foreignId('rubrik_id')->constrained('rubrik')->restrictOnDelete();
            $table->unsignedTinyInteger('skor');
            $table->unsignedTinyInteger('bobot_snapshot');
            $table->timestamps();

            $table->unique(['penilaian_id', 'rubrik_id']);
        });

        Schema::create('penilaian_riwayat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian')->cascadeOnDelete();
            $table->foreignId('diubah_oleh')->constrained('users')->restrictOnDelete();
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->text('alasan');
            $table->timestamp('waktu')->useCurrent();

            $table->index(['penilaian_id', 'waktu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_riwayat');
        Schema::dropIfExists('penilaian_detail');
        Schema::dropIfExists('penilaian');
    }
};
