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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->nullable(); // Null untuk OAuth login
            $table->string('role', 20)->default('peserta');
            $table->string('provider', 20)->nullable();
            $table->string('provider_id', 191)->nullable();
            $table->foreignId('bidang_id')->nullable()->constrained('bidang');
            $table->boolean('status_aktif')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
            $table->rememberToken();
            $table->timestamps();

            // Indeks
            $table->index(['role', 'status_aktif']);
            $table->unique(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
