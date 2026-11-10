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
        Schema::table('bulan_magang', function (Blueprint $table) {
            $table->string('nama_bulan', 100)->nullable()->after('id')->comment('Contoh: Gelombang 3, Bulan November 2026');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulan_magang', function (Blueprint $table) {
            $table->dropColumn('nama_bulan');
        });
    }
};
