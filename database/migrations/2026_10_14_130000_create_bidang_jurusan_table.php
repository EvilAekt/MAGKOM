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
        if (!Schema::hasTable('bidang_jurusan')) {
            Schema::create('bidang_jurusan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnDelete();
                $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['bidang_id', 'jurusan_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidang_jurusan');
    }
};
