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
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 40)->comment('Kode unik dokumen: surat_pengantar, cv, dll');
            $table->string('nama', 100)->comment('Nama berkas pada formulir');
            $table->string('jenjang', 5)->comment('smk, d3, s1');
            $table->boolean('wajib')->default(true);
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['kode', 'jenjang'], 'uq_jenis_dokumen_kode_jenjang');
            $table->index(['jenjang', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumen');
    }
};
