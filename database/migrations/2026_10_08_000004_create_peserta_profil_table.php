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
        Schema::create('peserta_profil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nim_nisn', 20)->comment('Nomor Induk Siswa/Mahasiswa');
            $table->string('institusi', 150)->comment('Nama Sekolah / Perguruan Tinggi');
            $table->foreignId('jurusan_id')->constrained('jurusan')->restrictOnDelete();
            $table->string('jenjang', 5)->comment('smk, d3, s1');
            $table->string('no_hp', 20)->comment('Nomor WhatsApp / Telepon aktif');
            $table->timestamps();

            $table->index(['jurusan_id', 'jenjang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_profil');
    }
};
