<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesertaTugasBatalKirimTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;

    protected User $otherPeserta;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected Tugas $tugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);
        Storage::fake('private');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan aplikasi kedinasan',
            'aktif' => true,
        ]);

        $this->pembimbing = new User([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'budi.santoso@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User([
            'nama' => 'Naufal Pratama',
            'email' => 'naufal.peserta@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        $this->otherPeserta = new User([
            'nama' => 'Peserta Lain',
            'email' => 'lain.peserta@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->otherPeserta->role = RoleEnum::Peserta;
        $this->otherPeserta->email_verified_at = now();
        $this->otherPeserta->save();

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(80)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);

        $this->tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Perancangan Antarmuka Modul Peserta',
            'deskripsi' => 'Selesaikan perancangan antarmuka sesuai standar MagKom.',
            'deadline' => now()->addDays(7)->toDateString(),
            'status' => StatusTugasEnum::Proses,
            'status_publikasi' => 'dipublikasikan',
            'kategori' => 'Frontend',
            'kompleksitas' => KompleksitasEnum::Sedang,
        ]);
    }

    public function test_peserta_melihat_tombol_dan_modal_batal_kirim_saat_tugas_diserahkan(): void
    {
        $file = UploadedFile::fake()->create('dokumen_tugas.pdf', 500, 'application/pdf');
        $filePath = $file->store("progres/{$this->tugas->id}", 'private');

        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Laporan awal tugas.',
            'file' => $filePath,
            'link' => 'https://drive.google.com/file/d/sample',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $this->tugas));

        $response->assertOk();
        $response->assertSee('Batalkan pengiriman');
        $response->assertSee('modalKonfirmasiBatalKirim');
        $response->assertSee('formBatalKirimTugas');
        $response->assertSee('Diserahkan');
    }

    public function test_peserta_dapat_membatalkan_pengiriman_tugas(): void
    {
        $file = UploadedFile::fake()->create('dokumen_tugas.pdf', 500, 'application/pdf');
        $filePath = $file->store("progres/{$this->tugas->id}", 'private');

        $progres = ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Laporan awal tugas.',
            'file' => $filePath,
            'link' => 'https://github.com/magkom/demo-repo',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        $this->assertDatabaseHas('progres_log', [
            'id' => $progres->id,
            'tugas_id' => $this->tugas->id,
        ]);

        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.batal-kirim', $this->tugas));

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));
        $response->assertSessionHas('success');
        $response->assertSessionHas('unsubmitted_file', $filePath);
        $response->assertSessionHas('unsubmitted_link', 'https://github.com/magkom/demo-repo');

        $this->assertDatabaseMissing('progres_log', [
            'id' => $progres->id,
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'progres_log',
            'entitas_id' => $progres->id,
        ]);
    }

    public function test_peserta_dapat_menyerahkan_kembali_setelah_batal_kirim_menggunakan_unsubmitted_file(): void
    {
        $file = UploadedFile::fake()->create('dokumen_tugas.pdf', 500, 'application/pdf');
        $filePath = $file->store("progres/{$this->tugas->id}", 'private');

        $progres = ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Laporan awal tugas.',
            'file' => $filePath,
            'link' => 'https://github.com/magkom/demo-repo',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        // 1. Batalkan pengiriman
        $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.batal-kirim', $this->tugas));

        $this->assertDatabaseMissing('progres_log', [
            'id' => $progres->id,
        ]);

        // 2. Serahkan kembali dengan file yang sama (via recent_file_path) dan update link
        $resubmitResponse = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.progres', $this->tugas), [
                'recent_file_path' => $filePath,
                'link' => 'https://github.com/magkom/updated-repo',
                'catatan' => 'Laporan perbaikan setelah pembatalan.',
            ]);

        $resubmitResponse->assertRedirect(route('peserta.tugas.show', $this->tugas));

        $this->assertDatabaseHas('progres_log', [
            'tugas_id' => $this->tugas->id,
            'file' => $filePath,
            'link' => 'https://github.com/magkom/updated-repo',
            'status_review' => StatusReviewProgresEnum::Menunggu->value,
        ]);
    }

    public function test_peserta_tidak_dapat_batal_kirim_jika_tugas_sudah_selesai(): void
    {
        $this->tugas->update(['status' => StatusTugasEnum::Selesai]);

        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Tugas sudah dinilai selesai.',
            'link' => 'https://example.com/work',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Diterima,
        ]);

        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.batal-kirim', $this->tugas));

        $response->assertSessionHas('error');
    }

    public function test_peserta_tidak_dapat_batal_kirim_jika_status_review_bukan_menunggu(): void
    {
        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Tugas sudah diterima pembimbing.',
            'link' => 'https://example.com/work',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Diterima,
        ]);

        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.batal-kirim', $this->tugas));

        $response->assertSessionHas('error');
    }

    public function test_peserta_lain_tidak_dapat_membatalkan_tugas_peserta_pertama_idor_protection(): void
    {
        // Berikan penempatan aktif untuk otherPeserta agar lolos middleware EnsurePesertaDiterima
        Penempatan::create([
            'peserta_id' => $this->otherPeserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(80)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);

        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Laporan milik peserta 1.',
            'link' => 'https://example.com/work',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        $response = $this->actingAs($this->otherPeserta)
            ->post(route('peserta.tugas.batal-kirim', $this->tugas));

        $response->assertForbidden();
    }

    public function test_pembatalan_berulang_tidak_merusak_status(): void
    {
        // 1. Batalkan ketika tidak ada progres log aktif
        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.batal-kirim', $this->tugas));

        $response->assertSessionHas('error');
        $this->assertEquals(0, ProgresLog::where('tugas_id', $this->tugas->id)->count());
        $this->assertEquals(StatusTugasEnum::Proses, $this->tugas->fresh()->status);
    }

    public function test_recent_file_path_milik_tugas_lain_atau_peserta_lain_ditolak(): void
    {
        // Buat file milik peserta lain
        $otherFile = UploadedFile::fake()->create('other_secret.pdf', 300, 'application/pdf');
        $otherPath = $otherFile->store('progres/9999', 'private');

        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.progres', $this->tugas), [
                'recent_file_path' => $otherPath,
                'catatan' => 'Mencoba merujuk file peserta lain.',
            ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));

        // Verifikasi file tidak di-attach karena bukan milik peserta/tugas ini
        $createdProgres = ProgresLog::where('tugas_id', $this->tugas->id)->first();
        $this->assertNotNull($createdProgres);
        $this->assertNull($createdProgres->file);
    }

    public function test_path_traversal_pada_recent_file_path_ditolak(): void
    {
        $traversalPath = "progres/{$this->tugas->id}/../../.env";

        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.progres', $this->tugas), [
                'recent_file_path' => $traversalPath,
                'catatan' => 'Mencoba path traversal.',
            ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));

        $createdProgres = ProgresLog::where('tugas_id', $this->tugas->id)->first();
        $this->assertNotNull($createdProgres);
        $this->assertNull($createdProgres->file);
    }

    public function test_file_yang_sudah_tidak_tersedia_di_disk_ditangani_dengan_aman(): void
    {
        $nonExistentPath = "progres/{$this->tugas->id}/ghost_file.pdf";

        $response = $this->actingAs($this->peserta)
            ->post(route('peserta.tugas.progres', $this->tugas), [
                'recent_file_path' => $nonExistentPath,
                'catatan' => 'File tidak ada di disk fisik.',
            ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));

        $createdProgres = ProgresLog::where('tugas_id', $this->tugas->id)->first();
        $this->assertNotNull($createdProgres);
        $this->assertNull($createdProgres->file);
    }

    public function test_kontrak_json_dan_redirect_sesuai_permintaan_frontend(): void
    {
        $file = UploadedFile::fake()->create('dokumen_ajax.pdf', 200, 'application/pdf');
        $filePath = $file->store("progres/{$this->tugas->id}", 'private');

        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Laporan ajax.',
            'file' => $filePath,
            'link' => 'https://example.com/ajax-link',
            'tgl_lapor' => now()->toDateString(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        // AJAX Request (expectsJson)
        $response = $this->actingAs($this->peserta)
            ->postJson(route('peserta.tugas.batal-kirim', $this->tugas));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'unsubmitted_file' => $filePath,
            'unsubmitted_link' => 'https://example.com/ajax-link',
        ]);
    }
}
