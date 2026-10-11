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
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('link_referensi', 255)->nullable()->after('deskripsi');
            $table->string('file_lampiran', 255)->nullable()->after('link_referensi');
            $table->string('nama_file_lampiran', 255)->nullable()->after('file_lampiran');
            $table->unsignedInteger('ukuran_file_lampiran')->nullable()->after('nama_file_lampiran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn([
                'link_referensi',
                'file_lampiran',
                'nama_file_lampiran',
                'ukuran_file_lampiran',
            ]);
        });
    }
};
