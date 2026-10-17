<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasTugasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\TugasTemplate;
use App\Models\User;
use App\Support\Pengaturan;
use Carbon\Carbon;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdvancedRoleEnhancementPhaseSixTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing1;
    protected User $pembimbing2;
    protected User $pesertaUser;
    protected BulanMagang $bulan;
    protected Jurusan $jurusan;
    protected Kuota $kuota;
    protected Bidang $bidang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RubrikSeeder::class);
        $this->seed(JenisDokumenSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & Persandian',
            'deskripsi' => 'Bidang Aptika Diskominfo Sleman',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'pt',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => Carbon::parse('2026-11-01'),
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
        ]);

        $this->superAdmin = $this->createTestUser('SuperAdmin MagKom', 'superadmin@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->admin = $this->createTestUser('Admin Operasional', 'admin@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing1 = $this->createTestUser('Budi Pembimbing', 'pembimbing1@slemankab.go.id', RoleEnum::Pembimbing, bidangId: $this->bidang->id);
        $this->pembimbing2 = $this->createTestUser('Dewi Pembimbing', 'pembimbing2@slemankab.go.id', RoleEnum::Pembimbing, bidangId: $this->bidang->id);
        $this->pesertaUser = $this->createTestUser('Ahmad Peserta', 'ahmad@universitas.ac.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->pesertaUser->id,
            'nim_nisn' => '22010199',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);
    }

    protected function createTestUser(string $nama, string $email, RoleEnum $role, bool $statusAktif = true, ?int $bidangId = null): User
    {
        $u = new User();
        $u->nama = $nama;
        $u->email = $email;
        $u->password = Hash::make('Password123!');
        $u->role = $role;
        $u->status_aktif = $statusAktif;
        $u->must_change_password = false;
        $u->email_verified_at = now();
        $u->bidang_id = $bidangId;
        $u->save();

        return $u;
    }

    // ==========================================
    // 1. PENGUJIAN PILAR PEMBIMBING LAPANGAN ASN
    // ==========================================

    public function test_pembimbing_can_manage_tugas_templates_and_fetch_picker_json(): void
    {
        // 1. Akses halaman bank template
        $response = $this->actingAs($this->pembimbing1)->get(route('pembimbing.tugas.template.index'));
        $response->assertStatus(200);
        $response->assertSee('Bank Template Penugasan');

        // 2. Simpan template baru
        $storeResponse = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.template.store'), [
            'judul' => 'Analisis Kebutuhan Sistem e-Government',
            'deskripsi' => 'Lakukan identifikasi dan buat diagram use case arsitektur SPBE.',
            'kategori' => 'Analisis Sistem',
            'kompleksitas' => 'sedang',
            'estimasi_hari' => 5,
            'link_referensi' => 'https://layanan.slemankab.go.id/docs',
            'is_global' => false,
        ]);
        $storeResponse->assertRedirect(route('pembimbing.tugas.template.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('tugas_template', [
            'judul' => 'Analisis Kebutuhan Sistem e-Government',
            'pembimbing_id' => $this->pembimbing1->id,
            'is_global' => false,
        ]);

        $template = TugasTemplate::where('judul', 'Analisis Kebutuhan Sistem e-Government')->firstOrFail();

        // 3. Ambil picker json
        $pickerResponse = $this->actingAs($this->pembimbing1)->getJson(route('pembimbing.tugas.template.picker'));
        $pickerResponse->assertStatus(200);
        $pickerResponse->assertJsonFragment([
            'id' => $template->id,
            'judul' => 'Analisis Kebutuhan Sistem e-Government',
        ]);

        // 4. Pembimbing lain tidak dapat melihat template privat milik pembimbing 1
        $picker2Response = $this->actingAs($this->pembimbing2)->getJson(route('pembimbing.tugas.template.picker'));
        $picker2Response->assertStatus(200);
        $picker2Response->assertJsonMissing([
            'id' => $template->id,
        ]);

        // 5. Hapus template oleh pemiliknya
        $deleteResponse = $this->actingAs($this->pembimbing1)->delete(route('pembimbing.tugas.template.destroy', $template));
        $deleteResponse->assertRedirect(route('pembimbing.tugas.template.index'));
        $this->assertDatabaseMissing('tugas_template', ['id' => $template->id]);
    }

    public function test_pembimbing_peserta_filter_tab_perhatian_identifies_inactive_and_overdue_mentees(): void
    {
        // Siapkan penempatan aktif
        $penempatan = Penempatan::create([
            'peserta_id' => $this->pesertaUser->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => Carbon::now()->subDays(10),
            'tgl_selesai' => Carbon::now()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Berikan tugas yang sudah lewat tenggat (overdue)
        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Pembuatan Wireframe Dashboard Admin',
            'deskripsi' => 'Selesaikan wireframe sampai halaman audit trail.',
            'deadline' => Carbon::now()->subDays(2), // Overdue
            'status' => StatusTugasEnum::Belum,
            'kategori' => 'UI/UX',
            'kompleksitas' => \App\Enums\KompleksitasEnum::Sedang,
        ]);

        // Akses tab 'perhatian'
        $response = $this->actingAs($this->pembimbing1)->get(route('pembimbing.peserta.index', ['tab' => 'perhatian']));
        $response->assertStatus(200);
        $response->assertSee('Ahmad Peserta');
        $response->assertSee('Perlu Perhatian');
    }

    public function test_pembimbing_laporan_kinerja_mentoring_renders_and_prints(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->pesertaUser->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => Carbon::now()->subDays(15),
            'tgl_selesai' => Carbon::now()->addDays(15),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Tambah logbook yang disetujui
        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => Carbon::yesterday(),
            'kegiatan' => 'Membantu instalasi server database di Sleman Smart Room',
            'status' => StatusLogbookEnum::Disetujui,
            'catatan_pembimbing' => 'Kerja bagus dan teliti.',
            'direviu_pada' => Carbon::now(),
        ]);

        // 1. Akses index laporan kinerja
        $response = $this->actingAs($this->pembimbing1)->get(route('pembimbing.laporan-kinerja.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Kinerja Mentoring ASN');
        $response->assertSee('Budi Pembimbing');

        // 2. Akses halaman cetak
        $printResponse = $this->actingAs($this->pembimbing1)->get(route('pembimbing.laporan-kinerja.cetak'));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Lembar Rekapitulasi Pembimbingan Magang');
        $printResponse->assertSee('Ahmad Peserta');
    }

    // ==========================================
    // 2. PENGUJIAN PILAR ADMINISTRASI
    // ==========================================

    public function test_admin_one_click_fifo_promote_waiting_list(): void
    {
        // Buat pendaftaran berstatus WaitingList
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaUser->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::WaitingList,
            'diajukan_at' => Carbon::now()->subDays(1),
        ]);

        // Kuota masih tersedia (10 tersisa)
        $this->assertEquals(0, $this->kuota->fresh()->terisi);

        // Eksekusi promote FIFO
        $response = $this->actingAs($this->admin)->post(route('admin.waiting-list.promote'), [
            'pendaftaran_id' => $pendaftaran->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'catatan' => 'Dipromosikan dari waiting list via automated FIFO',
        ]);

        $response->assertRedirect(route('admin.waiting-list.index'));
        $response->assertSessionHas('success');

        // Verifikasi pendaftaran menjadi Diterima dan penempatan terbentuk
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaran->fresh()->status);
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $this->pesertaUser->id,
            'status' => StatusPenempatanEnum::Aktif->value,
            'pembimbing_id' => $this->pembimbing1->id,
        ]);
        $this->assertEquals(1, $this->kuota->fresh()->terisi);
    }

    public function test_admin_download_bundel_zip_registration_documents(): void
    {
        Storage::fake('private');

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaUser->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => Carbon::now(),
        ]);

        $jenisDokumen = JenisDokumen::firstOrFail();
        $dummyPath = 'dokumen/pendaftaran/sample_cv.pdf';
        Storage::disk('private')->put($dummyPath, 'SAMPLE PDF CONTENT FOR TESTING');

        Dokumen::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => $jenisDokumen->id,
            'nama_asli' => 'cv_ahmad.pdf',
            'path' => $dummyPath,
            'ukuran' => 1024,
            'mime' => 'application/pdf',
            'hash' => sha1('sample_cv'),
        ]);

        // Unduh ZIP batch
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.download-zip', [
            'bulan_magang_id' => $this->bulan->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/zip');
    }

    public function test_admin_bulk_reassign_pembimbing_with_capacity_validation(): void
    {
        // Buat penempatan awal dengan pembimbing 1
        $penempatan = Penempatan::create([
            'peserta_id' => $this->pesertaUser->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => Carbon::now(),
            'tgl_selesai' => Carbon::now()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Pindahkan ke pembimbing 2
        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.bulk-reassign'), [
            'penempatan_ids' => [$penempatan->id],
            'pembimbing_id' => $this->pembimbing2->id,
            'alasan_reassign' => 'Pemerataan beban bimbingan staf dinas',
        ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        $this->assertEquals($this->pembimbing2->id, $penempatan->fresh()->pembimbing_id);
    }

    // ==========================================
    // 3. PENGUJIAN PILAR SUPERADMIN
    // ==========================================

    public function test_superadmin_staf_must_change_password_filter_and_regenerate(): void
    {
        // 1. Buat staf dengan status must_change_password = true
        $staf = $this->createTestUser('Staf Baru Magang', 'stafbaru@slemankab.go.id', RoleEnum::Admin);
        $staf->must_change_password = true;
        $staf->save();

        // Akses daftar staf dengan filter
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.staf.index', ['must_change_password' => '1']));
        $response->assertStatus(200);
        $response->assertSee('stafbaru@slemankab.go.id');
        $response->assertSee('Wajib Ganti Sandi');

        // 2. Regenerate temporary password
        $regenResponse = $this->actingAs($this->superAdmin)->post(route('superadmin.staf.regenerate-password', $staf));
        $regenResponse->assertRedirect(route('superadmin.staf.index'));
        $regenResponse->assertSessionHas('success');

        $this->assertTrue($staf->fresh()->must_change_password);
    }

    public function test_superadmin_master_data_json_snapshot_export(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.pengaturan.export-json'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('app', $content);
        $this->assertArrayHasKey('sha256_checksum', $content);
        $this->assertArrayHasKey('snapshot', $content);
        $this->assertArrayHasKey('pengaturan', $content['snapshot']);
        $this->assertArrayHasKey('jurusan', $content['snapshot']);
        $this->assertArrayHasKey('bidang', $content['snapshot']);
        $this->assertArrayHasKey('rubrik', $content['snapshot']);
    }

    public function test_superadmin_security_sweep_command_and_telemetry_action(): void
    {
        // Buat token reset kadaluwarsa (> 60 menit)
        DB::table('password_reset_tokens')->insert([
            'email' => 'expired_user@example.com',
            'token' => 'expired_dummy_token_hash',
            'created_at' => Carbon::now()->subMinutes(120),
        ]);

        // Buat calon pendaftar unverified kadaluwarsa (> 72 jam tanpa pengajuan)
        $stalePendaftar = new User();
        $stalePendaftar->nama = 'Pendaftar Stale';
        $stalePendaftar->email = 'stale@example.com';
        $stalePendaftar->password = Hash::make('Password123!');
        $stalePendaftar->role = RoleEnum::Peserta;
        $stalePendaftar->status_aktif = true;
        $stalePendaftar->email_verified_at = null;
        $stalePendaftar->created_at = Carbon::now()->subHours(96);
        $stalePendaftar->save();

        // 1. Uji via Artisan command
        $this->artisan('magkom:security-sweep --dry-run')
            ->expectsOutputToContain('DRY RUN')
            ->assertExitCode(0);

        // Verifikasi entri belum terhapus karena dry run
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'expired_user@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'stale@example.com']);

        // 2. Eksekusi via endpoint controller superadmin
        $sweepResponse = $this->actingAs($this->superAdmin)->post(route('superadmin.health.security-sweep'));
        $sweepResponse->assertSessionHas('status_keberhasilan');

        // Verifikasi entri telah bersih
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'expired_user@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'stale@example.com']);

        // Verifikasi audit log trail tercatat
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'system',
        ]);
    }
}
