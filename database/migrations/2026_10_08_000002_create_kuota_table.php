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
        Schema::create('kuota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulan_magang_id')->constrained('bulan_magang')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusan')->restrictOnDelete();
            $table->unsignedSmallInteger('jumlah')->default(0)->comment('Kapasitas kuota umum');
            $table->unsignedSmallInteger('terisi')->default(0)->comment('Jumlah peserta diterima');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['bulan_magang_id', 'jurusan_id'], 'uq_kuota_bulan_jurusan');
            $table->index(['bulan_magang_id', 'aktif']);
        });

        // Tambahkan check constraint terisi <= jumlah bila database adalah MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE kuota ADD CONSTRAINT chk_kuota_terisi_lte_jumlah CHECK (terisi <= jumlah)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuota');
    }
};
