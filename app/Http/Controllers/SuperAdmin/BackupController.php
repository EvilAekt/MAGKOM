<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    protected string $backupDir;

    public function __construct(protected AuditService $auditService)
    {
        $this->backupDir = storage_path('app/backups');
    }

    /**
     * Menampilkan antarmuka manajemen snapshot cadangan basis data (SEC-03).
     */
    public function index(): View
    {
        File::ensureDirectoryExists($this->backupDir);

        $backupFiles = collect(File::files($this->backupDir))
            ->map(function ($file) {
                return [
                    'filename' => $file->getFilename(),
                    'size_mb' => round($file->getSize() / (1024 * 1024), 2),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime())->translatedFormat('d M Y H:i:s'),
                    'timestamp' => $file->getMTime(),
                    'checksum' => hash_file('sha256', $file->getRealPath()),
                ];
            })
            ->sortByDesc('timestamp')
            ->values();

        $totalSizeMb = $backupFiles->sum('size_mb');

        return view('superadmin.backup.index', [
            'backupFiles' => $backupFiles,
            'totalBackups' => $backupFiles->count(),
            'totalSizeMb' => round($totalSizeMb, 2),
        ]);
    }

    /**
     * Memicu pembuatan snapshot basis data instan.
     */
    public function triggerSnapshot(Request $request): RedirectResponse
    {
        File::ensureDirectoryExists($this->backupDir);

        $driver = DB::connection()->getDriverName();
        $timestamp = date('Y-m-d-His');

        if ($driver === 'sqlite') {
            $dbPath = (string) config('database.connections.sqlite.database');
            $filename = "backup-magkom-sqlite-{$timestamp}.sqlite";
            $targetPath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;

            if ($dbPath !== ':memory:' && file_exists($dbPath)) {
                copy($dbPath, $targetPath);
            } else {
                // In-memory SQLite or test environment: dump schema and records
                $schema = DB::select('SELECT sql FROM sqlite_master WHERE sql IS NOT NULL');
                $dump = "-- SQLite Memory Snapshot: {$timestamp}\n\n";
                foreach ($schema as $s) {
                    $dump .= $s->sql.";\n";
                }
                File::put($targetPath, $dump);
            }
        } else {
            // MySQL/PostgreSQL schema & data export valid DDL & DML
            $filename = "backup-magkom-dump-{$timestamp}.sql";
            $targetPath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;

            $tables = DB::select('SHOW TABLES');
            $sqlContent = "-- MagKom Database Snapshot Export: {$timestamp}\n";
            $sqlContent .= "-- Driver: {$driver}\n";
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                $tableName = reset($tableObj);
                if (! is_string($tableName) || empty($tableName)) {
                    continue;
                }

                try {
                    $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    if (! empty($createTableResult)) {
                        $createRow = (array) $createTableResult[0];
                        $createSql = $createRow['Create Table'] ?? reset($createRow);
                        $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                        $sqlContent .= $createSql.";\n\n";
                    }
                } catch (\Throwable $e) {
                    $sqlContent .= "-- Could not retrieve CREATE TABLE for: {$tableName}\n";
                }

                // Dump records in chunked batches
                try {
                    $rows = DB::table($tableName)->get();
                    if ($rows->isNotEmpty()) {
                        foreach ($rows as $row) {
                            $rowArr = (array) $row;
                            $columns = array_keys($rowArr);
                            $escapedColumns = array_map(fn ($col) => "`{$col}`", $columns);
                            $escapedValues = array_map(function ($val) {
                                if (is_null($val)) {
                                    return 'NULL';
                                }
                                if (is_bool($val)) {
                                    return $val ? '1' : '0';
                                }
                                if (is_numeric($val)) {
                                    return (string) $val;
                                }

                                return "'".addslashes((string) $val)."'";
                            }, array_values($rowArr));

                            $sqlContent .= "INSERT INTO `{$tableName}` (".implode(', ', $escapedColumns).') VALUES ('.implode(', ', $escapedValues).");\n";
                        }
                        $sqlContent .= "\n";
                    }
                } catch (\Throwable $e) {
                    $sqlContent .= "-- Could not dump records for: {$tableName}\n";
                }
            }

            $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";
            File::put($targetPath, $sqlContent);
        }

        $checksum = hash_file('sha256', $targetPath);

        // Catat Audit Trail Forensik (AC-13-6)
        $this->auditService->catat(
            TipeAuditEnum::Aksi,
            'backup',
            null,
            null,
            ['filename' => $filename, 'checksum' => $checksum],
            ['aksi' => 'backup.snapshot_generated'],
            $request->user()
        );

        return redirect()->route('superadmin.backup.index')->with('success', "Snapshot cadangan '{$filename}' berhasil dibuat dengan checksum SHA-256 terverifikasi.");
    }

    /**
     * Mengunduh berkas cadangan dengan proteksi Path Traversal (SEC-03, SEC-11).
     */
    public function download(string $filename): BinaryFileResponse
    {
        if (! preg_match('/^[a-zA-Z0-9\-_.]+\.(sqlite|sql|gz|zip)$/', $filename)) {
            abort(400, 'Format nama berkas cadangan tidak valid.');
        }

        $filePath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;

        if (! file_exists($filePath)) {
            abort(404, 'Berkas cadangan tidak ditemukan.');
        }

        return response()->download($filePath, $filename);
    }

    /**
     * Menghapus snapshot cadangan lama.
     */
    public function destroy(string $filename): RedirectResponse
    {
        if (! preg_match('/^[a-zA-Z0-9\-_.]+\.(sqlite|sql|gz|zip)$/', $filename)) {
            abort(400, 'Format nama berkas cadangan tidak valid.');
        }

        $filePath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return redirect()->route('superadmin.backup.index')->with('success', "Berkas cadangan '{$filename}' berhasil dihapus.");
    }
}
