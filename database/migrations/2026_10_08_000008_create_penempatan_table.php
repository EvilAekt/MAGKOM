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
        Schema::create('penempatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->nullable()->unique()->constrained('pendaftaran')->nullOnDelete();
            $table->foreignId('peserta_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kuota_id')->nullable()->constrained('kuota')->nullOnDelete();
            $table->foreignId('slot_khusus_id')->nullable()->constrained('slot_khusus')->nullOnDelete();
            $table->foreignId('bidang_id')->nullable()->constrained('bidang')->nullOnDelete();
            $table->foreignId('pembimbing_id')->constrained('users')->restrictOnDelete();
            $table->string('jalur', 10)->comment('umum atau khusus');
            $table->string('sumber', 10)->comment('online atau manual');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tgl_mulai')->comment('Tanggal 1 bulan magang');
            $table->date('tgl_selesai')->comment('Tanggal akhir periode magang');
            $table->date('tgl_ditetapkan_selesai')->nullable();
            $table->date('tgl_nonaktif')->nullable()->comment('Tanggal berakhirnya masa tenggang');
            $table->string('status', 20)->default('aktif')->comment('aktif, selesai, mengundurkan_diri, nonaktif');
            $table->timestamps();

            $table->index(['status', 'tgl_selesai']);
            $table->index(['pembimbing_id', 'status']);
            $table->index(['peserta_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penempatan');
    }
};
