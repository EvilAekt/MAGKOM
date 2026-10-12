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
        Schema::create('survei_kepuasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penempatan_id')->unique()->constrained('penempatan')->cascadeOnDelete();
            $table->unsignedTinyInteger('skor_mentoring'); // 1 - 5
            $table->unsignedTinyInteger('skor_tugas');     // 1 - 5
            $table->unsignedTinyInteger('skor_fasilitas'); // 1 - 5
            $table->unsignedTinyInteger('skor_nps');       // 0 - 10
            $table->text('umpan_balik')->nullable();
            $table->text('saran')->nullable();
            $table->timestamps();

            $table->index('skor_nps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survei_kepuasan');
    }
};
