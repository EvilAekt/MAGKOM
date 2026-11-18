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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('tugas', function (Blueprint $table) {
            $table->string('link_referensi', 2048)->nullable()->change();
        });

        if (Schema::hasTable('tugas_template')) {
            Schema::table('tugas_template', function (Blueprint $table) {
                $table->string('link_referensi', 2048)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('tugas', function (Blueprint $table) {
            $table->string('link_referensi', 255)->nullable()->change();
        });

        if (Schema::hasTable('tugas_template')) {
            Schema::table('tugas_template', function (Blueprint $table) {
                $table->string('link_referensi', 255)->nullable()->change();
            });
        }
    }
};
