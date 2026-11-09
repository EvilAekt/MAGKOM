<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabaseService
{
    /**
     * Batas maksimum arsip cadangan yang dipertahankan (FIFO retention).
     */
    public const MAX_BACKUPS_RETAINED = 30;

    /**
     * Batas ambang minimum sisa disk bebas sebelum diizinkan membuat dump (MB).
     */
    public const MIN_FREE_DISK_MB = 500;

    protected string $backupDir;

    public function __construct(
        protected AuditService $auditService
    ) {
        $this->backupDir = storage_path('app/backups');
    }

    /**
     * Mendapatkan direktori penyimpanan arsip cadangan basis data.
     */
    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * Mendapatkan daftar berkas cadangan yang tersedia, terurut dari yang terbaru.
     *
     * @return Collection<int, array{
     *     filename: string,
     *     size_mb: float,
     *     created_at: string,
     *     timestamp: int,
     *     checksum: string,
     *     is_compressed: bool,
     *     format: string
     * }>
     */
    public function listBackups(): Collection
    {
        File::ensureDirectoryExists($this->backupDir);

        return collect(File::files($this->backupDir))
            ->filter(function ($file) {
                return (bool) preg_match('/^backup-magkom-[a-zA-Z0-9_\-]+\.(sql\.gz|sql|sqlite)$/', $file->getFilename());
            })
            ->map(function ($file) {
                $filename = $file->getFilename();
                $isGz = str_ends_with($filename, '.sql.gz');
                $isSqlite = str_ends_with($filename, '.sqlite');

                return [
                    'filename' => $filename,
                    'size_mb' => round($file->getSize() / (1024 * 1024), 2),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime())->translatedFormat('d M Y H:i:s'),
                    'timestamp' => $file->getMTime(),
                    'checksum' => hash_file('sha256', $file->getRealPath()),
                    'is_compressed' => $isGz,
                    'format' => $isGz ? 'Gzip SQL' : ($isSqlite ? 'SQLite' : 'Raw SQL'),
                ];
            })
            ->sortByDesc('timestamp')
            ->values();
    }

    /**
     * Mendapatkan daftar berkas cadangan dalam bentuk paginasi (LengthAwarePaginator).
     */
    public function getPaginatedBackups(int $perPage = 10, ?int $page = null): LengthAwarePaginator
    {
        $allBackups = $this->listBackups();
        $currentPage = max(1, (int) ($page ?? request()->input('page', 1)));
        $offset = ($currentPage - 1) * $perPage;

        $itemsForCurrentPage = $allBackups->slice($offset, $perPage)->values();

        return new LengthAwarePaginator(
            items: $itemsForCurrentPage,
            total: $allBackups->count(),
            perPage: $perPage,
            currentPage: $currentPage,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * Membaca telemetri penyimpanan dan estimasi ruang disk bebas.
     *
     * @return array{
     *     total_backups: int,
     *     total_size_mb: float,
     *     free_disk_mb: float,
     *     is_disk_low: bool
     * }
     */
    public function getDiskTelemetry(): array
    {
        File::ensureDirectoryExists($this->backupDir);

        $backups = $this->listBackups();
        $totalSizeMb = round((float) $backups->sum('size_mb'), 2);

        $freeBytes = @disk_free_space($this->backupDir);
        $freeDiskMb = ($freeBytes !== false) ? round($freeBytes / (1024 * 1024), 2) : 9999.0;
        $isDiskLow = ($freeBytes !== false) && ($freeDiskMb < self::MIN_FREE_DISK_MB);

        return [
            'total_backups' => $backups->count(),
            'total_size_mb' => $totalSizeMb,
            'free_disk_mb' => $freeDiskMb,
            'is_disk_low' => $isDiskLow,
        ];
    }

    /**
     * Memicu pembuatan snapshot basis data instan.
     *
     * @return array{
     *     filename: string,
     *     size_mb: float,
     *     checksum: string,
     *     driver: string,
     *     path: string
     * }
     *
     * @throws \RuntimeException
     */
    public function createSnapshot(?User $triggeredBy = null): array
    {
        File::ensureDirectoryExists($this->backupDir);

        // 1. Guardrail Kapasitas Disk
        $freeBytes = @disk_free_space($this->backupDir);
        if ($freeBytes !== false && $freeBytes < (self::MIN_FREE_DISK_MB * 1024 * 1024)) {
            $freeMb = round($freeBytes / (1024 * 1024), 1);
            throw new \RuntimeException("Ruang penyimpanan server tidak mencukupi (sisa {$freeMb} MB, minimal dibutuhkan ".self::MIN_FREE_DISK_MB.' MB).');
        }

        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $timestamp = date('Y-m-d-His');

        if ($driver === 'sqlite') {
            $filename = "backup-magkom-sqlite-{$timestamp}.sqlite";
            $targetPath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;
            $this->dumpSqlite($targetPath, $timestamp);
        } else {
            // Default MySQL / PostgreSQL / MariaDB: Streaming Gzip Compressed (.sql.gz)
            $filename = "backup-magkom-{$driver}-{$timestamp}.sql.gz";
            $targetPath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;
            $this->dumpMysqlCompressed($targetPath, $timestamp);
        }

        if (! file_exists($targetPath)) {
            throw new \RuntimeException('Gagal memverifikasi integritas berkas cadangan yang baru dibuat.');
        }

        $sizeMb = round(filesize($targetPath) / (1024 * 1024), 2);
        $checksum = hash_file('sha256', $targetPath);

        // 2. Pangkas cadangan lama melebihi kuota 10 snapshot (FIFO)
        $this->pruneOldBackups(self::MAX_BACKUPS_RETAINED);

        // 3. Catat jejak audit trail forensik (AC-13-6)
        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'backup',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'backup.snapshot_generated',
                'filename' => $filename,
                'size_mb' => $sizeMb,
                'checksum' => $checksum,
                'driver' => $driver,
            ],
            user: $triggeredBy,
            channel: 'backup-database',
            severity: 'info'
        );

        return [
            'filename' => $filename,
            'size_mb' => $sizeMb,
            'checksum' => $checksum,
            'driver' => $driver,
            'path' => $targetPath,
        ];
    }

    /**
     * Memangkas snapshot cadangan lama agar tidak melampaui batas retensi maksimum.
     */
    public function pruneOldBackups(int $keepMax = self::MAX_BACKUPS_RETAINED): int
    {
        $backups = $this->listBackups();
        if ($backups->count() <= $keepMax) {
            return 0;
        }

        $prunedCount = 0;
        $toPrune = $backups->slice($keepMax);

        foreach ($toPrune as $item) {
            $path = $this->backupDir.DIRECTORY_SEPARATOR.$item['filename'];
            if (file_exists($path)) {
                @unlink($path);
                $prunedCount++;
            }
        }

        return $prunedCount;
    }

    /**
     * Memvalidasi dan mencari path berkas cadangan dengan mitigasi Path Traversal (SEC-11).
     */
    public function findBackupPath(string $filename): ?string
    {
        if (! preg_match('/^backup-magkom-[a-zA-Z0-9_\-]+\.(sql\.gz|sql|sqlite)$/', $filename)) {
            return null;
        }

        $cleanFilename = basename($filename);
        $filePath = $this->backupDir.DIRECTORY_SEPARATOR.$cleanFilename;

        if (! file_exists($filePath)) {
            return null;
        }

        $realPath = realpath($filePath);
        $realBackupDir = realpath($this->backupDir);

        if ($realPath === false || $realBackupDir === false || ! str_starts_with($realPath, $realBackupDir)) {
            return null;
        }

        return $realPath;
    }

    /**
     * Menghapus snapshot cadangan terpilih secara aman.
     */
    public function deleteBackup(string $filename, ?User $triggeredBy = null): bool
    {
        $realPath = $this->findBackupPath($filename);
        if (! $realPath) {
            return false;
        }

        $checksum = hash_file('sha256', $realPath);
        $deleted = @unlink($realPath);

        if ($deleted) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'backup',
                idData: null,
                nilaiLama: null,
                nilaiBaru: null,
                detail: [
                    'aksi' => 'backup.destroyed',
                    'filename' => $filename,
                    'checksum' => $checksum,
                ],
                user: $triggeredBy,
                channel: 'backup-database',
                severity: 'warning'
            );
        }

        return $deleted;
    }

    /**
     * Generator dump SQLite (lokal & testing).
     */
    protected function dumpSqlite(string $targetPath, string $timestamp): void
    {
        $dbPath = (string) config('database.connections.sqlite.database');

        if ($dbPath !== ':memory:' && file_exists($dbPath)) {
            copy($dbPath, $targetPath);
        } else {
            $schema = DB::select("SELECT sql FROM sqlite_master WHERE sql IS NOT NULL AND type IN ('table', 'index', 'view')");
            $dump = "-- MagKom SQLite Snapshot: {$timestamp}\n\n";
            foreach ($schema as $s) {
                $dump .= $s->sql.";\n";
            }
            File::put($targetPath, $dump);
        }
    }

    /**
     * Generator dump MySQL dengan Hybrid Engine:
     * 1. Coba gunakan binary native mysqldump via Process jika tersedia.
     * 2. Fallback ke streaming chunked PDO + gzwrite() on-the-fly untuk mencegah OOM.
     */
    protected function dumpMysqlCompressed(string $targetPath, string $timestamp): void
    {
        $mysqldumpPath = $this->resolveMysqldumpBinary();

        if ($mysqldumpPath !== null) {
            $success = $this->runNativeMysqldump($mysqldumpPath, $targetPath);
            if ($success) {
                return;
            }
        }

        // Fallback Engine: Memory-efficient Chunked Streaming PDO via Gzip
        $this->runChunkedPdoMysqlDump($targetPath, $timestamp);
    }

    /**
     * Mencari path binary mysqldump pada sistem host.
     */
    protected function resolveMysqldumpBinary(): ?string
    {
        $customPath = (string) config('database.connections.mysql.dump.dump_binary_path', env('MYSQLDUMP_PATH', ''));
        if (! empty($customPath) && file_exists($customPath)) {
            return $customPath;
        }

        // Lokasi standar yang umum di Windows (Laragon/XAMPP) & Linux
        $candidates = [
            'F:\\XAMPEEK\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        // Cek apakah 'mysqldump' tersedia di global PATH via 'where' (Windows) atau 'which' (Linux)
        $lookupCmd = (PHP_OS_FAMILY === 'Windows') ? ['where', 'mysqldump'] : ['which', 'mysqldump'];
        try {
            $process = new Process($lookupCmd);
            $process->run();
            if ($process->isSuccessful()) {
                $output = trim($process->getOutput());
                $firstLine = strtok($output, "\r\n");
                if ($firstLine && file_exists($firstLine)) {
                    return $firstLine;
                }
            }
        } catch (\Throwable) {
        }

        return null;
    }

    /**
     * Menjalankan mysqldump native process dan mengalirkan langsung ke berkas .sql.gz.
     */
    protected function runNativeMysqldump(string $binaryPath, string $targetPath): bool
    {
        $host = (string) config('database.connections.mysql.host', '127.0.0.1');
        $port = (string) config('database.connections.mysql.port', '3306');
        $database = (string) config('database.connections.mysql.database', 'magkom_db');
        $username = (string) config('database.connections.mysql.username', 'root');
        $password = (string) config('database.connections.mysql.password', '');

        $cmd = [
            $binaryPath,
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--default-character-set=utf8mb4',
            '--routines',
            '--triggers',
            $database,
        ];

        $gz = gzopen($targetPath, 'wb9');
        if (! $gz) {
            return false;
        }

        try {
            // Password disalurkan melalui variabel lingkungan MYSQL_PWD agar aman dari intipan process list
            $env = array_merge($_ENV, $_SERVER, [
                'MYSQL_PWD' => $password,
            ]);

            $process = new Process($cmd, null, $env, null, 300);
            $process->run(function ($type, $buffer) use ($gz) {
                if ($type === Process::OUT) {
                    gzwrite($gz, $buffer);
                }
            });

            gzclose($gz);

            if (! $process->isSuccessful() || filesize($targetPath) === 0) {
                @unlink($targetPath);

                return false;
            }

            return true;
        } catch (\Throwable) {
            if (is_resource($gz)) {
                gzclose($gz);
            }
            if (file_exists($targetPath)) {
                @unlink($targetPath);
            }

            return false;
        }
    }

    /**
     * Fallback Engine: Streaming chunked PDO langsung ke Gzip pointer.
     * Menggunakan memory constant (< 32 MB) dan aman terhadap multi-megabyte datasets.
     */
    protected function runChunkedPdoMysqlDump(string $targetPath, string $timestamp): void
    {
        $gz = gzopen($targetPath, 'wb9');
        if (! $gz) {
            throw new \RuntimeException("Gagal membuka stream penulisan gzip pada: {$targetPath}");
        }

        try {
            $pdo = DB::connection()->getPdo();

            gzwrite($gz, "-- MagKom Database Snapshot Export: {$timestamp}\n");
            gzwrite($gz, "-- Generator: App\\Services\\BackupDatabaseService (Chunked Streaming PDO)\n");
            gzwrite($gz, "SET FOREIGN_KEY_CHECKS=0;\n");
            gzwrite($gz, "SET NAMES utf8mb4;\n\n");

            $tables = DB::select('SHOW TABLES');

            foreach ($tables as $tableObj) {
                $tableName = reset($tableObj);
                if (! is_string($tableName) || empty($tableName)) {
                    continue;
                }

                // DDL: CREATE TABLE
                try {
                    $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    if (! empty($createTableResult)) {
                        $createRow = (array) $createTableResult[0];
                        $createSql = $createRow['Create Table'] ?? reset($createRow);
                        gzwrite($gz, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                        gzwrite($gz, $createSql.";\n\n");
                    }
                } catch (\Throwable) {
                    gzwrite($gz, "-- Could not retrieve CREATE TABLE for: `{$tableName}`\n");
                }

                // DML: Stream Data Baris demi Baris menggunakan cursor unbuffered PDO
                try {
                    $cursor = DB::table($tableName)->cursor();
                    $batchValues = [];
                    $batchSize = 250;
                    $escapedColumns = null;

                    foreach ($cursor as $row) {
                        $rowArr = (array) $row;
                        if ($escapedColumns === null) {
                            $escapedColumns = array_map(fn ($col) => "`{$col}`", array_keys($rowArr));
                        }

                        $escapedValues = array_map(function ($val) use ($pdo) {
                            if ($val === null) {
                                return 'NULL';
                            }
                            if (is_bool($val)) {
                                return $val ? '1' : '0';
                            }
                            if (is_int($val) || is_float($val)) {
                                return (string) $val;
                            }

                            return $pdo->quote((string) $val);
                        }, array_values($rowArr));

                        $batchValues[] = '('.implode(', ', $escapedValues).')';

                        if (count($batchValues) >= $batchSize) {
                            $sql = "INSERT INTO `{$tableName}` (".implode(', ', $escapedColumns).") VALUES\n".implode(",\n", $batchValues).";\n";
                            gzwrite($gz, $sql);
                            $batchValues = [];
                        }
                    }

                    if (! empty($batchValues) && $escapedColumns !== null) {
                        $sql = "INSERT INTO `{$tableName}` (".implode(', ', $escapedColumns).") VALUES\n".implode(",\n", $batchValues).";\n";
                        gzwrite($gz, $sql);
                    }

                    gzwrite($gz, "\n");
                } catch (\Throwable) {
                    gzwrite($gz, "-- Could not dump records for: `{$tableName}`\n");
                }
            }

            gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
            gzclose($gz);
        } catch (\Throwable $e) {
            if (is_resource($gz)) {
                gzclose($gz);
            }
            if (file_exists($targetPath)) {
                @unlink($targetPath);
            }
            throw new \RuntimeException('Gagal membuat dump streaming basis data: '.$e->getMessage(), 0, $e);
        }
    }
}
