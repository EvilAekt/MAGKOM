<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\BackupDatabaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StreamingBackupAndKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected BackupDatabaseService $backupService;

    protected string $primaryKey = 'base64:PrimaryDedicatedBackupSecretKey12345=';

    protected string $rotatedKey = 'base64:RotatedNextYearBackupSecretKey99999=';

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupService = app(BackupDatabaseService::class);
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => $this->primaryKey]);
        config(['backup.fallback_keys' => []]);
    }

    /**
     * Uji 1: Enkripsi streaming chunked menghasilkan format MAGKOM_ENC_v2 dengan Key ID tersemat.
     */
    public function test_streaming_chunked_encryption_produces_v2_header_with_key_id(): void
    {
        $tempSource = storage_path('app/backups/stream-test.sql');
        $tempEnc = storage_path('app/backups/stream-test.sql.enc');
        $tempDec = storage_path('app/backups/stream-test-dec.sql');

        // Buat file multi-chunk (> 150 KB untuk menguji minimal 3 chunk 64KB)
        $chunkData = str_repeat("INSERT INTO telemetry VALUES ('metric_data_chunk_sample');\n", 4000);
        File::put($tempSource, $chunkData);

        $initialMemory = memory_get_usage();

        // Enkripsi streaming
        $this->backupService->encryptFile($tempSource, $tempEnc, $this->primaryKey, 'key-2026-v2');

        $peakMemory = memory_get_peak_usage() - $initialMemory;
        // Peak memory usage harus bounded (< 32 MB)
        $this->assertLessThan(32 * 1024 * 1024, $peakMemory);

        $rawEnc = (string) file_get_contents($tempEnc);
        $this->assertStringStartsWith(BackupDatabaseService::ENC_MAGIC_HEADER_V2, $rawEnc);

        // Dekripsi streaming
        $this->backupService->decryptFile($tempEnc, $tempDec, $this->primaryKey);
        $this->assertFileExists($tempDec);
        $this->assertEquals($chunkData, file_get_contents($tempDec));

        @unlink($tempSource);
        @unlink($tempEnc);
        @unlink($tempDec);
    }

    /**
     * Uji 2: Kompatibilitas ke belakang format warisan MAGKOM_ENC_v1 tetap didekripsi dengan sukses.
     */
    public function test_legacy_v1_backup_can_still_be_decrypted(): void
    {
        $tempV1Enc = storage_path('app/backups/legacy-v1.sql.enc');
        $tempV1Dec = storage_path('app/backups/legacy-v1-dec.sql');

        $plaintext = "SELECT 'LEGACY_BACKUP_SQL_RESTORATION';";

        // Buat manual paket v1: Magic (13 B) + Salt (16 B) + IV (16 B) + HMAC (32 B) + Ciphertext
        $salt = random_bytes(16);
        $iv = random_bytes(16);
        $derivedKey = hash_pbkdf2('sha256', $this->primaryKey, $salt, 10000, 32, true);
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $derivedKey, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext, $derivedKey, true);

        $v1Payload = BackupDatabaseService::ENC_MAGIC_HEADER_V1.$salt.$iv.$hmac.$ciphertext;
        File::put($tempV1Enc, $v1Payload);

        // Uji pemulihan format v1
        $this->backupService->decryptFile($tempV1Enc, $tempV1Dec, $this->primaryKey);
        $this->assertFileExists($tempV1Dec);
        $this->assertEquals($plaintext, file_get_contents($tempV1Dec));

        @unlink($tempV1Enc);
        @unlink($tempV1Dec);
    }

    /**
     * Uji 3: Rotasi kunci (Key Rotation) didukung melalui konfigurasi fallback_keys.
     */
    public function test_key_rotation_resilience_via_fallback_keys(): void
    {
        $tempSource = storage_path('app/backups/rotation-test.txt');
        $tempEnc = storage_path('app/backups/rotation-test.txt.enc');
        $tempDec = storage_path('app/backups/rotation-test-dec.txt');

        File::put($tempSource, 'Kerahasiaan Cadangan Negara 2026');

        // Enkripsi menggunakan kunci lama (primaryKey)
        $this->backupService->encryptFile($tempSource, $tempEnc, $this->primaryKey);

        // Simulasikan rotasi kunci di masa depan:
        // Kunci aktif berganti ke rotatedKey, dan primaryKey masuk ke daftar fallback
        config(['backup.encryption_key' => $this->rotatedKey]);
        config(['backup.fallback_keys' => [$this->primaryKey]]);

        // Dekripsi otomatis berhasil karena mencoba fallback key
        $this->backupService->decryptFile($tempEnc, $tempDec, $this->rotatedKey);
        $this->assertFileExists($tempDec);
        $this->assertEquals('Kerahasiaan Cadangan Negara 2026', file_get_contents($tempDec));

        @unlink($tempSource);
        @unlink($tempEnc);
        @unlink($tempDec);
    }

    /**
     * Uji 4: Guardrail produksi menolak jika kunci enkripsi cadangan menggunakan APP_KEY atau kosong.
     */
    public function test_production_guardrail_rejects_app_key_or_empty_key(): void
    {
        $this->app['env'] = 'production';
        config(['app.key' => 'base64:ApplicationHostRootSecretKey123456=']);

        // Kasus 1: Kunci kosong di produksi
        config(['backup.encryption_key' => null]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Kunci enkripsi cadangan (BACKUP_ENCRYPTION_KEY) wajib dikonfigurasi pada lingkungan produksi.');

        $this->backupService->resolveEncryptionKey();
    }

    /**
     * Uji 5: Guardrail produksi menolak jika BACKUP_ENCRYPTION_KEY identik dengan APP_KEY.
     */
    public function test_production_guardrail_rejects_identical_app_key(): void
    {
        $this->app['env'] = 'production';
        config(['app.key' => 'base64:SameKeyUsedForAppAndBackup1234567=']);
        config(['backup.encryption_key' => 'base64:SameKeyUsedForAppAndBackup1234567=']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('tidak boleh menggunakan APP_KEY pada lingkungan produksi');

        $this->backupService->resolveEncryptionKey();
    }

    /**
     * Uji 6: Pada lingkungan produksi, offsite disk wajib dikonfigurasi dan fail-closed jika gagal.
     */
    public function test_production_mandates_offsite_backup_disk(): void
    {
        $this->app['env'] = 'production';
        config(['app.key' => 'base64:ApplicationHostRootSecretKey123456=']);
        config(['backup.encryption_key' => 'base64:DedicatedBackupSecretKey99887766=']);
        config(['backup.offsite_disk' => null]); // Sengaja kosong di produksi

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Offsite backup disk belum dikonfigurasi');

        $this->backupService->createSnapshot();
    }

    /**
     * Uji 7: Pada lingkungan produksi, jika replikasi ke offsite disk gagal, proses snapshot fail-closed.
     */
    public function test_production_fails_closed_when_offsite_replication_fails(): void
    {
        $this->app['env'] = 'production';
        config(['app.key' => 'base64:ApplicationHostRootSecretKey123456=']);
        config(['backup.encryption_key' => 'base64:DedicatedBackupSecretKey99887766=']);
        config(['backup.offsite_disk' => 'unreachable_s3']);

        Storage::fake('unreachable_s3');
        // Simulasikan storage error dengan disk tak valid / write throw
        Storage::shouldReceive('disk')->with('unreachable_s3')->andThrow(new \RuntimeException('Connection to S3 bucket timed out.'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Replikasi offsite cadangan ke disk [unreachable_s3] gagal');

        $this->backupService->createSnapshot();
    }
}
