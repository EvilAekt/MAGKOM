<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\BackupDatabaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupEncryptionAndRestoreTest extends TestCase
{
    use RefreshDatabase;

    protected BackupDatabaseService $backupService;

    protected string $testKey = 'base64:TestSecretEncryptionKeyForBackup12345=';

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupService = app(BackupDatabaseService::class);
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => $this->testKey]);
    }

    public function test_create_snapshot_produces_encrypted_archive_with_magic_header_and_hmac(): void
    {
        $result = $this->backupService->createSnapshot(encryptionKey: $this->testKey);

        $this->assertTrue($result['is_encrypted']);
        $this->assertStringEndsWith('.enc', $result['filename']);
        $this->assertFileExists($result['path']);

        $rawContent = (string) file_get_contents($result['path']);
        $this->assertStringStartsWith(BackupDatabaseService::ENC_MAGIC_HEADER, $rawContent);

        // Bersihkan berkas setelah pengujian
        @unlink($result['path']);
    }

    public function test_decrypt_with_correct_key_succeeds_and_wrong_key_fails_hmac(): void
    {
        $tempSource = storage_path('app/backups/test-source.txt');
        $tempEnc = storage_path('app/backups/test-source.txt.enc');
        $tempDec = storage_path('app/backups/test-source.dec.txt');

        File::put($tempSource, 'Data Sensitif Peserta Magang Kominfo 2026');

        // Enkripsi
        $this->backupService->encryptFile($tempSource, $tempEnc, $this->testKey);
        $this->assertFileExists($tempEnc);

        // Dekripsi dengan kunci yang benar
        $this->backupService->decryptFile($tempEnc, $tempDec, $this->testKey);
        $this->assertEquals('Data Sensitif Peserta Magang Kominfo 2026', file_get_contents($tempDec));

        // Dekripsi dengan kunci salah harus memicu exception integritas HMAC
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Verifikasi integritas HMAC gagal');
        $this->backupService->decryptFile($tempEnc, storage_path('app/backups/fail.txt'), 'wrong-key-1234567890123456');

        @unlink($tempSource);
        @unlink($tempEnc);
        @unlink($tempDec);
    }

    public function test_files_snapshot_and_restore_workflow(): void
    {
        $testPrivateDir = storage_path('app/private/test-sub');
        File::ensureDirectoryExists($testPrivateDir);
        File::put($testPrivateDir.'/cv-peserta.pdf', '%PDF-1.4 Fake PDF Content');

        // Buat backup files
        $result = $this->backupService->createFilesSnapshot(encryptionKey: $this->testKey);
        $this->assertTrue($result['is_encrypted']);
        $this->assertFileExists($result['path']);

        // Hapus file asli untuk menguji pemulihan
        File::deleteDirectory(storage_path('app/private/test-sub'));

        // Jalankan restore files
        $restoreTarget = storage_path('app/private');
        $restoreResult = $this->backupService->restoreFiles($result['filename'], $restoreTarget, $this->testKey);

        $this->assertTrue($restoreResult['success']);
        $this->assertFileExists($testPrivateDir.'/cv-peserta.pdf');

        // Bersihkan
        File::deleteDirectory($testPrivateDir);
        @unlink($result['path']);
    }

    public function test_path_traversal_in_backup_operations_is_blocked(): void
    {
        $maliciousFilename = '../../../../Windows/System32/drivers/etc/hosts';
        $path = $this->backupService->findBackupPath($maliciousFilename);

        $this->assertNull($path);
    }
}
