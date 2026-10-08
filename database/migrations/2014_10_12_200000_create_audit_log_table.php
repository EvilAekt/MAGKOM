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
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 36);
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('tipe', 15); // auth, aksi, service, akses_data
            $table->string('aksi', 100);
            $table->string('entitas', 60)->nullable();
            $table->unsignedBigInteger('entitas_id')->nullable();
            $table->json('nilai_lama')->nullable();
            $table->json('nilai_baru')->nullable();
            $table->json('detail')->nullable();
            $table->string('metode', 10)->nullable();
            $table->string('rute', 150)->nullable();
            $table->unsignedSmallInteger('status_http')->nullable();
            $table->unsignedInteger('durasi_ms')->nullable();
            $table->string('ip', 45);
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('waktu')->useCurrent();

            // Indeks performa penelusuran audit
            $table->index(['user_id', 'waktu']);
            $table->index(['entitas', 'entitas_id']);
            $table->index('request_id');
            $table->index(['tipe', 'waktu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
