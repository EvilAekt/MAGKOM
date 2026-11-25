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
        // 1. Catat pengirim progres pada progres_log agar tugas tim menampilkan siapa anggota yang mengunggah
        Schema::table('progres_log', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('tugas_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        // 2. Pastikan tugas.penempatan_id bersifat nullable untuk Tugas Tim murni
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `tugas` MODIFY `penempatan_id` BIGINT UNSIGNED NULL');
        } elseif ($driver === 'sqlite') {
            $this->makeTugasPenempatanNullableSqlite();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progres_log', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    private function makeTugasPenempatanNullableSqlite(): void
    {
        $tableSql = DB::table('sqlite_master')
            ->where('type', 'table')
            ->where('name', 'tugas')
            ->value('sql');

        if (! is_string($tableSql) || ! str_contains($tableSql, '"penempatan_id" integer not null')) {
            return;
        }

        $indexes = DB::table('sqlite_master')
            ->where('type', 'index')
            ->where('tbl_name', 'tugas')
            ->whereNotNull('sql')
            ->pluck('sql')
            ->all();

        $newTableSql = str_replace(
            '"penempatan_id" integer not null',
            '"penempatan_id" integer',
            $tableSql
        );
        $tmpTableSql = preg_replace('/CREATE TABLE\s+"tugas"/i', 'CREATE TABLE "tugas_tmp_nullable"', $newTableSql, 1);

        if (! is_string($tmpTableSql)) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement($tmpTableSql);
        DB::statement('INSERT INTO "tugas_tmp_nullable" SELECT * FROM "tugas"');
        DB::statement('DROP TABLE "tugas"');
        DB::statement('ALTER TABLE "tugas_tmp_nullable" RENAME TO "tugas"');
        foreach ($indexes as $indexSql) {
            DB::statement($indexSql);
        }
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
