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
        // 1. Tambah user_id pada kelompok_anggota untuk mengaitkan anggota tim ke akun login mandiri
        Schema::table('kelompok_anggota', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('kelompok_magang_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        // 2. Tambah kolom tipe dan kelompok_magang_id pada tabel tugas
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('tipe', 20)->default('individu')->after('penempatan_id');
            $table->foreignId('kelompok_magang_id')
                ->nullable()
                ->after('tipe')
                ->constrained('kelompok_magang')
                ->nullOnDelete();

            $table->index(['tipe', 'kelompok_magang_id']);
        });

        // 3. Buat tabel pivot penugasan spesifik peserta dalam project bersama / tugas
        Schema::create('tugas_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')
                ->constrained('tugas')
                ->cascadeOnDelete();
            $table->foreignId('penempatan_id')
                ->nullable()
                ->constrained('penempatan')
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tugas_id', 'user_id']);
            $table->index(['user_id', 'tugas_id']);
            $table->index(['penempatan_id']);
        });

        // 4. Tambah kolom file_surat_selesai pada tabel penempatan untuk surat keterangan selesai per peserta
        Schema::table('penempatan', function (Blueprint $table) {
            $table->string('file_surat_selesai', 255)->nullable()->after('file_surat_diterima');
            $table->timestamp('tgl_upload_surat_selesai')->nullable()->after('file_surat_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penempatan', function (Blueprint $table) {
            $table->dropColumn(['file_surat_selesai', 'tgl_upload_surat_selesai']);
        });

        Schema::dropIfExists('tugas_peserta');

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropForeign(['kelompok_magang_id']);
            $table->dropIndex(['tipe', 'kelompok_magang_id']);
            $table->dropColumn(['tipe', 'kelompok_magang_id']);
        });

        Schema::table('kelompok_anggota', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id']);
        });
    }
};
