<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class HealthMonitorController extends Controller
{
    /**
     * Menampilkan dashboard telemetri kesehatan infrastruktur MagKom.
     */
    public function index(Request $request): View
    {
        // 1. Telemetri Basis Data
        $dbStatus = 'OK';
        $dbLatency = 0.0;
        $dbDriver = 'unknown';
        $dbName = '';
        $totalTables = 0;

        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $dbLatency = round((microtime(true) - $start) * 1000, 2);
            $dbDriver = DB::connection()->getDriverName();
            $dbName = (string) DB::connection()->getDatabaseName();

            if ($dbDriver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $totalTables = count($tables);
            } else {
                $tables = DB::select('SHOW TABLES');
                $totalTables = count($tables);
            }
        } catch (\Throwable $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }

        // 2. Telemetri Storage Berkas & Disk
        $storagePath = storage_path('app');
        $isWritable = is_writable($storagePath);
        $diskFree = @disk_free_space($storagePath) ?: 0;
        $diskTotal = @disk_total_space($storagePath) ?: 0;
        $diskUsed = max(0, $diskTotal - $diskFree);
        $diskUsedPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $totalDokumenCount = Dokumen::count();
        $totalDokumenBytes = (int) Dokumen::sum('ukuran');

        // 3. Runtime & Framework Telemetri
        $runtimeInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => (bool) config('app.debug'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'server_os' => PHP_OS,
        ];

        // 4. Keamanan & Job Health
        $totalAudit = AuditLog::count();
        $failedJobsCount = 0;
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();
        } catch (\Throwable $e) {
            $failedJobsCount = 0;
        }

        // 5. Cadangan Terakhir
        $backupDir = storage_path('app/backups');
        $lastBackupTime = null;
        if (File::isDirectory($backupDir)) {
            $files = File::files($backupDir);
            if (! empty($files)) {
                usort($files, fn ($a, $b) => $b->getMTime() <=> $a->getMTime());
                $lastBackupTime = date('d M Y H:i', $files[0]->getMTime());
            }
        }

        // 6. Telemetri Higienitas Keamanan (Security Sweep)
        $sweepService = app(\App\Services\SecuritySweepService::class);
        $sweeperStats = $sweepService->getPendingStats();

        return view('superadmin.health.index', [
            'dbStatus' => $dbStatus,
            'dbLatency' => $dbLatency,
            'dbDriver' => $dbDriver,
            'dbName' => basename($dbName),
            'totalTables' => $totalTables,
            'isWritable' => $isWritable,
            'diskFreeGb' => round($diskFree / (1024 * 1024 * 1024), 2),
            'diskTotalGb' => round($diskTotal / (1024 * 1024 * 1024), 2),
            'diskUsedPercent' => $diskUsedPercent,
            'totalDokumenCount' => $totalDokumenCount,
            'totalDokumenMb' => round($totalDokumenBytes / (1024 * 1024), 2),
            'runtimeInfo' => $runtimeInfo,
            'totalAudit' => $totalAudit,
            'failedJobsCount' => $failedJobsCount,
            'lastBackupTime' => $lastBackupTime,
            'sweeperStats' => $sweeperStats,
        ]);
    }

    /**
     * Memicu eksekusi Security Sweep secara on-demand dari panel telemetri.
     */
    public function runSecuritySweep(Request $request, \App\Services\SecuritySweepService $sweepService): \Illuminate\Http\RedirectResponse
    {
        $results = $sweepService->sweep(dryRun: false);

        return back()->with(
            'status_keberhasilan',
            "Pembersihan keamanan berhasil: {$results['deleted_tokens']} token kedaluwarsa dibersihkan, {$results['deleted_temp_files']} berkas arsip sementara dihapus, dan {$results['deleted_unverified_users']} pendaftar unverified (>72 jam) dibersihkan."
        );
    }
}
