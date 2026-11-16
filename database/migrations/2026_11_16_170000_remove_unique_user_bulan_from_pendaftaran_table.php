<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Relaksasi unique constraint (user_id, bulan_magang_id) pada tabel pendaftaran
     * agar calon peserta yang status pendaftarannya Ditolak atau Dibatalkan dapat
     * mengajukan pendaftaran magang baru kembali tanpa error duplicate key MySQL (1062).
     * Aturan 1 pendaftaran aktif tetap ditegakkan secara ketat pada domain layer (BR-17).
     */
    public function up(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            // 1. Tambah index komposit non-unik terlebih dahulu untuk menjaga performa
            // query dan foreign key index referensial di MySQL
            $table->index(['user_id', 'bulan_magang_id'], 'idx_pendaftaran_user_bulan');

            // 2. Lepaskan unique constraint lama yang memblokir pendaftaran ulang peserta ditolak
            $table->dropUnique('uq_pendaftaran_user_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            // 1. Pulihkan kembali unique constraint
            $table->unique(['user_id', 'bulan_magang_id'], 'uq_pendaftaran_user_bulan');

            // 2. Hapus index komposit non-unik
            $table->dropIndex('idx_pendaftaran_user_bulan');
        });
    }
};
