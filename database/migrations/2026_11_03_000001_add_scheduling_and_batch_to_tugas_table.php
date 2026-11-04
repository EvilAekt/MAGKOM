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
            $table->string('batch_id', 36)->nullable()->after('id')->index();
            $table->string('status_publikasi', 20)->default('dipublikasikan')->after('status')->index();
            $table->timestamp('jadwal_publikasi')->nullable()->after('status_publikasi')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropIndex(['status_publikasi']);
            $table->dropIndex(['jadwal_publikasi']);
            $table->dropColumn([
                'batch_id',
                'status_publikasi',
                'jadwal_publikasi',
            ]);
        });
    }
};
