<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupDatabaseService;
use Illuminate\Console\Command;

class RestoreDatabaseCommand extends Command
{
    /**
     * Nama dan signature perintah console (BACKUP-002).
     *
     * @var string
     */
    protected $signature = 'magkom:restore-database 
                            {backup_file : Nama berkas cadangan di storage/app/backups}
                            {--connection=mysql : Koneksi basis data target}
                            {--key= : Kunci dekripsi opsional jika berbeda dari BACKUP_ENCRYPTION_KEY}
                            {--force : Paksa eksekusi tanpa konfirmasi interaktif di lingkungan production}';

    /**
     * Alias perintah konsol standar.
     *
     * @var array<int, string>
     */
    protected $aliases = ['backup:restore'];

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Memulihkan basis data dari arsip cadangan MAGKOM (mendukung arsip terenkripsi AES-256 .enc dan .sql.gz)';

    /**
     * Eksekusi perintah console.
     */
    public function handle(BackupDatabaseService $backupService): int
    {
        $backupFile = (string) $this->argument('backup_file');
        $connection = (string) $this->option('connection');
        $key = $this->option('key') ? (string) $this->option('key') : null;

        $this->info('========================================================');
        $this->info('  MAGKOM DISASTER RECOVERY: DATABASE RESTORE DRILL      ');
        $this->info('========================================================');

        // Guardrail Keamanan Lingkungan Produksi (Rule 3.3)
        if (app()->isProduction()) {
            $this->warn('PERINGATAN: Perintah restore dijalankan di lingkungan PRODUCTION!');
            $this->warn("Tindakan ini akan menimpa seluruh tabel pada koneksi: '{$connection}'.");

            if (! $this->option('force')) {
                if (! $this->confirm('Apakah Anda benar-benar yakin ingin melanjutkan pemulihan basis data?', false)) {
                    $this->error('Operasi pemulihan dibatalkan oleh pengguna.');

                    return Command::FAILURE;
                }
            }
        }

        $start = microtime(true);

        try {
            $this->comment("Sedang memproses pemulihan dari berkas '{$backupFile}' ke koneksi '{$connection}'...");
            $result = $backupService->restoreDatabase(
                filename: $backupFile,
                targetConnection: $connection,
                encryptionKey: $key
            );

            $duration = round(microtime(true) - $start, 2);

            $this->info('✓ Basis data berhasil dipulihkan secara utuh!');
            $this->table(
                ['Parameter', 'Nilai'],
                [
                    ['Berkas Cadangan', $result['filename']],
                    ['Koneksi Basis Data Target', $result['target_connection']],
                    ['Jumlah Tabel Dipulihkan', (string) $result['tables_count']],
                    ['Durasi Pemulihan', "{$duration} detik"],
                ]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Gagal memulihkan basis data: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
