<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SecuritySweepService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menjalankan pembersihan higienitas keamanan otomatis (PRD 8.4, 10.3 NFR-A5, NFR-S6).
     *
     * @return array{deleted_tokens: int, deleted_temp_files: int, deleted_unverified_users: int}
     */
    public function sweep(bool $dryRun = false): array
    {
        // 1. Bersihkan token reset password yang kedaluwarsa (> 60 menit)
        $tokenCutoff = Carbon::now()->subMinutes(60);
        $expiredTokensQuery = DB::table('password_reset_tokens')->where('created_at', '<', $tokenCutoff);
        $expiredTokensCount = $expiredTokensQuery->count();
        if (! $dryRun && $expiredTokensCount > 0) {
            $expiredTokensQuery->delete();
        }

        // 2. Bersihkan file zip sementara di storage/app/temp yang berumur > 2 jam
        $tempDir = storage_path('app/temp');
        $deletedTempFiles = 0;
        if (File::isDirectory($tempDir)) {
            $files = File::files($tempDir);
            $twoHoursAgo = time() - 7200;
            foreach ($files as $file) {
                if ($file->getExtension() === 'zip' && $file->getMTime() < $twoHoursAgo) {
                    $deletedTempFiles++;
                    if (! $dryRun) {
                        @File::delete($file->getPathname());
                    }
                }
            }
        }

        // 3. Bersihkan calon pendaftar yang tidak memverifikasi email > 72 jam dan belum memiliki pengajuan pendaftaran
        $unverifiedCutoff = Carbon::now()->subHours(72);
        $unverifiedUsersQuery = User::query()
            ->where('role', RoleEnum::Peserta)
            ->whereNull('email_verified_at')
            ->where('created_at', '<', $unverifiedCutoff)
            ->whereDoesntHave('pendaftaran');

        $unverifiedCount = $unverifiedUsersQuery->count();
        if (! $dryRun && $unverifiedCount > 0) {
            $unverifiedUsersQuery->delete();
        }

        // 4. Catat ke audit trail jika bukan dry-run
        if (! $dryRun) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'system',
                idData: null,
                nilaiLama: null,
                nilaiBaru: [
                    'deleted_tokens' => $expiredTokensCount,
                    'deleted_temp_files' => $deletedTempFiles,
                    'deleted_unverified_users' => $unverifiedCount,
                ],
                detail: [
                    'command' => 'magkom:security-sweep',
                    'deskripsi' => 'Pembersihan higienitas keamanan sistem otomatis & pembersihan residu sementara (PRD 8.4, 10.3)',
                ]
            );
        }

        return [
            'deleted_tokens' => $expiredTokensCount,
            'deleted_temp_files' => $deletedTempFiles,
            'deleted_unverified_users' => $unverifiedCount,
        ];
    }

    /**
     * Dapatkan ringkasan statistik item kedaluwarsa yang siap dibersihkan.
     *
     * @return array{pending_tokens: int, pending_temp_files: int, pending_unverified_users: int, last_sweep_at: ?string}
     */
    public function getPendingStats(): array
    {
        $tokenCutoff = Carbon::now()->subMinutes(60);
        $pendingTokens = DB::table('password_reset_tokens')->where('created_at', '<', $tokenCutoff)->count();

        $tempDir = storage_path('app/temp');
        $pendingTempFiles = 0;
        if (File::isDirectory($tempDir)) {
            $twoHoursAgo = time() - 7200;
            foreach (File::files($tempDir) as $file) {
                if ($file->getExtension() === 'zip' && $file->getMTime() < $twoHoursAgo) {
                    $pendingTempFiles++;
                }
            }
        }

        $unverifiedCutoff = Carbon::now()->subHours(72);
        $pendingUnverified = User::query()
            ->where('role', RoleEnum::Peserta)
            ->whereNull('email_verified_at')
            ->where('created_at', '<', $unverifiedCutoff)
            ->whereDoesntHave('pendaftaran')
            ->count();

        $lastAudit = AuditLog::query()
            ->where('entitas', 'system')
            ->where('detail->command', 'magkom:security-sweep')
            ->latest('waktu')
            ->first();

        return [
            'pending_tokens' => $pendingTokens,
            'pending_temp_files' => $pendingTempFiles,
            'pending_unverified_users' => $pendingUnverified,
            'last_sweep_at' => $lastAudit?->waktu?->format('d M Y H:i'),
        ];
    }
}
