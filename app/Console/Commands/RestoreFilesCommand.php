<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupDatabaseService;
use Illuminate\Console\Command;

class RestoreFilesCommand extends Command
{
    /**
     * Nama dan signature perintah console (BACKUP-002).
     *
     * @var string
     */
    protected $signature = 'magkom:restore-files 
                            {backup_file : Nama berkas cadangan zip di storage/app/backups}
                            {--target-dir= : Direktori target ekstraksi (default: storage/app/private)}
                            {--key= : Kunci dekripsi opsional jika terenkripsi}
                            {--force : Paksa ekstraksi tanpa konfirmasi}';

    /**
     * Alias perintah konsol standar.
     *
     * @var array<int, string>
     */
    protected $aliases = ['backup:restore-files'];

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Memulihkan arsip berkas privat pengguna dari cadangan (mendukung arsip zip dan terenkripsi .enc)';

    /**
     * Eksekusi perintah console.
     */
    public function handle(BackupDatabaseService $backupService): int
    {
        $backupFile = (string) $this->argument('backup_file');
        $targetDir = $this->option('target-dir') ? (string) $this->option('target-dir') : null;
        $key = $this->option('key') ? (string) $this->option('key') : null;

        $this->info('========================================================');
        $this->info('  MAGKOM DISASTER RECOVERY: FILES RESTORE DRILL         ');
        $this->info('========================================================');

        $start = microtime(true);

        try {
            $this->comment("Sedang memulihkan berkas dari '{$backupFile}'...");
            $result = $backupService->restoreFiles(
                filename: $backupFile,
                targetDir: $targetDir,
                encryptionKey: $key
            );

            $duration = round(microtime(true) - $start, 2);

            $this->info('✓ Berkas privat berhasil dipulihkan!');
            $this->table(
                ['Parameter', 'Nilai'],
                [
                    ['Berkas Cadangan', $result['filename']],
                    ['Direktori Tujuan', $result['target_dir']],
                    ['Jumlah Berkas Dipulihkan', (string) $result['files_count']],
                    ['Durasi Pemulihan', "{$duration} detik"],
                ]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Gagal memulihkan berkas: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
