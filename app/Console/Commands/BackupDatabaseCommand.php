<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupDatabaseService;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    /**
     * Nama dan signature perintah console.
     *
     * @var string
     */
    protected $signature = 'magkom:backup-database {--clean-old : Pangkas cadangan lama melebihi batas retensi}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Membuat snapshot cadangan basis data MAGKOM terkompresi Gzip dan verifikasi integritas checksum SHA-256';

    /**
     * Eksekusi perintah console.
     */
    public function handle(BackupDatabaseService $backupService): int
    {
        $this->info('====================================================');
        $this->info('  MAGKOM DATABASE BACKUP RUNNER (DISASTER RECOVERY) ');
        $this->info('====================================================');

        $start = microtime(true);

        try {
            $this->comment('Sedang memproses dump dan kompresi on-the-fly...');
            $result = $backupService->createSnapshot(null);

            $duration = round(microtime(true) - $start, 2);

            $this->info('✓ Snapshot cadangan basis data berhasil dibuat!');
            $this->table(
                ['Parameter', 'Nilai'],
                [
                    ['Nama Berkas', $result['filename']],
                    ['Driver Basis Data', strtoupper($result['driver'])],
                    ['Ukuran Berkas', $result['size_mb'].' MB'],
                    ['Integritas SHA-256', $result['checksum']],
                    ['Durasi Eksekusi', "{$duration} detik"],
                    ['Lokasi Penyimpanan', $result['path']],
                ]
            );

            if ($this->option('clean-old')) {
                $pruned = $backupService->pruneOldBackups();
                if ($pruned > 0) {
                    $this->comment("✓ Retensi FIFO: {$pruned} berkas cadangan lama berhasil dipangkas.");
                }
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Gagal membuat cadangan basis data: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
