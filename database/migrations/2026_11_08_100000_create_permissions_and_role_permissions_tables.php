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
        // 1. Master Definisi Hak Akses / Modul Sistem
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_modul', 150);
            $table->string('slug', 100)->unique();
            $table->string('kategori', 50)->default('Umum');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_system')->default(false);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['kategori', 'urutan']);
        });

        // 2. Pemetaan Hak Akses ke Role (General/Global Policy per Role)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30); // superadmin, admin, pembimbing, peserta
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role', 'permission_id'], 'uq_role_permission');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
