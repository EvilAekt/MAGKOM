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
        Schema::dropIfExists('survei_kepuasan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('survei_kepuasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penempatan_id')->constrained('penempatan')->cascadeOnDelete();
            $table->unsignedTinyInteger('skor_mentoring');
            $table->unsignedTinyInteger('skor_tugas');
            $table->unsignedTinyInteger('skor_fasilitas');
            $table->unsignedTinyInteger('skor_nps');
            $table->text('umpan_balik')->nullable();
            $table->text('saran')->nullable();
            $table->timestamps();
        });
    }
};
