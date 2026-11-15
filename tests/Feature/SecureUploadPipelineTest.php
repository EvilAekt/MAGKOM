<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\AntivirusScanService;
use App\Services\DocumentStorageService;
use Database\Seeders\JenisDokumenSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SecureUploadPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected Pendaftaran $pendaftaran;

    protected JenisDokumen $jenisDokumen;

    protected DocumentStorageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $user = User::factory()->create();
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $user->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
            'durasi_bulan' => 1,
        ]);

        $this->seed(JenisDokumenSeeder::class);
        $this->jenisDokumen = JenisDokumen::firstOrFail();

        $this->service = app(DocumentStorageService::class);
    }

    /**
     * Uji 1: Unggah berkas valid (PDF bersih) berhasil disimpan dan melewati karantina dengan aman.
     */
    public function test_clean_pdf_upload_succeeds_and_cleans_quarantine(): void
    {
        $file = UploadedFile::fake()->create('dokumen_resmi.pdf', 150, 'application/pdf');

        $dokumen = $this->service->simpan($this->pendaftaran, $this->jenisDokumen, $file);

        $this->assertDatabaseHas('dokumen', [
            'id' => $dokumen->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'nama_asli' => 'dokumen_resmi.pdf',
        ]);

        // Berkas final tersimpan di storage privat
        Storage::disk('private')->assertExists($dokumen->path);

        // Folder karantina bersih
        $quarantineFiles = Storage::disk('private')->files('quarantine');
        $this->assertEmpty($quarantineFiles);
    }

    /**
     * Uji 2: Berkas yang memuat signature uji virus EICAR ditolak dan dibersihkan dari karantina.
     */
    public function test_eicar_infected_file_is_blocked_and_quarantined(): void
    {
        $payload = AntivirusScanService::EICAR_TEST_SIGNATURE;
        $file = UploadedFile::fake()->createWithContent('malware_test.pdf', $payload);

        $this->expectException(ValidationException::class);

        try {
            $this->service->simpan($this->pendaftaran, $this->jenisDokumen, $file);
        } finally {
            // Pastikan tidak ada data yang masuk ke database
            $this->assertDatabaseMissing('dokumen', [
                'nama_asli' => 'malware_test.pdf',
            ]);

            // Area karantina harus bersih (berkas terinfeksi dihapus)
            $this->assertEmpty(Storage::disk('private')->files('quarantine'));
        }
    }

    /**
     * Uji 3: Berkas executable biner Windows PE (disamarkan sebagai PDF) ditolak oleh inspeksi heuristik.
     */
    public function test_disguised_executable_is_rejected(): void
    {
        // Header MZ adalah executable Windows
        $payload = "MZ\x90\x00\x03\x00\x00\x00Fake PDF Trojan Payload";
        $file = UploadedFile::fake()->createWithContent('disguised_trojan.pdf', $payload);

        $this->expectException(ValidationException::class);

        $this->service->simpan($this->pendaftaran, $this->jenisDokumen, $file);
    }

    /**
     * Uji 4: Berkas yang disusupi PHP WebShell ditolak oleh antivirus scanner.
     */
    public function test_embedded_php_webshell_is_rejected(): void
    {
        $payload = "%PDF-1.4\n<?php eval(\$_POST['cmd']); ?>";
        $file = UploadedFile::fake()->createWithContent('webshell.pdf', $payload);

        $this->expectException(ValidationException::class);

        $this->service->simpan($this->pendaftaran, $this->jenisDokumen, $file);
    }

    /**
     * Uji 5: Penggantian Atomik (Atomic Replacement) - Berkas lama tidak hilang sebelum berkas baru sukses.
     */
    public function test_atomic_replacement_preserves_old_file_when_subsequent_update_succeeds(): void
    {
        // 1. Simpan berkas awal
        $initialFile = UploadedFile::fake()->create('surat_v1.pdf', 100, 'application/pdf');
        $dokumenV1 = $this->service->simpan($this->pendaftaran, $this->jenisDokumen, $initialFile);
        $oldPath = $dokumenV1->path;

        Storage::disk('private')->assertExists($oldPath);

        // 2. Unggah berkas pengganti yang valid (v2)
        $newFile = UploadedFile::fake()->create('surat_v2.pdf', 120, 'application/pdf');
        $dokumenV2 = $this->service->simpan($this->pendaftaran, $this->jenisDokumen, $newFile);

        // Berkas baru ada dan terverifikasi
        Storage::disk('private')->assertExists($dokumenV2->path);
        $this->assertNotSame($oldPath, $dokumenV2->path);

        // Berkas lama dihapus setelah berkas baru berhasil disimpan secara atomik
        Storage::disk('private')->assertMissing($oldPath);

        // Catatan database diperbarui
        $this->assertDatabaseHas('dokumen', [
            'id' => $dokumenV1->id,
            'nama_asli' => 'surat_v2.pdf',
        ]);
    }
}
