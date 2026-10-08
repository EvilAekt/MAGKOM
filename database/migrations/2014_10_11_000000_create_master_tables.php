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
        // 1. Master Bidang
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang', 100);
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 2. Master Jurusan
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jurusan', 100)->unique();
            $table->string('kategori', 10); // 'smk' atau 'mahasiswa'
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 3. Master Rubrik Penilaian
        Schema::create('rubrik', function (Blueprint $table) {
            $table->id();
            $table->string('kriteria', 100);
            $table->unsignedTinyInteger('bobot');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 4. Pengaturan Sistem
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 100)->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('rubrik');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('bidang');
    }
};
