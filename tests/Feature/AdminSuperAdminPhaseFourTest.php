<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Services\ExportReportService;
use App\Services\PlacementService;
use Carbon\Carbon;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminSuperAdminPhaseFourTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Bidang $bidang;

    protected function createTestUser(string $nama, string $email, RoleEnum $role, bool $statusAktif = true): User
    {
        $u = new User;
        $u->nama = $nama;
        $u->email = $email;
        $u->password = 'Password123!';
        $u->role = $role;
        $u->status_aktif = $statusAktif;
        $u->must_change_password = false;
        $u->email_verified_at = now();
        $u->save();

        return $u;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
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
            'jumlah' => 5,
            'terisi' => 0,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Pengembangan sistem informasi dan website',
        ]);

        $this->superAdmin = $this->createTestUser('Super Administrator', 'superadmin@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->admin = $this->createTestUser('Admin Verifikator', 'admin@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing = $this->createTestUser('Pembimbing Lapangan', 'pembimbing@slemankab.go.id', RoleEnum::Pembimbing);
        $this->peserta = $this->createTestUser('Peserta UGM', 'peserta@example.com', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, DIY',
        ]);
    }

    /**
     * Modul 1: Validasi batas jadwal bulan magang (Bab 8.2: tgl_buka < tgl_tutup <= tgl 1 bulan mulai).
     */
    public function test_validasi_bulan_magang_menolak_tgl_tutup_melebihi_tanggal_1_bulan_mulai(): void
    {
        // Periode magang: Desember 2026 (2026-12-01). Batas tgl_tutup maksimal 2026-12-01.
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.bulan.store'), [
                'bulan_mulai' => '2026-12',
                'tgl_buka' => '2026-11-01',
                'tgl_tutup' => '2026-12-05', // INVALID: melebihi 2026-12-01
                'aktif' => '1',
            ]);

        $response->assertSessionHasErrors('tgl_tutup');
    }

    public function test_validasi_bulan_magang_menolak_tgl_buka_setelah_tgl_tutup(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.bulan.store'), [
                'bulan_mulai' => '2026-12',
                'tgl_buka' => '2026-11-20',
                'tgl_tutup' => '2026-11-10', // INVALID: tgl_tutup sebelum tgl_buka
                'aktif' => '1',
            ]);

        $response->assertSessionHasErrors(['tgl_buka', 'tgl_tutup']);
    }

    public function test_validasi_bulan_magang_menerima_jadwal_yang_sesuai_aturan_bab_8_2(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.bulan.store'), [
                'bulan_mulai' => '2026-12',
                'tgl_buka' => '2026-11-01',
                'tgl_tutup' => '2026-12-01', // VALID: tepat tanggal 1 bulan mulai
                'aktif' => '1',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bulan_magang', [
            'bulan_mulai' => '2026-12-01 00:00:00',
            'tgl_buka' => '2026-11-01 00:00:00',
            'tgl_tutup' => '2026-12-01 00:00:00',
        ]);
    }

    /**
     * Modul 2: Penanganan status mengundurkan_diri pada penempatan dan arsip peserta (AC-07-5, BR-07, AC-12-4).
     */
    public function test_arsip_peserta_superadmin_menampilkan_peserta_mengundurkan_diri(): void
    {
        $pesertaMundur = $this->createTestUser('Budi Pengunduran', 'budi.mundur@example.com', RoleEnum::Peserta, false);

        PesertaProfil::create([
            'user_id' => $pesertaMundur->id,
            'nim_nisn' => '99887766',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081299998888',
            'alamat' => 'Sleman',
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $pesertaMundur->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-09-01',
            'tgl_selesai' => '2026-11-30',
            'status' => StatusPenempatanEnum::MengundurkanDiri,
        ]);

        // Cek halaman index arsip peserta
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.arsip.index'));

        $response->assertOk();
        $response->assertSee('Budi Pengunduran');
        $response->assertSee('Mengundurkan Diri');

        // Cek filter status mengundurkan_diri
        $responseFilter = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.arsip.index', ['status' => 'mengundurkan_diri']));

        $responseFilter->assertOk();
        $responseFilter->assertSee('Budi Pengunduran');
    }

    /**
     * Modul 3: Urutan FIFO antrean verifikasi (diajukan_at asc) & PlacementService@terima (AC-04-1, BR-05).
     */
    public function test_antrean_verifikasi_admin_mengurutkan_fifo_berdasarkan_diajukan_at(): void
    {
        $user1 = $this->createTestUser('Pendaftar Pertama', 'pendaftar1@example.com', RoleEnum::Peserta);
        $p1 = Pendaftaran::create([
            'user_id' => $user1->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => Carbon::parse('2026-10-02 08:00:00'),
        ]);
        $p1->created_at = Carbon::parse('2026-10-02 08:00:00');
        $p1->saveQuietly();

        $user2 = $this->createTestUser('Pendaftar Kedua', 'pendaftar2@example.com', RoleEnum::Peserta);
        $p2 = Pendaftaran::create([
            'user_id' => $user2->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => Carbon::parse('2026-10-05 10:00:00'),
        ]);
        $p2->created_at = Carbon::parse('2026-10-05 10:00:00');
        $p2->saveQuietly();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index'));

        $response->assertOk();
        $list = $response->viewData('pendaftaranList');

        // Pendaftar pertama harus muncul di urutan pertama (FIFO)
        $this->assertEquals($p1->id, $list->items()[0]->id);
        $this->assertEquals($p2->id, $list->items()[1]->id);

        // Jika user memilih urutan terbaru, Pendaftar kedua muncul di urutan pertama
        $responseTerbaru = $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index', ['sort' => 'terbaru']));
        $listTerbaru = $responseTerbaru->viewData('pendaftaranList');
        $this->assertEquals($p2->id, $listTerbaru->items()[0]->id);
    }

    public function test_placement_service_terima_mencatat_status_lama_secara_akurat_pada_audit_trail(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::WaitingList,
            'diajukan_at' => now(),
        ]);

        /** @var PlacementService $placementService */
        $placementService = app(PlacementService::class);
        $penempatan = $placementService->terima(
            pendaftaran: $pendaftaran,
            admin: $this->admin,
            pembimbing: $this->pembimbing,
            bidang: $this->bidang
        );

        $this->assertInstanceOf(Penempatan::class, $penempatan);
        $this->assertEquals(StatusPenempatanEnum::Aktif, $penempatan->status);

        // Periksa jejak audit
        $audit = AuditLog::where('entitas', 'pendaftaran')
            ->where('entitas_id', $pendaftaran->id)
            ->where('tipe', TipeAuditEnum::Aksi)
            ->latest('id')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals(['status' => 'waiting_list'], $audit->nilai_lama);
        $this->assertEquals(['status' => 'diterima'], $audit->nilai_baru);
    }

    /**
     * Modul 4: Audit trail akses data ekspor laporan & UTF-8 BOM CSV (AC-13-4, AC-17-1, AC-17-2).
     */
    public function test_ekspor_csv_menyertakan_utf8_bom_dan_mencatat_audit_akses_data(): void
    {
        /** @var ExportReportService $exportService */
        $exportService = app(ExportReportService::class);

        $response = $exportService->exportPendaftaran($this->admin, []);

        // Tangkap isi konten stream
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Verifikasi UTF-8 BOM (\xEF\xBB\xBF) di awal berkas
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csvContent);

        // Verifikasi jejak audit tipe AksesData
        $audit = AuditLog::where('entitas', 'pendaftaran')
            ->where('tipe', TipeAuditEnum::AksesData)
            ->latest('id')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals('Akses Pembacaan Data', $audit->tipe->label());
    }

    /**
     * Modul 5: Filter audit trail SuperAdmin dengan dropdown staf internal (AC-13-6).
     */
    public function test_filter_audit_log_superadmin_merender_dropdown_staf_internal_dan_memfilter_user(): void
    {
        // Buat rekaman audit log
        AuditLog::create([
            'request_id' => (string) Str::uuid(),
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'test.aksi',
            'entitas' => 'kuota',
            'entitas_id' => $this->kuota->id,
            'user_id' => $this->admin->id,
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'waktu' => now(),
            'detail' => ['test' => 'data'],
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.audit-log.index'));

        $response->assertOk();
        $response->assertSee('Aktor / Staf');
        $response->assertSee($this->admin->nama);
        $response->assertSee($this->superAdmin->nama);

        // Filter berdasarkan admin_id
        $responseFiltered = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.audit-log.index', ['user_id' => $this->admin->id]));

        $responseFiltered->assertOk();
        $logs = $responseFiltered->viewData('logs');
        $this->assertTrue($logs->getCollection()->every(fn ($log) => $log->user_id === $this->admin->id));
    }
}
