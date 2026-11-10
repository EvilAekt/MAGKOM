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
        Schema::create('tugas_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 50)->default('Umum');
            $table->string('kompleksitas', 10)->default('sedang');
            $table->string('link_referensi', 255)->nullable();
            $table->boolean('is_global')->default(false);
            $table->timestamps();

            $table->index(['pembimbing_id', 'kategori']);
            $table->index('is_global');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_template');
    }
};
