<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Decommission permanen tabel route_metrics (MAGKOM-DECOM-TELEMETRY-2026-11-13).
     */
    public function up(): void
    {
        Schema::dropIfExists('route_metrics');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tabel telemetri sudah resmi didecommission secara permanen dari sistem MagKom.
    }
};
