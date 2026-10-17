<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SecuritySweepService;
use Illuminate\Console\Command;

class SecuritySweeperCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'magkom:security-sweep {--dry-run : Simulasi pembersihan tanpa menghapus entri}';

    /**
     * The console command description.
     */
    protected $description = 'Membersihkan token kedaluwarsa, berkas arsip zip sementara, dan pendaftar tidak terverifikasi (PRD Bab 8.4, 10.3 NFR-A5)';

    /**
     * Execute the console command.
     */
    public function handle(SecuritySweepService $sweepService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Memulai pembersihan keamanan sistem (Security Sweep)...' . ($dryRun ? ' [DRY RUN]' : ''));

        $results = $sweepService->sweep($dryRun);

        $this->table(
            ['Komponen Yang Diperiksa', 'Jumlah Dibersihkan'],
            [
                ['Token Reset Kata Sandi Kedaluwarsa (> 60m)', $results['deleted_tokens']],
                ['Berkas Arsip ZIP Sementara (> 2 jam)', $results['deleted_temp_files']],
                ['Pendaftar Belum Verifikasi (> 72 Jam Tanpa Pengajuan)', $results['deleted_unverified_users']],
            ]
        );

        $this->info('Security Sweeper berhasil diselesaikan dengan aman.');

        return Command::SUCCESS;
    }
}
