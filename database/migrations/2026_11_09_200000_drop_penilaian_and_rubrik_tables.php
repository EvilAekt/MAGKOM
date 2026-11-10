<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('penilaian_riwayat');
        Schema::dropIfExists('penilaian_detail');
        Schema::dropIfExists('penilaian_details');
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('rubrik');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fitur penilaian dan skala predikat dihapus secara permanen dari sistem MagKom.
    }
};
