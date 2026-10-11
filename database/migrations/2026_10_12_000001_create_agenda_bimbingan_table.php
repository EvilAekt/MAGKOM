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
        Schema::create('agenda_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penempatan_id')->constrained('penempatan')->cascadeOnDelete();
            $table->foreignId('pembimbing_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul_kegiatan', 150);
            $table->string('tipe', 20)->default('tatap_muka');
            $table->date('tgl_bimbingan');
            $table->string('jam_mulai', 10)->default('09:00');
            $table->string('lokasi_ruang', 150)->nullable();
            $table->text('notulensi_arahan')->nullable();
            $table->string('status', 20)->default('terjadwal');
            $table->timestamps();

            $table->index(['pembimbing_id', 'status']);
            $table->index(['penempatan_id', 'status']);
            $table->index('tgl_bimbingan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_bimbingan');
    }
};
