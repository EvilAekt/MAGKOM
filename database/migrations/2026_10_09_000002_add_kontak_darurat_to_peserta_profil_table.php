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
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->string('nama_kontak_darurat', 100)->nullable()->after('no_hp')->comment('Nama Kontak Darurat');
            $table->string('no_kontak_darurat', 20)->nullable()->after('nama_kontak_darurat')->comment('Nomor Telepon Kontak Darurat');
            $table->string('hubungan_kontak_darurat', 50)->nullable()->after('no_kontak_darurat')->comment('Hubungan (Orang Tua/Wali/Keluarga)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->dropColumn(['nama_kontak_darurat', 'no_kontak_darurat', 'hubungan_kontak_darurat']);
        });
    }
};
