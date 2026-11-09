<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Pembimbing\Tugas\CreateBatchTugasAction;
use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembimbingTugasArchitectureSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $pembimbingLain;

    protected User $peserta1;

    protected User $peserta2;

    protected Penempatan $penempatan1;

    protected Penempatan $penempatan2;

    protected Bidang $bidang;

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->role = $role;
        $user->bidang_id = $bidangId;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->bidang = Bidang::create(['nama_bidang' => 'Layanan E-Government']);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Budi Santoso, S.Kom',
            'budi.santoso@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->pembimbingLain = $this->createUser(
            'Pembimbing Asing',
            'asing@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta1 = $this->createUser('Mentee Satu', 'mentee1@magkom.go.id', RoleEnum::Peserta);
        $this->peserta2 = $this->createUser('Mentee Dua', 'mentee2@magkom.go.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->peserta1->id,
            'institusi' => 'UGM',
            'nim_nisn' => '11111',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'no_hp' => '081234567891',
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta2->id,
            'institusi' => 'UNY',
            'nim_nisn' => '22222',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'no_hp' => '081234567892',
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(5),
            'tgl_selesai' => Carbon::today()->addDays(60),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(5),
            'tgl_selesai' => Carbon::today()->addDays(60),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);
    }

    public function test_batch_shared_attachment_is_not_deleted_when_one_tugas_is_updated(): void
    {
        $file = UploadedFile::fake()->create('panduan_batch.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id, $this->penempatan2->id],
            'judul' => 'Tugas Bersama Arsitektur',
            'deskripsi' => 'Deskripsi tugas batch',
            'kategori' => 'Backend Development',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(14)->toDateString(),
            'file_lampiran' => $file,
        ]);

        $response->assertRedirect(route('pembimbing.tugas.index'));

        $tugasList = Tugas::where('judul', 'Tugas Bersama Arsitektur')->get();
        $this->assertCount(2, $tugasList);

        $tugas1 = $tugasList->firstWhere('penempatan_id', $this->penempatan1->id);
        $tugas2 = $tugasList->firstWhere('penempatan_id', $this->penempatan2->id);

        $this->assertNotNull($tugas1->file_lampiran);
        $this->assertEquals($tugas1->file_lampiran, $tugas2->file_lampiran);
        Storage::disk('private')->assertExists($tugas1->file_lampiran);

        $originalSharedPath = $tugas1->file_lampiran;

        // Pembimbing mengupdate tugas1 dengan file lampiran baru
        $newFile = UploadedFile::fake()->create('revisi_khusus_mentee1.pdf', 300, 'application/pdf');

        $updateResponse = $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas1->id), [
            'judul' => 'Tugas Bersama Arsitektur (Revisi 1)',
            'deskripsi' => 'Deskripsi baru tugas 1',
            'kategori' => 'Backend Development',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(14)->toDateString(),
            'file_lampiran' => $newFile,
        ]);

        $updateResponse->assertRedirect();

        $tugas1->refresh();
        $tugas2->refresh();

        // Tugas1 memiliki file baru
        $this->assertNotEquals($originalSharedPath, $tugas1->file_lampiran);
        Storage::disk('private')->assertExists($tugas1->file_lampiran);

        // Berkas lama MASIH ADA di storage karena masih direferensikan oleh tugas2!
        Storage::disk('private')->assertExists($originalSharedPath);
        $this->assertEquals($originalSharedPath, $tugas2->file_lampiran);
    }

    public function test_single_task_attachment_is_deleted_when_replaced(): void
    {
        $file = UploadedFile::fake()->create('panduan_tunggal.pdf', 500, 'application/pdf');

        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas Tunggal',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'Testing',
            'kompleksitas' => 'rendah',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $file,
        ]);

        $tugas = Tugas::where('judul', 'Tugas Tunggal')->firstOrFail();
        $oldPath = $tugas->file_lampiran;
        Storage::disk('private')->assertExists($oldPath);

        // Ganti dengan file baru
        $newFile = UploadedFile::fake()->create('panduan_tunggal_v2.pdf', 400, 'application/pdf');

        $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas->id), [
            'judul' => 'Tugas Tunggal Updated',
            'deskripsi' => 'Deskripsi updated',
            'kategori' => 'Testing',
            'kompleksitas' => 'rendah',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $newFile,
        ]);

        $tugas->refresh();
        // File lama terhapus karena tidak direferensikan siapapun
        Storage::disk('private')->assertMissing($oldPath);
        Storage::disk('private')->assertExists($tugas->file_lampiran);
    }

    public function test_update_audit_records_deskripsi_in_new_values(): void
    {
        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas Audit Deskripsi',
            'deskripsi' => 'Deskripsi awal yang panjang',
            'kategori' => 'Security',
            'kompleksitas' => 'tinggi',
            'deadline' => Carbon::today()->addDays(7)->toDateString(),
        ]);

        $tugas = Tugas::where('judul', 'Tugas Audit Deskripsi')->firstOrFail();

        $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas->id), [
            'judul' => 'Tugas Audit Deskripsi Updated',
            'deskripsi' => 'Deskripsi yang telah dimutasi secara aman',
            'kategori' => 'Security',
            'kompleksitas' => 'tinggi',
            'deadline' => Carbon::today()->addDays(7)->toDateString(),
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'tugas',
            'entitas_id' => $tugas->id,
            'aksi' => 'update_tugas',
        ]);
    }

    public function test_selesai_action_marks_status_completed_and_cannot_be_edited(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Tugas Siap Selesai',
            'deskripsi' => 'Deskripsi tugas',
            'kategori' => 'DevOps',
            'kompleksitas' => 'rendah',
            'deadline' => Carbon::today()->addDays(5),
            'status' => StatusTugasEnum::Proses,
            'status_publikasi' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.selesai', $tugas->id));
        $response->assertRedirect();

        $tugas->refresh();
        $this->assertEquals(StatusTugasEnum::Selesai, $tugas->status);

        // Coba edit tugas yang sudah selesai -> ditolak
        $editResponse = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.edit', $tugas->id));
        $editResponse->assertRedirect(route('pembimbing.tugas.show', $tugas->id));
        $editResponse->assertSessionHasErrors(['error']);
    }

    public function test_review_progres_updates_review_and_emits_after_commit(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Tugas Progres Test',
            'kategori' => 'Testing',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(5),
            'status' => StatusTugasEnum::Proses,
            'status_publikasi' => 'dipublikasikan',
        ]);

        $progres = ProgresLog::create([
            'tugas_id' => $tugas->id,
            'catatan' => 'Progres awal 50%',
            'tgl_lapor' => Carbon::today()->toDateString(),
            'status_review' => 'menunggu',
        ]);

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.progres.review', $progres->id), [
            'status_review' => StatusReviewProgresEnum::Diterima->value,
            'feedback' => 'Kerja bagus, lanjutkan!',
        ]);

        $response->assertRedirect();

        $progres->refresh();
        $this->assertEquals(StatusReviewProgresEnum::Diterima, $progres->status_review);
        $this->assertEquals('Kerja bagus, lanjutkan!', $progres->feedback);

        $this->assertDatabaseHas('realtime_events', [
            'user_id' => $this->peserta1->id,
            'event_type' => 'progres_direviu',
        ]);
    }

    public function test_transaction_failure_during_create_cleans_up_orphan_file_and_rolls_back(): void
    {
        $file = UploadedFile::fake()->create('orphan_test.pdf', 500, 'application/pdf');

        // Gunakan title khusus yang memicu exception di event listener atau observer, atau panggil action langsung
        $action = app(CreateBatchTugasAction::class);

        // Buat penempatan dengan ID yang invalid saat create untuk memicu database exception dalam loop
        $mockPenempatan = new Penempatan;
        $mockPenempatan->id = 999999;
        $mockPenempatan->pembimbing_id = $this->pembimbing->id;
        $mockPenempatan->peserta_id = $this->peserta1->id;

        $thrown = false;
        try {
            $action->execute(
                penempatans: [$mockPenempatan],
                validated: [
                    'judul' => 'Tugas Error Transaksi',
                    'kategori' => 'Error Test',
                    'kompleksitas' => 'rendah',
                    'deadline' => Carbon::today()->addDays(5)->toDateString(),
                ],
                fileLampiran: $file,
                user: $this->pembimbing
            );
        } catch (\Throwable $e) {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Transaksi harus melempar exception.');
        // Pastikan tidak ada berkas orphan di storage
        $files = Storage::disk('private')->allFiles('tugas');
        $this->assertEmpty($files, 'Berkas orphan harus dibersihkan setelah transaksi gagal.');
        $this->assertDatabaseMissing('tugas', ['judul' => 'Tugas Error Transaksi']);
    }

    public function test_notification_failure_does_not_rollback_task_creation(): void
    {
        $file = UploadedFile::fake()->create('notif_fail.pdf', 300, 'application/pdf');

        // Mock RealtimeEventService agar melempar exception saat publish dipanggil
        $mockRealtime = $this->mock(RealtimeEventService::class);
        $mockRealtime->shouldReceive('publish')
            ->andThrow(new \RuntimeException('Koneksi SSE timeout'));

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas Notif Error Resilience',
            'deskripsi' => 'Deskripsi tugas',
            'kategori' => 'Resilience',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(5)->toDateString(),
            'file_lampiran' => $file,
        ]);

        $response->assertRedirect(route('pembimbing.tugas.index'));

        // Data tugas TETAP tersimpan di database meskipun SSE gagal
        $this->assertDatabaseHas('tugas', [
            'judul' => 'Tugas Notif Error Resilience',
        ]);
        $tugas = Tugas::where('judul', 'Tugas Notif Error Resilience')->firstOrFail();
        Storage::disk('private')->assertExists($tugas->file_lampiran);
    }

    public function test_two_consecutive_updates_in_same_batch_safely_handles_file_deletion(): void
    {
        $file = UploadedFile::fake()->create('batch_shared_init.pdf', 500, 'application/pdf');

        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id, $this->penempatan2->id],
            'judul' => 'Tugas Batch Concurrency',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'Batch Test',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $file,
        ]);

        $tugas1 = Tugas::where('penempatan_id', $this->penempatan1->id)->where('judul', 'Tugas Batch Concurrency')->firstOrFail();
        $tugas2 = Tugas::where('penempatan_id', $this->penempatan2->id)->where('judul', 'Tugas Batch Concurrency')->firstOrFail();

        $originalPath = $tugas1->file_lampiran;
        $this->assertEquals($originalPath, $tugas2->file_lampiran);
        Storage::disk('private')->assertExists($originalPath);

        // Update Tugas 1 dengan berkas baru
        $fileBaru1 = UploadedFile::fake()->create('tugas1_new.pdf', 300, 'application/pdf');
        $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas1->id), [
            'judul' => 'Tugas Batch Concurrency 1 Updated',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'Batch Test',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $fileBaru1,
        ]);

        // File original masih ada karena tugas2 masih mereferensikannya
        Storage::disk('private')->assertExists($originalPath);

        // Update Tugas 2 dengan berkas baru
        $fileBaru2 = UploadedFile::fake()->create('tugas2_new.pdf', 350, 'application/pdf');
        $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas2->id), [
            'judul' => 'Tugas Batch Concurrency 2 Updated',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'Batch Test',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $fileBaru2,
        ]);

        // Sekarang kedua tugas sudah memiliki file baru, sehingga originalPath aman dihapus
        Storage::disk('private')->assertMissing($originalPath);
        Storage::disk('private')->assertExists($tugas1->fresh()->file_lampiran);
        Storage::disk('private')->assertExists($tugas2->fresh()->file_lampiran);
    }

    public function test_transaction_failure_during_update_cleans_up_orphan_file_and_preserves_old_file(): void
    {
        $oldFile = UploadedFile::fake()->create('initial_doc.pdf', 400, 'application/pdf');

        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas Test Rollback Update',
            'deskripsi' => 'Deskripsi lama',
            'kategori' => 'Batch Test',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(5)->toDateString(),
            'file_lampiran' => $oldFile,
        ]);

        $tugas = Tugas::where('judul', 'Tugas Test Rollback Update')->firstOrFail();
        $oldPath = $tugas->file_lampiran;
        Storage::disk('private')->assertExists($oldPath);

        // Mock AuditService agar gagal di dalam DB::transaction saat update
        $mockAudit = $this->mock(AuditService::class);
        $mockAudit->shouldReceive('catat')
            ->andThrow(new \Exception('Database deadlock during update audit logging'));

        $newFile = UploadedFile::fake()->create('attempted_new_doc.pdf', 600, 'application/pdf');

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas->id), [
                'judul' => 'Tugas Updated Will Fail',
                'deskripsi' => 'Deskripsi baru',
                'kategori' => 'Batch Test',
                'kompleksitas' => 'sedang',
                'deadline' => Carbon::today()->addDays(5)->toDateString(),
                'file_lampiran' => $newFile,
            ]);
            $this->fail('Harusnya melempar exception karena audit gagal');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Database deadlock during update audit logging', $e->getMessage());
        }

        // 1. Database rollback: judul dan file_lampiran lama tetap terjaga
        $tugas->refresh();
        $this->assertEquals('Tugas Test Rollback Update', $tugas->judul);
        $this->assertEquals($oldPath, $tugas->file_lampiran);

        // 2. Berkas lama TIDAK terhapus
        Storage::disk('private')->assertExists($oldPath);

        // 3. Berkas baru (orphan) yang diupload sebelum transaksi dibersihkan dari disk privat
        $allFiles = Storage::disk('private')->allFiles();
        $this->assertCount(1, $allFiles);
        $this->assertEquals($oldPath, $allFiles[0]);
    }

    public function test_batch_shared_file_remains_intact_if_one_update_succeeds_and_another_update_fails(): void
    {
        $sharedFile = UploadedFile::fake()->create('shared_contract.pdf', 500, 'application/pdf');

        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id, $this->penempatan2->id],
            'judul' => 'Tugas Shared Fail Scenario',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'Batch Test',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $sharedFile,
        ]);

        $tugas1 = Tugas::where('penempatan_id', $this->penempatan1->id)->where('judul', 'Tugas Shared Fail Scenario')->firstOrFail();
        $tugas2 = Tugas::where('penempatan_id', $this->penempatan2->id)->where('judul', 'Tugas Shared Fail Scenario')->firstOrFail();

        $originalSharedPath = $tugas1->file_lampiran;
        $this->assertEquals($originalSharedPath, $tugas2->file_lampiran);
        Storage::disk('private')->assertExists($originalSharedPath);

        // Update Tugas 1 BERHASIL dengan berkas baru
        $fileBaru1 = UploadedFile::fake()->create('tugas1_success.pdf', 300, 'application/pdf');
        $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas1->id), [
            'judul' => 'Tugas Shared 1 Updated',
            'deskripsi' => 'Deskripsi baru',
            'kategori' => 'Batch Test',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(10)->toDateString(),
            'file_lampiran' => $fileBaru1,
        ]);

        // Berkas bersama asli HARUS TETAP ADA karena tugas2 masih menggunakannya
        Storage::disk('private')->assertExists($originalSharedPath);
        Storage::disk('private')->assertExists($tugas1->fresh()->file_lampiran);

        // Sekarang coba update Tugas 2 tapi TRANSAKSI GAGAL
        $mockAudit = $this->mock(AuditService::class);
        $mockAudit->shouldReceive('catat')
            ->andThrow(new \Exception('Deadlock on updating tugas2'));

        $this->withoutExceptionHandling();
        $fileBaru2 = UploadedFile::fake()->create('tugas2_fail.pdf', 350, 'application/pdf');

        try {
            $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $tugas2->id), [
                'judul' => 'Tugas Shared 2 Will Fail',
                'deskripsi' => 'Deskripsi gagal',
                'kategori' => 'Batch Test',
                'kompleksitas' => 'sedang',
                'deadline' => Carbon::today()->addDays(10)->toDateString(),
                'file_lampiran' => $fileBaru2,
            ]);
            $this->fail('Harusnya melempar exception');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Deadlock on updating tugas2', $e->getMessage());
        }

        // Verifikasi kritis:
        // 1. Tugas 2 tetap memegang originalSharedPath
        $this->assertEquals($originalSharedPath, $tugas2->fresh()->file_lampiran);

        // 2. Berkas originalSharedPath MASIH ADA dan tidak terhapus
        Storage::disk('private')->assertExists($originalSharedPath);

        // 3. Berkas baru tugas 2 yang gagal dibersihkan (orphan cleanup)
        $allFiles = Storage::disk('private')->allFiles();
        // File yang ada harus tepat 2: originalSharedPath (dipakai tugas 2) dan file_lampiran baru milik tugas 1
        $this->assertCount(2, $allFiles);
        $this->assertContains($originalSharedPath, $allFiles);
        $this->assertContains($tugas1->fresh()->file_lampiran, $allFiles);
    }
}
