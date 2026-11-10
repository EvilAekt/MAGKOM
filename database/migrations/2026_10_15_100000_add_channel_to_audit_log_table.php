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
        Schema::table('audit_log', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_log', 'channel')) {
                $table->string('channel', 40)->default('system-security')->after('tipe')->index();
            }
            if (! Schema::hasColumn('audit_log', 'severity')) {
                $table->string('severity', 20)->default('info')->after('channel')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            if (Schema::hasColumn('audit_log', 'severity')) {
                $table->dropColumn('severity');
            }
            if (Schema::hasColumn('audit_log', 'channel')) {
                $table->dropColumn('channel');
            }
        });
    }
};
