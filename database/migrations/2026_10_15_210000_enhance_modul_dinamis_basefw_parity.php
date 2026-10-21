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
        Schema::table('modul_dinamis', function (Blueprint $table) {
            $table->string('tipe_sumber', 20)->default('dynamic')->after('slug'); // 'dynamic' atau 'physical_table'
            $table->string('nama_tabel_fisik', 64)->nullable()->after('tipe_sumber');
            $table->string('primary_key', 64)->default('id')->after('nama_tabel_fisik');
            $table->boolean('is_manual_pk')->default(false)->after('primary_key');
            $table->boolean('is_public_read')->default(false)->after('status_aktif');
            $table->string('scope_mode', 20)->default('none')->after('is_public_read'); // 'none', 'bidang_scope', 'creator_scope'
            $table->json('log_aksi')->nullable()->after('izin_aksi'); // {"list": false, "create": true, "edit": true, "delete": true, "export_csv": true, "export_pdf": true}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modul_dinamis', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_sumber',
                'nama_tabel_fisik',
                'primary_key',
                'is_manual_pk',
                'is_public_read',
                'scope_mode',
                'log_aksi',
            ]);
        });
    }
};
