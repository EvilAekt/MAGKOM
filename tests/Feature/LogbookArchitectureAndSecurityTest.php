<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\RealtimeEvent;
use App\Models\User;
use App\Services\Pembimbing\LogbookDashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogbookArchitectureAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $pesertaA;

    protected User $pesertaB;

    protected User $pembimbingA;

    protected User $pembimbingB;

    protected User $admin;

    protected User $superAdmin;

    protected Penempatan $penempatanA;

    protected Penempatan $penempatanB;

    protected Bidang $bidang;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Tata Kelola Komunikasi & Informatika',
            'deskripsi' => 'Pengelolaan sistem dan informasi publik',
            'kuota' => 15,
        ]);

        $this->pesertaA = $this->createUser('Peserta A', 'peserta.a@kominfo.test', RoleEnum::Peserta);
        $this->pesertaB = $this->createUser('Peserta B', 'peserta.b@kominfo.test', RoleEnum::Peserta);

        $this->pembimbingA = $this->createUser('Pembimbing A', 'pembimbing.a@kominfo.test', RoleEnum::Pembimbing);
        $this->pembimbingB = $this->createUser('Pembimbing B', 'pembimbing.b@kominfo.test', RoleEnum::Pembimbing);

        $this->admin = $this->createUser('Admin Kominfo', 'admin.logbook@kominfo.test', RoleEnum::Admin);
        $this->superAdmin = $this->createUser('SuperAdmin Audit', 'superadmin.logbook@kominfo.test', RoleEnum::SuperAdmin);

        $this->penempatanA = Penempatan::create([
            'peserta_id' => $this->pesertaA->id,
            'pembimbing_id' => $this->pembimbingA->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subWeeks(4),
            'tgl_selesai' => Carbon::today()->addWeeks(8),
        ]);

        $this->penempatanB = Penempatan::create([
            'peserta_id' => $this->pesertaB->id,
            'pembimbing_id' => $this->pembimbingB->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subWeeks(4),
            'tgl_selesai' => Carbon::today()->addWeeks(8),
        ]);
    }

    private function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'Secret123!';
        $user->role = $role;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    public function test_peserta_a_tidak_bisa_mengubah_atau_mengunduh_logbook_peserta_b_anti_idor(): void
    {
        $logbookB = Logbook::create([
            'penempatan_id' => $this->penempatanB->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDays(2),
            'kegiatan' => 'Kegiatan rahasia peserta B.',
            'file_laporan' => 'logbook/test/file-b.pdf',
            'status' => StatusLogbookEnum::Terkirim,
        ]);
        Storage::disk('private')->put('logbook/test/file-b.pdf', '%PDF-1.4 dummy content');

        // Peserta A mencoba update logbook B -> 403 Forbidden
        $responseUpdate = $this->actingAs($this->pesertaA)->putJson(route('peserta.logbook.update', $logbookB), [
            'tanggal' => Carbon::today()->subDays(2)->toDateString(),
            'kegiatan' => 'Mencoba manipulasi kegiatan milik peserta B secara ilegal.',
        ]);
        $responseUpdate->assertForbidden();

        // Peserta A mencoba unduh lampiran logbook B -> 403 Forbidden
        $responseDownload = $this->actingAs($this->pesertaA)->get(route('peserta.logbook.lampiran', $logbookB));
        $responseDownload->assertForbidden();
    }

    public function test_pembimbing_a_tidak_bisa_mereview_logbook_mentee_pembimbing_b(): void
    {
        $logbookB = Logbook::create([
            'penempatan_id' => $this->penempatanB->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Kegiatan mentee pembimbing B.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.review', $logbookB), [
            'status' => 'disetujui',
            'catatan_pembimbing' => 'Mencoba menyetujui logbook peserta yang bukan bimbingan saya.',
        ]);

        $response->assertForbidden();
    }

    public function test_superadmin_strictly_read_only_ditolak_mereview_dan_bulk_review_secara_mutlak(): void
    {
        $logbookA = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Kegiatan mentee pembimbing A.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Review satuan oleh SuperAdmin -> Ditolak (403)
        $responseReview = $this->actingAs($this->superAdmin)->postJson(route('pembimbing.logbook.review', $logbookA), [
            'status' => 'disetujui',
            'catatan_pembimbing' => 'SuperAdmin mencoba memberikan approval.',
        ]);
        $responseReview->assertForbidden();

        // Bulk review oleh SuperAdmin -> Ditolak (403)
        $responseBulk = $this->actingAs($this->superAdmin)->postJson(route('pembimbing.logbook.bulk-review'), [
            'logbook_ids' => [$logbookA->id],
            'catatan_pembimbing' => 'SuperAdmin mencoba bulk approval.',
        ]);
        $responseBulk->assertForbidden();
    }

    public function test_bulk_review_menolak_permintaan_jika_ada_id_di_luar_kewenangan_pembimbing(): void
    {
        $logbookA = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Logbook mentee A.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $logbookB = Logbook::create([
            'penempatan_id' => $this->penempatanB->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Logbook mentee B.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Pembimbing A mengirim ID campuran [milik A, milik B]
        $response = $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.bulk-review'), [
            'logbook_ids' => [$logbookA->id, $logbookB->id],
            'catatan_pembimbing' => 'Batch approval campuran.',
        ]);

        $response->assertForbidden();
        $this->assertEquals(StatusLogbookEnum::Terkirim, $logbookA->fresh()->status);
        $this->assertEquals(StatusLogbookEnum::Terkirim, $logbookB->fresh()->status);
    }

    public function test_update_logbook_membersihkan_file_lama_dan_tidak_meninggalkan_orphan_file(): void
    {
        $fileAwal = UploadedFile::fake()->create('laporan-minggu1.pdf', 200, 'application/pdf');

        $storeResponse = $this->actingAs($this->pesertaA)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->toDateString(),
            'kegiatan' => 'Menyelesaikan implementasi modul integrasi API.',
            'file_laporan' => $fileAwal,
        ]);
        $storeResponse->assertRedirect();

        $logbook = Logbook::where('penempatan_id', $this->penempatanA->id)->first();
        $this->assertNotNull($logbook);
        $pathAwal = $logbook->file_laporan;
        $this->assertNotNull($pathAwal);
        Storage::disk('private')->assertExists($pathAwal);

        // Peserta mengunggah file baru saat revisi
        $fileBaru = UploadedFile::fake()->create('laporan-minggu1-revisi.pdf', 300, 'application/pdf');
        $updateResponse = $this->actingAs($this->pesertaA)->put(route('peserta.logbook.update', $logbook), [
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->toDateString(),
            'kegiatan' => 'Revisi deskripsi: melengkapi hasil tangkapan layar antarmuka.',
            'file_laporan' => $fileBaru,
        ]);
        $updateResponse->assertRedirect();

        $logbook->refresh();
        $pathBaru = $logbook->file_laporan;
        $this->assertNotEquals($pathAwal, $pathBaru);
        Storage::disk('private')->assertExists($pathBaru);

        // File awal harus dibersihkan secara aman
        Storage::disk('private')->assertMissing($pathAwal);
    }

    public function test_update_logbook_dengan_tanggal_lampau_lebih_dari_tiga_hari_tidak_diblokir_validasi_mundur(): void
    {
        // Logbook dibuat 10 hari lalu (sebelumnya)
        $tglLampau = Carbon::today()->subDays(10);
        $logbook = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => $tglLampau,
            'kegiatan' => 'Kegiatan 10 hari lalu yang diminta revisi oleh pembimbing.',
            'status' => StatusLogbookEnum::PerluRevisi,
        ]);

        // Peserta memperbarui logbook revisi tersebut dengan tanggal asli yang sama
        $response = $this->actingAs($this->pesertaA)->putJson(route('peserta.logbook.update', $logbook), [
            'minggu_ke' => 1,
            'tanggal' => $tglLampau->toDateString(),
            'kegiatan' => 'Perbaikan uraian tugas: telah menambahkan log server dan tiket.',
        ]);

        $response->assertOk();
        $logbook->refresh();
        $this->assertEquals(StatusLogbookEnum::Terkirim, $logbook->status);
        $this->assertStringContainsString('Perbaikan uraian tugas', $logbook->kegiatan);
    }

    public function test_notifikasi_realtime_akurat_saat_pembimbing_hanya_memberikan_catatan_tanpa_status(): void
    {
        $logbook = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Kegiatan mandiri peserta A.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Pembimbing hanya mengisi catatan pembimbing tanpa mengubah status terkirim
        $response = $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.review', $logbook), [
            'catatan_pembimbing' => 'Teruskan progres sampai modul testing tuntas.',
        ]);

        $response->assertOk();
        $logbook->refresh();
        $this->assertEquals(StatusLogbookEnum::Terkirim, $logbook->status);

        // Pastikan title notifikasi BUKAN "Logbook Harian Disetujui"
        $event = RealtimeEvent::where('user_id', $this->pesertaA->id)
            ->where('event_type', RealtimeEventTypeEnum::LogbookDireviu->value)
            ->latest('id')
            ->first();

        $this->assertNotNull($event);
        $this->assertSame('Catatan Logbook Diperbarui', $event->payload['title']);
        $this->assertSame('Teruskan progres sampai modul testing tuntas.', $event->payload['catatan_pembimbing']);
        $this->assertNotSame('Logbook Harian Disetujui', $event->payload['title']);
    }

    public function test_dashboard_service_menghitung_statistik_akurat_dengan_agregasi_tunggal(): void
    {
        Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDays(3),
            'kegiatan' => 'Kegiatan disetujui.',
            'status' => StatusLogbookEnum::Disetujui,
            'catatan_pembimbing' => 'Bagus.',
        ]);

        Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 2,
            'tanggal' => Carbon::today()->subDays(2),
            'kegiatan' => 'Kegiatan perlu revisi.',
            'status' => StatusLogbookEnum::PerluRevisi,
            'catatan_pembimbing' => 'Perbaiki lampiran.',
        ]);

        Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 3,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Kegiatan menunggu.',
            'status' => StatusLogbookEnum::Terkirim,
            'catatan_pembimbing' => null,
        ]);

        /** @var LogbookDashboardService $service */
        $service = app(LogbookDashboardService::class);
        $stats = $service->getStatistics($this->pembimbingA);

        $this->assertSame(3, $stats['totalLogbook']);
        $this->assertSame(1, $stats['totalMenteeMelapor']);
        $this->assertSame(2, $stats['countDenganCatatan']);
        $this->assertSame(1, $stats['countTanpaCatatan']);
        $this->assertSame(1, $stats['countMenunggu']);
        $this->assertSame(1, $stats['countDisetujui']);
        $this->assertSame(1, $stats['countRevisi']);
        $this->assertSame(33, $stats['persenDisetujui']);
        $this->assertSame(33, $stats['persenMenunggu']);
        $this->assertSame(33, $stats['persenRevisi']);
    }

    public function test_bulk_review_mengembalikan_422_jika_tidak_ada_entri_terkirim(): void
    {
        $logbookDisetujui = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Logbook yang sudah disetujui sebelumnya.',
            'status' => StatusLogbookEnum::Disetujui,
        ]);

        $response = $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.bulk-review'), [
            'logbook_ids' => [$logbookDisetujui->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Tidak ada entri logbook terkirim yang dapat disetujui.',
        ]);
    }

    public function test_peserta_tidak_bisa_membuat_logbook_ganda_pada_minggu_atau_tanggal_yang_sama(): void
    {
        $today = Carbon::today();

        // 1. Submit entri logbook pertama untuk minggu ke-1
        $responseFirst = $this->actingAs($this->pesertaA)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 1,
            'tanggal' => $today->toDateString(),
            'kegiatan' => 'Kegiatan pertama minggu ke-1.',
        ]);
        $responseFirst->assertRedirect();
        $this->assertDatabaseCount('logbook', 1);

        // 2. Mencoba submit entri kedua dengan minggu_ke yang sama (tanggal valid lampau 1 hari)
        $responseDuplicateWeek = $this->actingAs($this->pesertaA)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 1,
            'tanggal' => $today->copy()->subDay()->toDateString(),
            'kegiatan' => 'Mencoba membuat entri kedua untuk minggu ke-1 yang sama.',
        ]);
        $responseDuplicateWeek->assertRedirect(route('peserta.logbook.index'));
        $responseDuplicateWeek->assertSessionHas('error');
        $this->assertDatabaseCount('logbook', 1);

        // 3. Mencoba submit entri dengan tanggal yang sama persis via JSON
        $responseDuplicateDate = $this->actingAs($this->pesertaA)->postJson(route('peserta.logbook.store'), [
            'minggu_ke' => 2,
            'tanggal' => $today->toDateString(),
            'kegiatan' => 'Mencoba membuat entri kedua untuk tanggal yang sama.',
        ]);
        $responseDuplicateDate->assertStatus(422);
        $this->assertDatabaseCount('logbook', 1);
    }

    public function test_riwayat_feedback_dan_revisi_logbook_tercatat_lengkap_pada_audit_trail(): void
    {
        $today = Carbon::today();

        // 1. Peserta mengisi logbook awal
        $this->actingAs($this->pesertaA)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 1,
            'tanggal' => $today->toDateString(),
            'kegiatan' => 'Penyusunan arsitektur sistem.',
        ]);

        $logbook = Logbook::where('penempatan_id', $this->penempatanA->id)->firstOrFail();

        // 2. Pembimbing memberikan catatan revisi
        $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.review', $logbook), [
            'status' => 'perlu_revisi',
            'catatan_pembimbing' => 'Diagram konteks dan DFD perlu disesuaikan dengan standar Kominfo.',
        ]);

        $logbook->refresh();
        $this->assertEquals(StatusLogbookEnum::PerluRevisi, $logbook->status);

        // 3. Peserta melakukan revisi
        $this->actingAs($this->pesertaA)->putJson(route('peserta.logbook.update', $logbook), [
            'minggu_ke' => 1,
            'tanggal' => $today->toDateString(),
            'kegiatan' => 'Penyusunan arsitektur sistem dan penyesuaian diagram DFD level 0.',
        ]);

        $logbook->refresh();
        $this->assertEquals(StatusLogbookEnum::Terkirim, $logbook->status);

        // 4. Pembimbing menyetujui logbook revisi
        $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.review', $logbook), [
            'status' => 'disetujui',
            'catatan_pembimbing' => 'Revisi diagram sudah sesuai dan disetujui.',
        ]);

        $logbook->refresh();
        $this->assertEquals(StatusLogbookEnum::Disetujui, $logbook->status);

        // 5. Verifikasi riwayat lengkap di audit trail
        $auditLogs = AuditLog::where('entitas', 'logbook')
            ->where('entitas_id', $logbook->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->assertGreaterThanOrEqual(4, $auditLogs->count());

        $aksiTypes = $auditLogs->pluck('aksi')->all();
        $this->assertContains('isi_logbook_mingguan', $aksiTypes);
        $this->assertContains('beri_catatan_logbook', $aksiTypes);
        $this->assertContains('update_logbook_mingguan', $aksiTypes);

        // Pastikan catatan pembimbing dan perubahan status tersimpan dalam audit log
        $reviewLogs = $auditLogs->where('aksi', 'beri_catatan_logbook')->values();
        $this->assertCount(2, $reviewLogs);

        // Review pertama: minta revisi
        $this->assertEquals('perlu_revisi', $reviewLogs[0]->nilai_baru['status']->value ?? $reviewLogs[0]->nilai_baru['status']);
        $this->assertEquals('Diagram konteks dan DFD perlu disesuaikan dengan standar Kominfo.', $reviewLogs[0]->nilai_baru['catatan_pembimbing']);

        // Review kedua: disetujui
        $this->assertEquals('disetujui', $reviewLogs[1]->nilai_baru['status']->value ?? $reviewLogs[1]->nilai_baru['status']);
        $this->assertEquals('Revisi diagram sudah sesuai dan disetujui.', $reviewLogs[1]->nilai_baru['catatan_pembimbing']);
    }

    public function test_bulk_review_memproses_seluruh_entri_secara_atomik_dengan_locking_order_terurut(): void
    {
        $logbook1 = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDays(5),
            'kegiatan' => 'Kegiatan 1',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $logbook2 = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 2,
            'tanggal' => Carbon::today()->subDays(4),
            'kegiatan' => 'Kegiatan 2',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $logbook3 = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'minggu_ke' => 3,
            'tanggal' => Carbon::today()->subDays(3),
            'kegiatan' => 'Kegiatan 3',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Kirim ID secara acak / terbalik untuk memverifikasi penanganan locking order
        $scrambledIds = [$logbook3->id, $logbook1->id, $logbook2->id];

        $response = $this->actingAs($this->pembimbingA)->postJson(route('pembimbing.logbook.bulk-review'), [
            'logbook_ids' => $scrambledIds,
            'catatan_pembimbing' => 'Batch approval seluruh modul mingguan.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'count' => 3,
        ]);

        $this->assertEquals(StatusLogbookEnum::Disetujui, $logbook1->fresh()->status);
        $this->assertEquals(StatusLogbookEnum::Disetujui, $logbook2->fresh()->status);
        $this->assertEquals(StatusLogbookEnum::Disetujui, $logbook3->fresh()->status);

        // Verifikasi audit log per id
        foreach ([$logbook1->id, $logbook2->id, $logbook3->id] as $lbId) {
            $this->assertDatabaseHas('audit_log', [
                'entitas' => 'logbook',
                'entitas_id' => $lbId,
                'aksi' => 'bulk_review_logbook',
            ]);
        }
    }
}
