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
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('user_id')->comment('Path berkas foto profil peserta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
