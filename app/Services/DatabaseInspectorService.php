<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseInspectorService
{
    /**
     * Daftar tabel sistem yang tidak boleh diubah atau diexpose ke generator.
     */
    public const PROTECTED_TABLES = [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'job_batches',
        'jobs',
    ];

    /**
     * Mengambil seluruh tabel basis data yang aman untuk digenerate.
     *
     * @return array<int, string>
     */
    public function getAvailableTables(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $tables = [];

        if ($driver === 'sqlite') {
            $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
            foreach ($rows as $row) {
                $tables[] = (string) $row->name;
            }
        } elseif ($driver === 'mysql') {
            $databaseName = $connection->getDatabaseName();
            $rows = DB::select('SELECT TABLE_NAME as name FROM information_schema.tables WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME', [$databaseName]);
            foreach ($rows as $row) {
                $tables[] = (string) $row->name;
            }
        } else {
            $tables = Schema::getTableListing();
        }

        return array_values(array_filter($tables, fn ($tbl) => ! in_array($tbl, self::PROTECTED_TABLES, true)));
    }

    /**
     * Menginspeksi skema kolom dari tabel fisik terpilih.
     *
     * @return array{
     *     table: string,
     *     primary_key: string,
     *     is_manual_pk: bool,
     *     has_unit_scope: bool,
     *     columns: array<int, array{
     *         name: string,
     *         label: string,
     *         data_type: string,
     *         is_pk: bool,
     *         is_nullable: bool,
     *         is_searchable: bool,
     *         is_visible: bool
     *     }>
     * }
     */
    public function inspectTable(string $table): array
    {
        if (in_array($table, self::PROTECTED_TABLES, true) || ! Schema::hasTable($table)) {
            throw new \InvalidArgumentException("Tabel {$table} tidak valid atau dilindungi.");
        }

        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $columns = [];
        $primaryKey = 'id';
        $hasAutoIncrementId = false;
        $hasUnitScope = false;

        if ($driver === 'mysql') {
            $databaseName = $connection->getDatabaseName();
            $rawColumns = DB::select('SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, EXTRA, IS_NULLABLE
                FROM information_schema.columns
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION', [$databaseName, $table]);

            foreach ($rawColumns as $col) {
                $colName = (string) $col->COLUMN_NAME;
                $isPk = strtoupper((string) $col->COLUMN_KEY) === 'PRI';
                $isAutoInc = str_contains(strtolower((string) $col->EXTRA), 'auto_increment');

                if (strtolower($colName) === 'id' && $isPk) {
                    $hasAutoIncrementId = true;
                    $primaryKey = 'id';
                } elseif ($isPk && ! $hasAutoIncrementId) {
                    $primaryKey = $colName;
                }

                if (in_array(strtolower($colName), ['id_unit_kerja', 'bidang_id', 'unit_id'], true)) {
                    $hasUnitScope = true;
                }

                $columns[] = [
                    'name' => $colName,
                    'label' => $this->labelFromColumn($colName),
                    'data_type' => $this->mapSqlTypeToMagkomType((string) $col->DATA_TYPE),
                    'is_pk' => $isPk,
                    'is_nullable' => strtoupper((string) $col->IS_NULLABLE) === 'YES',
                    'is_searchable' => in_array($this->mapSqlTypeToMagkomType((string) $col->DATA_TYPE), ['text', 'textarea', 'number'], true),
                    'is_visible' => ! in_array($colName, ['password', 'remember_token', 'deleted_at'], true),
                ];
            }
        } else {
            // Driver SQLite / Fallback Schema Manager
            $colNames = Schema::getColumnListing($table);
            foreach ($colNames as $colName) {
                $isPk = (strtolower($colName) === 'id');
                if ($isPk) {
                    $hasAutoIncrementId = true;
                }

                if (in_array(strtolower($colName), ['id_unit_kerja', 'bidang_id', 'unit_id'], true)) {
                    $hasUnitScope = true;
                }

                $columns[] = [
                    'name' => $colName,
                    'label' => $this->labelFromColumn($colName),
                    'data_type' => $this->guessTypeFromName($colName),
                    'is_pk' => $isPk,
                    'is_nullable' => true,
                    'is_searchable' => true,
                    'is_visible' => ! in_array($colName, ['password', 'remember_token', 'deleted_at'], true),
                ];
            }
        }

        return [
            'table' => $table,
            'primary_key' => $primaryKey,
            'is_manual_pk' => ! $hasAutoIncrementId,
            'has_unit_scope' => $hasUnitScope,
            'columns' => $columns,
        ];
    }

    /**
     * Mengubah nama kolom menjadi label yang manusiawi (Porting labelFromColumn Basefw).
     */
    public function labelFromColumn(string $column): string
    {
        $title = Str::headline($column);
        $title = str_ireplace(['Id ', ' Id', ' Kode ', ' Tgl '], ['ID ', ' ID', ' Kode ', ' Tanggal '], $title);

        return trim($title);
    }

    /**
     * Memetakan tipe data SQL ke 10 tipe data formulir MagKom.
     */
    private function mapSqlTypeToMagkomType(string $sqlType): string
    {
        $sqlType = strtolower($sqlType);

        if (in_array($sqlType, ['int', 'integer', 'bigint', 'smallint', 'tinyint', 'mediumint', 'decimal', 'float', 'double'], true)) {
            if ($sqlType === 'tinyint') {
                return 'boolean';
            }

            return 'number';
        }

        if (in_array($sqlType, ['text', 'mediumtext', 'longtext'], true)) {
            return 'textarea';
        }

        if (in_array($sqlType, ['date', 'year'], true)) {
            return 'date';
        }

        if (in_array($sqlType, ['datetime', 'timestamp'], true)) {
            return 'datetime';
        }

        return 'text';
    }

    /**
     * Tebak tipe data sederhana dari nama kolom jika database tidak mengembalikan metadata rinci.
     */
    private function guessTypeFromName(string $name): string
    {
        $nameLower = strtolower($name);
        if (str_ends_with($nameLower, '_id') || str_starts_with($nameLower, 'id_')) {
            return 'number';
        }
        if (str_contains($nameLower, 'tanggal') || str_contains($nameLower, 'tgl') || str_ends_with($nameLower, '_at')) {
            return 'date';
        }
        if (str_contains($nameLower, 'deskripsi') || str_contains($nameLower, 'keterangan') || str_contains($nameLower, 'catatan')) {
            return 'textarea';
        }
        if (str_starts_with($nameLower, 'is_') || str_starts_with($nameLower, 'status_')) {
            return 'boolean';
        }
        if (str_contains($nameLower, 'jumlah') || str_contains($nameLower, 'kuota') || str_contains($nameLower, 'total')) {
            return 'number';
        }

        return 'text';
    }
}
