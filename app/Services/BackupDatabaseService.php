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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupDatabaseService
{
    /**
     * Header pengenal berkas terenkripsi resmi MAGKOM (BACKUP-001).
     */
    public const ENC_MAGIC_HEADER = 'MAGKOM_ENC_v1';

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
                return (bool) preg_match('/^backup-magkom-[a-zA-Z0-9_\-]+\.(sql\.gz|sql|sqlite|zip)(\.enc)?$/', $file->getFilename());
            })
            ->map(function ($file) {
                $filename = $file->getFilename();
                $isEnc = str_ends_with($filename, '.enc');
                $isGz = str_contains($filename, '.sql.gz');
                $isSqlite = str_contains($filename, '.sqlite');
                $isZip = str_contains($filename, '.zip');

                $format = $isZip ? 'Archive Files (ZIP)' : ($isGz ? 'Gzip SQL' : ($isSqlite ? 'SQLite' : 'Raw SQL'));
                if ($isEnc) {
                    $format .= ' (AES-256 Encrypted)';
                }

                return [
                    'filename' => $filename,
                    'size_mb' => round($file->getSize() / (1024 * 1024), 2),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime())->translatedFormat('d M Y H:i:s'),
                    'timestamp' => $file->getMTime(),
                    'checksum' => hash_file('sha256', $file->getRealPath()),
                    'is_compressed' => $isGz || $isZip,
                    'is_encrypted' => $isEnc,
                    'format' => $format,
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
    /**
     * Memicu pembuatan snapshot basis data instan (mendukung enkripsi AES-256-CBC).
     *
     * @return array{
     *     filename: string,
     *     size_mb: float,
     *     checksum: string,
     *     driver: string,
     *     path: string,
     *     is_encrypted: bool
     * }
     *
     * @throws \RuntimeException
     */
    public function createSnapshot(?User $triggeredBy = null, ?string $encryptionKey = null): array
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

        // 2. Enkripsi Berkas Arsip (BACKUP-001: AES-256-CBC with HMAC-SHA256)
        $encryptEnabled = (bool) config('backup.encryption_enabled', true);
        $key = $encryptionKey ?? (string) config('backup.encryption_key', config('app.key'));
        $isEncrypted = false;

        if ($encryptEnabled && ! empty($key)) {
            $encFilename = $filename.'.enc';
            $encTargetPath = $this->backupDir.DIRECTORY_SEPARATOR.$encFilename;
            $this->encryptFile($targetPath, $encTargetPath, $key);
            @unlink($targetPath);
            $targetPath = $encTargetPath;
            $filename = $encFilename;
            $isEncrypted = true;
        }

        $sizeMb = round(filesize($targetPath) / (1024 * 1024), 2);
        $checksum = hash_file('sha256', $targetPath);

        // 3. Salin ke Offsite Object Storage / Remote Disk jika dikonfigurasi
        $offsiteDisk = config('backup.offsite_disk');
        if (! empty($offsiteDisk)) {
            try {
                Storage::disk($offsiteDisk)->putFileAs('backups', new \Illuminate\Http\File($targetPath), $filename);
            } catch (\Throwable $e) {
                Log::warning("Gagal menyalin backup ke offsite disk [{$offsiteDisk}]: ".$e->getMessage());
            }
        }

        // 4. Pangkas cadangan lama melebihi kuota snapshot (FIFO)
        $this->pruneOldBackups(self::MAX_BACKUPS_RETAINED);

        // 5. Catat jejak audit trail forensik (AC-13-6)
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
                'is_encrypted' => $isEncrypted,
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
            'is_encrypted' => $isEncrypted,
        ];
    }

    /**
     * Membuat snapshot cadangan berkas privat pengguna (dokumen peserta).
     *
     * @return array{
     *     filename: string,
     *     size_mb: float,
     *     checksum: string,
     *     driver: string,
     *     path: string,
     *     is_encrypted: bool
     * }
     */
    public function createFilesSnapshot(?User $triggeredBy = null, ?string $encryptionKey = null): array
    {
        File::ensureDirectoryExists($this->backupDir);
        $privateDir = (string) config('backup.private_files_dir', storage_path('app/private'));
        $timestamp = date('Y-m-d-His');
        $rawFilename = "backup-magkom-files-{$timestamp}.zip";
        $rawTargetPath = $this->backupDir.DIRECTORY_SEPARATOR.$rawFilename;

        $zip = new ZipArchive;
        if ($zip->open($rawTargetPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat arsip berkas zip.');
        }

        if (File::exists($privateDir)) {
            $files = File::allFiles($privateDir);
            foreach ($files as $file) {
                $relative = substr($file->getRealPath(), strlen($privateDir) + 1);
                $zip->addFile($file->getRealPath(), $relative);
            }
        }
        $zip->close();

        $targetPath = $rawTargetPath;
        $filename = $rawFilename;
        $encryptEnabled = (bool) config('backup.encryption_enabled', true);
        $key = $encryptionKey ?? (string) config('backup.encryption_key', config('app.key'));
        $isEncrypted = false;

        if ($encryptEnabled && ! empty($key)) {
            $encFilename = $filename.'.enc';
            $encTargetPath = $this->backupDir.DIRECTORY_SEPARATOR.$encFilename;
            $this->encryptFile($targetPath, $encTargetPath, $key);
            @unlink($targetPath);
            $targetPath = $encTargetPath;
            $filename = $encFilename;
            $isEncrypted = true;
        }

        $sizeMb = round(filesize($targetPath) / (1024 * 1024), 2);
        $checksum = hash_file('sha256', $targetPath);

        $offsiteDisk = config('backup.offsite_disk');
        if (! empty($offsiteDisk)) {
            try {
                Storage::disk($offsiteDisk)->putFileAs('backups', new \Illuminate\Http\File($targetPath), $filename);
            } catch (\Throwable $e) {
                Log::warning("Gagal menyalin berkas arsip ke offsite disk [{$offsiteDisk}]: ".$e->getMessage());
            }
        }

        $this->pruneOldBackups(self::MAX_BACKUPS_RETAINED);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'backup',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'backup.files_snapshot_generated',
                'filename' => $filename,
                'size_mb' => $sizeMb,
                'checksum' => $checksum,
                'is_encrypted' => $isEncrypted,
                'type' => 'files',
            ],
            user: $triggeredBy,
            channel: 'backup-files',
            severity: 'info'
        );

        return [
            'filename' => $filename,
            'size_mb' => $sizeMb,
            'checksum' => $checksum,
            'driver' => 'zip',
            'path' => $targetPath,
            'is_encrypted' => $isEncrypted,
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
        if (! preg_match('/^backup-magkom-[a-zA-Z0-9_\-]+\.(sql\.gz|sql|sqlite|zip)(\.enc)?$/', $filename)) {
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
     * Mengenkripsi berkas dengan AES-256-CBC dan HMAC-SHA256 Encrypt-then-MAC (BACKUP-001).
     */
    public function encryptFile(string $sourcePath, string $targetPath, string $key): void
    {
        if (! file_exists($sourcePath)) {
            throw new \InvalidArgumentException("Berkas sumber tidak ditemukan: {$sourcePath}");
        }

        $iv = random_bytes(16);
        $salt = random_bytes(16);
        $derivedKey = hash_pbkdf2('sha256', $key, $salt, 10000, 32, true);

        $plaintext = (string) file_get_contents($sourcePath);
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $derivedKey, OPENSSL_RAW_DATA, $iv);

        if ($ciphertext === false) {
            throw new \RuntimeException('Gagal mengenkripsi berkas cadangan dengan OpenSSL.');
        }

        $hmac = hash_hmac('sha256', $ciphertext, $derivedKey, true);

        $handle = fopen($targetPath, 'wb');
        if (! $handle) {
            throw new \RuntimeException("Gagal membuka target berkas terenkripsi: {$targetPath}");
        }

        // Header paket: Magic (13 B) + Salt (16 B) + IV (16 B) + HMAC (32 B) + Ciphertext
        fwrite($handle, self::ENC_MAGIC_HEADER);
        fwrite($handle, $salt);
        fwrite($handle, $iv);
        fwrite($handle, $hmac);
        fwrite($handle, $ciphertext);
        fclose($handle);
    }

    /**
     * Mendekripsi berkas terenkripsi dengan verifikasi integritas HMAC-SHA256 (BACKUP-001/BACKUP-002).
     */
    public function decryptFile(string $sourcePath, string $targetPath, string $key): void
    {
        if (! file_exists($sourcePath)) {
            throw new \InvalidArgumentException("Berkas terenkripsi tidak ditemukan: {$sourcePath}");
        }

        $content = (string) file_get_contents($sourcePath);
        $magicLen = strlen(self::ENC_MAGIC_HEADER);

        if (strlen($content) < ($magicLen + 64)) {
            throw new \RuntimeException('Format berkas terenkripsi tidak valid atau rusak (ukuran terlalu kecil).');
        }

        $magic = substr($content, 0, $magicLen);
        if ($magic !== self::ENC_MAGIC_HEADER) {
            throw new \RuntimeException('Header arsip tidak cocok dengan format enkripsi resmi MAGKOM.');
        }

        $salt = substr($content, $magicLen, 16);
        $iv = substr($content, $magicLen + 16, 16);
        $hmac = substr($content, $magicLen + 32, 32);
        $ciphertext = substr($content, $magicLen + 64);

        $derivedKey = hash_pbkdf2('sha256', $key, $salt, 10000, 32, true);
        $calculatedHmac = hash_hmac('sha256', $ciphertext, $derivedKey, true);

        if (! hash_equals($hmac, $calculatedHmac)) {
            throw new \RuntimeException('Verifikasi integritas HMAC gagal: Kunci enkripsi salah atau berkas telah dimanipulasi.');
        }

        $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $derivedKey, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            throw new \RuntimeException('Gagal mendekripsi ciphertext cadangan.');
        }

        $written = file_put_contents($targetPath, $decrypted);
        if ($written === false) {
            throw new \RuntimeException("Gagal menulis hasil dekripsi ke target: {$targetPath}");
        }
    }

    /**
     * Memulihkan basis data dari arsip cadangan (BACKUP-002).
     *
     * @return array{
     *     success: bool,
     *     filename: string,
     *     target_connection: string,
     *     tables_count: int,
     *     tables: array<string>
     * }
     */
    public function restoreDatabase(string $filename, string $targetConnection = 'mysql', ?string $encryptionKey = null, ?User $triggeredBy = null): array
    {
        $realPath = $this->findBackupPath($filename);
        if (! $realPath) {
            throw new \InvalidArgumentException("Berkas cadangan '{$filename}' tidak ditemukan atau path traversal terdeteksi.");
        }

        $key = $encryptionKey ?? (string) config('backup.encryption_key', config('app.key'));
        $tempPath = null;
        $workPath = $realPath;

        try {
            // 1. Jika berkas terenkripsi (.enc), dekripsi ke lokasi temporer terisolasi
            if (str_ends_with($filename, '.enc')) {
                $tempPath = $this->backupDir.DIRECTORY_SEPARATOR.'temp-restore-'.uniqid().'.sql.gz';
                $this->decryptFile($realPath, $tempPath, $key);
                $workPath = $tempPath;
            }

            // 2. Ekstrak isi SQL dari arsip Gzip atau plaintext
            $sqlContent = '';
            if (str_contains($workPath, '.gz') || str_ends_with($workPath, '.gz')) {
                $gz = @gzopen($workPath, 'rb');
                if (! $gz) {
                    throw new \RuntimeException('Gagal membuka arsip gzip cadangan.');
                }
                while (! gzeof($gz)) {
                    $chunk = gzread($gz, 65536);
                    if ($chunk !== false) {
                        $sqlContent .= $chunk;
                    }
                }
                gzclose($gz);
            } else {
                $sqlContent = (string) file_get_contents($workPath);
            }

            if (empty(trim($sqlContent))) {
                throw new \RuntimeException('Berkas dump SQL cadangan kosong atau tidak valid.');
            }

            // 3. Jalankan pemulihan pada koneksi target via PDO
            $connection = DB::connection($targetConnection);
            $pdo = $connection->getPdo();

            $driver = $connection->getDriverName();
            if ($driver === 'mysql') {
                $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
            }

            // Jalankan seluruh batch script SQL
            $pdo->exec($sqlContent);

            if ($driver === 'mysql') {
                $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
            }

            $tables = Schema::connection($targetConnection)->getTableListing();

            // 4. Catat jejak audit trail pemulihan bencana (DR)
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'backup',
                idData: null,
                nilaiLama: null,
                nilaiBaru: null,
                detail: [
                    'aksi' => 'backup.database_restored',
                    'filename' => $filename,
                    'target_connection' => $targetConnection,
                    'tables_count' => count($tables),
                ],
                user: $triggeredBy,
                channel: 'backup-restore',
                severity: 'warning'
            );

            return [
                'success' => true,
                'filename' => $filename,
                'target_connection' => $targetConnection,
                'tables_count' => count($tables),
                'tables' => $tables,
            ];
        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Memulihkan arsip berkas fisik pengguna (BACKUP-002).
     *
     * @return array{
     *     success: bool,
     *     filename: string,
     *     target_dir: string,
     *     files_count: int
     * }
     */
    public function restoreFiles(string $filename, ?string $targetDir = null, ?string $encryptionKey = null, ?User $triggeredBy = null): array
    {
        $realPath = $this->findBackupPath($filename);
        if (! $realPath) {
            throw new \InvalidArgumentException("Berkas cadangan '{$filename}' tidak ditemukan atau path tidak valid.");
        }

        $key = $encryptionKey ?? (string) config('backup.encryption_key', config('app.key'));
        $targetDirectory = $targetDir ?? (string) config('backup.private_files_dir', storage_path('app/private'));
        File::ensureDirectoryExists($targetDirectory);

        $tempPath = null;
        $workPath = $realPath;

        try {
            if (str_ends_with($filename, '.enc')) {
                $tempPath = $this->backupDir.DIRECTORY_SEPARATOR.'temp-restore-'.uniqid().'.zip';
                $this->decryptFile($realPath, $tempPath, $key);
                $workPath = $tempPath;
            }

            $zip = new ZipArchive;
            if ($zip->open($workPath) !== true) {
                throw new \RuntimeException('Gagal membuka arsip berkas zip.');
            }

            $extractedFilesCount = $zip->numFiles;
            $zip->extractTo($targetDirectory);
            $zip->close();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'backup',
                idData: null,
                nilaiLama: null,
                nilaiBaru: null,
                detail: [
                    'aksi' => 'backup.files_restored',
                    'filename' => $filename,
                    'target_dir' => $targetDirectory,
                    'files_count' => $extractedFilesCount,
                ],
                user: $triggeredBy,
                channel: 'backup-restore',
                severity: 'warning'
            );

            return [
                'success' => true,
                'filename' => $filename,
                'target_dir' => $targetDirectory,
                'files_count' => $extractedFilesCount,
            ];
        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
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
     * Mencari path binary mysqldump pada sistem host (kompatibel Linux & Windows / BACKUP-001).
     */
    protected function resolveMysqldumpBinary(): ?string
    {
        // 1. Prioritas utama: konfigurasi eksplisit environment atau file konfigurasi
        $customPath = (string) config('database.connections.mysql.dump.dump_binary_path', env('MYSQLDUMP_PATH', ''));
        if (! empty($customPath) && file_exists($customPath)) {
            return $customPath;
        }

        // 2. Cari di PATH sistem host via perintah bawaan sistem operasi (which / where)
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

        // 3. Jalur instalasi standar server Linux / UNIX
        $linuxCandidates = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
        ];

        foreach ($linuxCandidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        // 4. Jalur pengembangan lokal Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $windowsCandidates = [
                'F:\\XAMPEEK\\mysql\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            ];

            foreach ($windowsCandidates as $candidate) {
                if (file_exists($candidate)) {
                    return $candidate;
                }
            }
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
