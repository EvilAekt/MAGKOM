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
        Schema::create('slot_khusus', function (Blueprint $table) {
            $table->id();
            $table->date('bulan_mulai')->unique()->comment('Selalu tanggal 1 bulan magang');
            $table->unsignedSmallInteger('jumlah')->default(3)->comment('Kapasitas slot khusus');
            $table->unsignedSmallInteger('terisi')->default(0)->comment('Jumlah slot khusus terpakai');
            $table->boolean('aktif')->default(true);
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['aktif', 'bulan_mulai']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE slot_khusus ADD CONSTRAINT chk_slot_khusus_terisi_lte_jumlah CHECK (terisi <= jumlah)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_khusus');
    }
};
