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
        Schema::create('bulan_magang', function (Blueprint $table) {
            $table->id();
            $table->date('bulan_mulai')->unique()->comment('Selalu tanggal 1 bulan magang');
            $table->date('tgl_buka')->comment('Tanggal mulai buka pendaftaran');
            $table->date('tgl_tutup')->comment('Tanggal tutup pendaftaran');
            $table->string('override_status', 10)->nullable()->comment('tersedia, tutup, segera, atau null');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['aktif', 'bulan_mulai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulan_magang');
    }
};
