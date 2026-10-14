<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\DocumentStorageService;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SecurityDocumentVerificationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $pesertaA;
    protected User $pesertaB;
    protected User $pembimbingA;
    protected User $pembimbingLain;
    protected User $admin;
    protected User $superAdmin;

    protected Pendaftaran $pendaftaranA;
    protected Pendaftaran $pendaftaranB;
    protected JenisDokumen $jenisKtp;
    protected Dokumen $dokumenA;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->jenisKtp = JenisDokumen::firstOrFail();

        $jurusan = Jurusan::create(['nama_jurusan' => 'Informatika', 'kategori' => 'mahasiswa', 'aktif' => true]);
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(5),
            'tgl_tutup' => now()->addDays(20),
            'aktif' => true,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika', 'aktif' => true]);
        $kuota = \App\Models\Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        // 1. Peserta A & Pendaftaran A
        $this->pesertaA = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pendaftaranA = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => 'berkas_belum_lengkap',
        ]);

        // Upload Dokumen A
        $fileA = UploadedFile::fake()->create('ktp_peserta_a.pdf', 500, 'application/pdf');
        $documentService = app(DocumentStorageService::class);
        $this->dokumenA = $documentService->simpan($this->pendaftaranA, $this->jenisKtp, $fileA);

        // 2. Peserta B & Pendaftaran B
        $this->pesertaB = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pendaftaranB = Pendaftaran::create([
            'user_id' => $this->pesertaB->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => 'berkas_belum_lengkap',
        ]);

        // 3. Pembimbing & Staf
        $this->pembimbingA = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        Penempatan::create([
            'peserta_id' => $this->pesertaA->id,
            'pembimbing_id' => $this->pembimbingA->id,
            'bidang_id' => $bidang->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->pembimbingLain = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
    }

    /**
     * BOLA/IDOR TEST: Peserta B dilarang keras mengunduh atau melihat dokumen milik Peserta A.
     */
    public function test_bola_idor_unauthorized_user_cannot_download_or_preview_document(): void
    {
        $signedDownloadUrl = $this->dokumenA->getSignedDownloadUrl(10);
        $signedPreviewUrl = $this->dokumenA->getSignedPreviewUrl(10);

        // 1. Peserta B mencoba akses dokumen Peserta A -> wajib 403 Forbidden
        $this->actingAs($this->pesertaB)
            ->get($signedDownloadUrl)
            ->assertForbidden();

        $this->actingAs($this->pesertaB)
            ->get($signedPreviewUrl)
            ->assertForbidden();

        // 2. Pembimbing lain yang tidak membimbing Peserta A -> wajib 403 Forbidden
        $this->actingAs($this->pembimbingLain)
            ->get($signedDownloadUrl)
            ->assertForbidden();
    }

    /**
     * AUTHORIZATION TEST: Pemilik sah, Pembimbing resmi, Admin, dan SuperAdmin diizinkan mengakses.
     */
    public function test_authorized_actors_can_access_document(): void
    {
        $signedDownloadUrl = $this->dokumenA->getSignedDownloadUrl(10);

        // 1. Pemilik sah (Peserta A) -> 200 OK
        $this->actingAs($this->pesertaA)
            ->get($signedDownloadUrl)
            ->assertOk();

        // 2. Pembimbing resmi Peserta A -> 200 OK
        $this->actingAs($this->pembimbingA)
            ->get($signedDownloadUrl)
            ->assertOk();

        // 3. Admin -> 200 OK
        $this->actingAs($this->admin)
            ->get($signedDownloadUrl)
            ->assertOk();

        // 4. SuperAdmin -> 200 OK
        $this->actingAs($this->superAdmin)
            ->get($signedDownloadUrl)
            ->assertOk();
    }

    /**
     * SIGNATURE TAMPERING TEST: URL yang tanda tangannya diubah / tidak valid ditolak 403.
     */
    public function test_tampered_or_missing_signature_aborts_with_403(): void
    {
        // URL tanpa signature
        $rawUrl = route('berkas.download', ['dokumen' => $this->dokumenA->id]);

        $this->actingAs($this->pesertaA)
            ->get($rawUrl)
            ->assertForbidden();

        // URL dengan signature yang diubah / tampered
        $tamperedUrl = $this->dokumenA->getSignedDownloadUrl(10) . 'tampered';

        $this->actingAs($this->pesertaA)
            ->get($tamperedUrl)
            ->assertForbidden();
    }

    /**
     * BOLA/IDOR UPLOAD TEST: Peserta B ditolak mengunggah berkas ke pendaftaran Peserta A.
     */
    public function test_bola_idor_peserta_b_cannot_upload_document_to_pendaftaran_a(): void
    {
        $fakeFile = UploadedFile::fake()->create('hacker_ktp.pdf', 300, 'application/pdf');

        $this->actingAs($this->pesertaB)
            ->post(route('peserta.berkas.unggah', ['pendaftaran' => $this->pendaftaranA->id]), [
                'jenis_dokumen_id' => $this->jenisKtp->id,
                'file' => $fakeFile,
            ])
            ->assertForbidden();
    }

    /**
     * SECURITY AUDIT: Injeksi Magic Bytes & MIME Type Confusion ditolak oleh service.
     */
    public function test_malicious_executable_or_fake_pdf_is_rejected_by_magic_bytes_check(): void
    {
        $documentService = app(DocumentStorageService::class);

        // Buat file berbahaya yang bertipe executable / script
        $maliciousFile = UploadedFile::fake()->create('exploit.php', 100, 'application/x-php');

        $this->expectException(ValidationException::class);
        $documentService->simpan($this->pendaftaranA, $this->jenisKtp, $maliciousFile);
    }
}
