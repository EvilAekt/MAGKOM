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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('jenis_dokumen_id')->constrained('jenis_dokumen')->restrictOnDelete();
            $table->string('path', 255)->comment('Lokasi simpan file pada private disk');
            $table->string('nama_asli', 255)->comment('Nama asli berkas saat diunggah');
            $table->string('mime', 100)->comment('MIME type hasil inspeksi magic bytes');
            $table->unsignedInteger('ukuran')->comment('Ukuran berkas dalam bytes');
            $table->char('hash', 64)->comment('SHA-256 checksum integritas berkas');
            $table->timestamps();

            $table->unique(['pendaftaran_id', 'jenis_dokumen_id'], 'uq_dokumen_pendaftaran_jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
