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
        // 1. Pastikan foreign key pendaftaran_id memiliki index tersendiri
        Schema::table('dokumen', function (Blueprint $table) {
            if (! Schema::hasColumn('dokumen', 'kelompok_anggota_id')) {
                $table->foreignId('kelompok_anggota_id')
                    ->nullable()
                    ->after('jenis_dokumen_id')
                    ->constrained('kelompok_anggota')
                    ->cascadeOnDelete();
            }

            // Buat index eksplisit untuk foreign key pendaftaran_id
            $table->index('pendaftaran_id', 'dokumen_pendaftaran_id_index');
        });

        // 2. Drop unique lama dan ganti dengan unique baru
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropUnique('uq_dokumen_pendaftaran_jenis');
            $table->unique(
                ['pendaftaran_id', 'jenis_dokumen_id', 'kelompok_anggota_id'],
                'uq_dokumen_pendaftaran_jenis_anggota'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropUnique('uq_dokumen_pendaftaran_jenis_anggota');
            $table->unique(['pendaftaran_id', 'jenis_dokumen_id'], 'uq_dokumen_pendaftaran_jenis');

            if (Schema::hasColumn('dokumen', 'kelompok_anggota_id')) {
                $table->dropForeign(['kelompok_anggota_id']);
                $table->dropColumn('kelompok_anggota_id');
            }

            $table->dropIndex('dokumen_pendaftaran_id_index');
        });
    }
};
