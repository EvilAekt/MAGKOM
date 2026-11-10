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
        Schema::table('pendaftaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pendaftaran', 'file_surat_diterima')) {
                $table->string('file_surat_diterima', 255)->nullable()->after('catatan');
            }
            if (! Schema::hasColumn('pendaftaran', 'surat_diterima_uploaded_at')) {
                $table->timestamp('surat_diterima_uploaded_at')->nullable()->after('file_surat_diterima');
            }
        });

        Schema::table('penempatan', function (Blueprint $table) {
            if (! Schema::hasColumn('penempatan', 'file_surat_diterima')) {
                $table->string('file_surat_diterima', 255)->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penempatan', function (Blueprint $table) {
            if (Schema::hasColumn('penempatan', 'file_surat_diterima')) {
                $table->dropColumn(['file_surat_diterima']);
            }
        });

        Schema::table('pendaftaran', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('pendaftaran', 'surat_diterima_uploaded_at')) {
                $dropCols[] = 'surat_diterima_uploaded_at';
            }
            if (Schema::hasColumn('pendaftaran', 'file_surat_diterima')) {
                $dropCols[] = 'file_surat_diterima';
            }
            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
