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
        Schema::create('kelompok_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ketua_user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('nama_kelompok', 150);
            $table->timestamps();
        });

        Schema::create('kelompok_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_magang_id')
                ->constrained('kelompok_magang')
                ->cascadeOnDelete();
            $table->string('nama', 255);
            $table->string('nim_nisn', 50)->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->string('tipe', 20)->default('mandiri')->after('kuota_id');
            $table->foreignId('kelompok_magang_id')
                ->nullable()
                ->after('tipe')
                ->constrained('kelompok_magang')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropForeign(['kelompok_magang_id']);
            $table->dropColumn(['tipe', 'kelompok_magang_id']);
        });

        Schema::dropIfExists('kelompok_anggota');
        Schema::dropIfExists('kelompok_magang');
    }
};
