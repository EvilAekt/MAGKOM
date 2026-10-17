<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->timestamp('persetujuan_wali_at')->nullable()->after('hubungan_kontak_darurat');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->dropColumn('persetujuan_wali_at');
        });
    }
};
