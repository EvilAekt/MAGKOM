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
        Schema::create('logbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penempatan_id')->constrained('penempatan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('kegiatan');
            $table->string('file_laporan', 255)->nullable();
            $table->string('link_laporan', 500)->nullable();
            $table->string('status', 15)->default('terkirim');
            $table->text('catatan_pembimbing')->nullable();
            $table->timestamps();

            $table->unique(['penempatan_id', 'tanggal']);
            $table->index(['penempatan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook');
    }
};
