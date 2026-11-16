<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\BackupDatabaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupSqlParserTest extends TestCase
{
    use RefreshDatabase;

    protected BackupDatabaseService $backupService;

    protected string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupService = app(BackupDatabaseService::class);
        $this->backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($this->backupDir);
    }

    public function test_sql_restore_parser_handles_multiline_strings_with_semicolons(): void
    {
        // 1. Siapkan tabel temporer untuk pengujian parser
        DB::statement('CREATE TABLE IF NOT EXISTS test_multiline_sql (id INT PRIMARY KEY, content TEXT, notes TEXT)');
        DB::statement('DELETE FROM test_multiline_sql');

        // 2. Siapkan konten SQL dump yang memuat teks multibaris dengan titik koma di akhir baris
        $sqlContent = <<<SQL
-- Komentar awalan dump
/*!40101 SET @saved_cs_client = 1 */;

/* Komentar blok
   yang berada di beberapa baris */

INSERT INTO test_multiline_sql (id, content, notes) VALUES (
    1,
    'Baris pertama dengan titik koma di akhir;\nBaris kedua teks normal;\nBaris ketiga selesai;',
    'Catatan sederhana'
);

INSERT INTO test_multiline_sql (id, content, notes) VALUES (
    2,
    'Teks dengan quote ber-escape: ''quoted text; with semicolon'' dan tanda titik koma;',
    'Catatan kedua'
);

SQL;

        $filename = 'backup-magkom-test-multiline-'.uniqid().'.sql';
        $filePath = $this->backupDir.DIRECTORY_SEPARATOR.$filename;
        File::put($filePath, $sqlContent);

        try {
            // 3. Jalankan restore via BackupDatabaseService
            $result = $this->backupService->restoreDatabase($filename, 'sqlite');

            $this->assertTrue($result['success']);
            $this->assertGreaterThanOrEqual(2, $result['executed_statements']);

            // 4. Verifikasi bahwa data berhasil masuk secara utuh tanpa terpotong
            $row1 = DB::table('test_multiline_sql')->where('id', 1)->first();
            $this->assertNotNull($row1);
            $this->assertStringContainsString('Baris pertama dengan titik koma di akhir;', $row1->content);
            $this->assertStringContainsString('Baris kedua teks normal;', $row1->content);

            $row2 = DB::table('test_multiline_sql')->where('id', 2)->first();
            $this->assertNotNull($row2);
            $this->assertStringContainsString('with semicolon', $row2->content);
        } finally {
            @unlink($filePath);
            DB::statement('DROP TABLE IF EXISTS test_multiline_sql');
        }
    }
}
