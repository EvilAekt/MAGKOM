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
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penempatan_id')->constrained('penempatan')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 50);
            $table->string('kompleksitas', 10);
            $table->date('deadline');
            $table->string('status', 10)->default('belum');
            $table->timestamps();

            $table->index(['penempatan_id', 'status']);
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
