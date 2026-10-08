<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bulan_magang_id')->constrained('bulan_magang')->cascadeOnDelete();
            $table->foreignId('kuota_id')->constrained('kuota')->cascadeOnDelete();
            $table->unsignedTinyInteger('durasi_bulan')->comment('Durasi magang 1 s/d 6 bulan');
            $table->string('status', 25)->default('berkas_belum_lengkap')->comment('Status alur seleksi');
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable()->comment('Catatan revisi atau alasan penolakan');
            $table->timestamp('diajukan_at')->nullable();
            $table->timestamp('diputuskan_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'bulan_magang_id'], 'uq_pendaftaran_user_bulan');
            $table->index(['bulan_magang_id', 'status']);
            $table->index(['status', 'diajukan_at']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE pendaftaran ADD CONSTRAINT chk_pendaftaran_durasi CHECK (durasi_bulan BETWEEN 1 AND 6)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
