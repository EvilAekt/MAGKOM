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
        Schema::table('logbook', function (Blueprint $table) {
            $table->unsignedInteger('minggu_ke')->nullable()->after('penempatan_id');
            $table->index(['penempatan_id', 'minggu_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbook', function (Blueprint $table) {
            $table->dropIndex(['penempatan_id', 'minggu_ke']);
            $table->dropColumn('minggu_ke');
        });
    }
};
