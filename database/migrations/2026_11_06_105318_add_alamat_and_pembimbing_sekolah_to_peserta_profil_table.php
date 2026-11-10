<?php

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
            $table->string('nama_pembimbing_sekolah', 150)->nullable()->after('institusi');
            $table->text('alamat')->nullable()->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_profil', function (Blueprint $table) {
            $table->dropColumn(['nama_pembimbing_sekolah', 'alamat']);
        });
    }
};
