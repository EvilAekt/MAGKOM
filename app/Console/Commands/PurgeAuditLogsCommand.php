<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Services\AuditService;
use App\Support\Pengaturan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeAuditLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'magkom:audit-purge {--days= : Jumlah hari retensi log akses data (default dari pengaturan)}';

    /**
     * The console command description.
     */
    protected $description = 'Memangkas log akses data audit trail yang melampaui masa retensi (PRD Bab 10.3 NFR-A5 & TDD Bab 9)';

    /**
     * Execute the console command.
     */
    public function handle(AuditService $auditService): int
    {
        $daysOption = $this->option('days');
        $retensiHari = $daysOption !== null ? (int) $daysOption : (int) Pengaturan::get('retensi_audit_akses_data_hari', '365');

        if ($retensiHari < 30) {
            $this->warn('Batas retensi minimal adalah 30 hari. Menggunakan batas aman 30 hari.');
            $retensiHari = 30;
        }

        $cutoff = Carbon::now()->subDays($retensiHari);

        $this->info("Memulai pembersihan log akses data sebelum {$cutoff->toDateTimeString()} (retensi: {$retensiHari} hari)...");

        // Sesuai NFR-A5: Hanya tipe 'akses_data' yang dipangkas, tipe auth, aksi, dan service tetap dipertahankan
        $deletedCount = AuditLog::where('tipe', TipeAuditEnum::AksesData)
            ->where('waktu', '<', $cutoff)
            ->delete();

        $this->info("Berhasil memangkas {$deletedCount} entri log akses data.");

        // Catat mutasi pembersihan sistem ini ke tabel audit_log
        $auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'audit_log',
            idData: null,
            nilaiLama: null,
            nilaiBaru: [
                'baris_dipangkas' => $deletedCount,
                'retensi_hari' => $retensiHari,
                'cutoff' => $cutoff->toIso8601String(),
            ],
            detail: [
                'command' => 'magkom:audit-purge',
                'deskripsi' => 'Pembersihan otomatis berkala log akses data (NFR-A5)',
            ]
        );

        return self::SUCCESS;
    }
}
