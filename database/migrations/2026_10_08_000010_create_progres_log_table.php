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
        Schema::create('progres_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->text('catatan');
            $table->string('file', 255)->nullable();
            $table->date('tgl_lapor');
            $table->string('status_review', 15)->default('menunggu');
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index(['tugas_id', 'status_review']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_log');
    }
};
