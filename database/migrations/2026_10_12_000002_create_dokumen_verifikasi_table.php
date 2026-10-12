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
        Schema::create('dokumen_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unik', 64)->unique();
            $table->string('tipe_dokumen', 32); // surat_diterima, lembar_nilai, sertifikat
            $table->foreignId('penempatan_id')->constrained('penempatan')->cascadeOnDelete();
            $table->string('nomor_dokumen', 100);
            $table->date('tanggal_terbit');
            $table->string('status_keabsahan', 20)->default('sah'); // sah, dicabut, kedaluwarsa
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['kode_unik', 'status_keabsahan']);
            $table->index('penempatan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_verifikasi');
    }
};
