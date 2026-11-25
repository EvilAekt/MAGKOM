<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('penempatan', function (Blueprint $table) {
                $table->dropUnique('penempatan_pendaftaran_id_unique');
                $table->unique(['pendaftaran_id', 'peserta_id'], 'penempatan_pendaftaran_peserta_unique');
            });

            return;
        }

        Schema::table('penempatan', function (Blueprint $table) {
            // Drop unique tunggal pendaftaran_id agar 1 pendaftaran kelompok dapat menaungi seluruh anggota tim
            $table->dropForeign(['pendaftaran_id']);
            $table->dropUnique('penempatan_pendaftaran_id_unique');

            // Tambahkan kembali foreign key dengan composite unique per pendaftaran dan peserta
            $table->foreign('pendaftaran_id')
                ->references('id')
                ->on('pendaftaran')
                ->nullOnDelete();

            $table->unique(['pendaftaran_id', 'peserta_id'], 'penempatan_pendaftaran_peserta_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('penempatan', function (Blueprint $table) {
                $table->dropUnique('penempatan_pendaftaran_peserta_unique');
                $table->unique('pendaftaran_id', 'penempatan_pendaftaran_id_unique');
            });

            return;
        }

        Schema::table('penempatan', function (Blueprint $table) {
            $table->dropForeign(['pendaftaran_id']);
            $table->dropUnique('penempatan_pendaftaran_peserta_unique');

            $table->foreign('pendaftaran_id')
                ->references('id')
                ->on('pendaftaran')
                ->nullOnDelete();

            $table->unique('pendaftaran_id', 'penempatan_pendaftaran_id_unique');
        });
    }
};
