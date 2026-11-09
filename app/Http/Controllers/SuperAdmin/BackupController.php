<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\BackupDatabaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupDatabaseService $backupService,
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan antarmuka manajemen snapshot cadangan basis data (SEC-03).
     */
    public function index(Request $request): View
    {
        $page = $request->has('page') ? (int) $request->input('page') : 1;
        $backupFiles = $this->backupService->getPaginatedBackups(perPage: 10, page: $page);
        $diskTelemetry = $this->backupService->getDiskTelemetry();

        return view('superadmin.backup.index', [
            'backupFiles' => $backupFiles,
            'totalBackups' => $diskTelemetry['total_backups'],
            'totalSizeMb' => $diskTelemetry['total_size_mb'],
            'diskTelemetry' => $diskTelemetry,
        ]);
    }

    /**
     * Memicu pembuatan snapshot basis data instan dengan rate-limiting & disk guard.
     */
    public function triggerSnapshot(Request $request): RedirectResponse
    {
        $userId = $request->user()?->id ?? 'superadmin';
        $lockKey = "backup_trigger_lock_{$userId}";
        $lock = Cache::lock($lockKey, 60);

        if (! $lock->get()) {
            return redirect()->route('superadmin.backup.index')
                ->with('error', 'Harap tunggu 1 menit sebelum memicu snapshot baru untuk mencegah beban IOPS basis data berlebih.');
        }

        try {
            $result = $this->backupService->createSnapshot($request->user());

            return redirect()->route('superadmin.backup.index')
                ->with('success', "Snapshot cadangan '{$result['filename']}' ({$result['size_mb']} MB) berhasil dibuat dengan checksum SHA-256 terverifikasi.");
        } catch (\Throwable $e) {
            return redirect()->route('superadmin.backup.index')
                ->with('error', 'Gagal membuat snapshot cadangan: '.$e->getMessage());
        }
    }

    /**
     * Mengunduh berkas cadangan dengan proteksi Path Traversal & Audit Forensik (SEC-03, SEC-11).
     */
    public function download(string $filename, Request $request): BinaryFileResponse
    {
        $realPath = $this->backupService->findBackupPath($filename);

        if (! $realPath) {
            abort(400, 'Format nama berkas cadangan tidak valid atau berkas tidak ditemukan.');
        }

        // Catat Audit Trail Akses Data Forensik (AC-13-6)
        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'backup',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'backup.downloaded',
                'filename' => $filename,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            user: $request->user(),
            channel: 'backup-database',
            severity: 'info'
        );

        return response()->download($realPath, $filename, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Menghapus snapshot cadangan secara aman.
     */
    public function destroy(string $filename, Request $request): RedirectResponse
    {
        $deleted = $this->backupService->deleteBackup($filename, $request->user());

        if (! $deleted) {
            return redirect()->route('superadmin.backup.index')
                ->with('error', 'Gagal menghapus berkas cadangan. Pastikan berkas valid dan terdaftar.');
        }

        return redirect()->route('superadmin.backup.index')
            ->with('success', "Berkas cadangan '{$filename}' berhasil dihapus.");
    }
}
