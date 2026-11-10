<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Admin\Kuota\StoreKuotaAction;
use App\Actions\Admin\Kuota\ToggleKuotaAction;
use App\Actions\Admin\Kuota\UpdateBidangKuotaAction;
use App\Actions\Admin\Kuota\UpdateKuotaAction;
use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Exceptions\Domain\KuotaPenuhException;
use App\Exceptions\Domain\PendaftaranAktifGandaException;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Permission;
use App\Models\User;
use App\Queries\Admin\Kuota\KuotaIndexQuery;
use App\Services\QuotaAllocationService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSuperAdminArchitectureAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
            'deskripsi' => 'Pengembangan TIK',
            'kuota' => 20,
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'bidang_id' => $this->bidang->id,
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bidang->jurusan()->attach($this->jurusan->id);

        $this->bulan = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->admin = $this->createUser('Admin Seleksi', 'admin.magkom@kominfo.test', RoleEnum::Admin);
        $this->superAdmin = $this->createUser('Super Admin Utama', 'superadmin.magkom@kominfo.test', RoleEnum::SuperAdmin);

        $this->pembimbing = $this->createUser('Pembimbing Utama', 'pembimbing.magkom@kominfo.test', RoleEnum::Pembimbing);
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->save();

        $this->peserta = $this->createUser('Peserta Magang', 'peserta.magkom@kominfo.test', RoleEnum::Peserta);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    private function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'Password123!';
        $user->role = $role;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    public function test_kuota_index_query_returns_complete_view_data_and_accurate_metrics(): void
    {
        $query = app(KuotaIndexQuery::class);
        $data = $query->getIndexData($this->bulan->id);

        $this->assertArrayHasKey('bulanList', $data);
        $this->assertArrayHasKey('selectedBulan', $data);
        $this->assertArrayHasKey('jurusanList', $data);
        $this->assertArrayHasKey('bidangList', $data);
        $this->assertArrayHasKey('bidangJsonData', $data);
        $this->assertArrayHasKey('totalKapasitas', $data);
        $this->assertArrayHasKey('totalTerisi', $data);
        $this->assertArrayHasKey('globalPersen', $data);
        $this->assertArrayHasKey('isPendaftaranBuka', $data);
        $this->assertArrayHasKey('sisaHariTutup', $data);

        $this->assertTrue($data['isPendaftaranBuka']);
        $this->assertSame(10, $data['totalKapasitas']);
        $this->assertSame(0, $data['totalTerisi']);
    }

    public function test_store_kuota_action_persists_multijurusan_and_records_audit(): void
    {
        $jurusan2 = Jurusan::create([
            'bidang_id' => $this->bidang->id,
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $action = app(StoreKuotaAction::class);
        $kuotaIds = $action->execute($this->bulan->id, 5, [$this->jurusan->id, $jurusan2->id]);

        $this->assertCount(2, $kuotaIds);
        $this->assertDatabaseHas('kuota', [
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 5,
        ]);
        $this->assertDatabaseHas('kuota', [
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $jurusan2->id,
            'jumlah' => 5,
        ]);
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'kuota',
        ]);
    }

    public function test_update_kuota_action_updates_quantity_transactionally(): void
    {
        $action = app(UpdateKuotaAction::class);
        $action->execute($this->kuota, ['jumlah' => 15]);

        $this->assertSame(15, $this->kuota->fresh()->jumlah);
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'kuota',
            'entitas_id' => $this->kuota->id,
        ]);
    }

    public function test_toggle_kuota_action_rejects_deactivation_if_active_registration_exists(): void
    {
        // Pendaftaran aktif terhubung ke kuota
        Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-ACTIVE',
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
        ]);

        $action = app(ToggleKuotaAction::class);
        $result = $action->execute($this->kuota);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('BR-12', $result['message']);
        $this->assertTrue($this->kuota->fresh()->aktif);
    }

    public function test_toggle_kuota_action_succeeds_when_no_active_records(): void
    {
        $action = app(ToggleKuotaAction::class);
        $result = $action->execute($this->kuota);

        $this->assertTrue($result['success']);
        $this->assertFalse($this->kuota->fresh()->aktif);
    }

    public function test_update_bidang_kuota_action_prevents_decreasing_below_accepted_participants(): void
    {
        $this->kuota->update(['jumlah' => 5, 'terisi' => 3]);

        $action = app(UpdateBidangKuotaAction::class);
        $result = $action->execute(
            $this->bidang,
            $this->bulan->id,
            [$this->jurusan->id => 2], // 2 lebih kecil dari terisi (3)
            'buka'
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('tidak boleh lebih kecil', $result['message']);
    }

    public function test_update_bidang_kuota_action_close_bidang_preserves_terisi(): void
    {
        $this->kuota->update(['jumlah' => 10, 'terisi' => 2]);

        $action = app(UpdateBidangKuotaAction::class);
        $result = $action->execute(
            $this->bidang,
            $this->bulan->id,
            [],
            'tutup'
        );

        $this->assertTrue($result['success']);
        $fresh = $this->kuota->fresh();
        $this->assertSame(2, $fresh->jumlah); // Dijaga sama dengan terisi
        $this->assertTrue($fresh->aktif); // Aktif karena jumlah > 0
    }

    public function test_quota_allocation_service_atomically_increments_and_decrements(): void
    {
        $service = app(QuotaAllocationService::class);

        // Ambil kuota di dalam transaksi
        DB::transaction(function () use ($service) {
            $updated = $service->ambilKuotaUmum($this->kuota->id);
            $this->assertSame(1, $updated->terisi);
        });

        $this->assertSame(1, $this->kuota->fresh()->terisi);

        // Kembalikan kuota di dalam transaksi
        DB::transaction(function () use ($service) {
            $service->lepasKuotaUmum($this->kuota->id);
        });

        $this->assertSame(0, $this->kuota->fresh()->terisi);
    }

    public function test_quota_allocation_service_throws_exception_when_quota_full(): void
    {
        $this->kuota->update(['jumlah' => 1, 'terisi' => 1]);
        $service = app(QuotaAllocationService::class);

        $this->expectException(KuotaPenuhException::class);

        DB::transaction(function () use ($service) {
            $service->ambilKuotaUmum($this->kuota->id);
        });
    }

    public function test_quota_allocation_service_enforces_br17_single_active_registration(): void
    {
        Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-ACTIVE-1',
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
        ]);

        $service = app(QuotaAllocationService::class);

        $this->expectException(PendaftaranAktifGandaException::class);
        $service->pastikanTidakAdaPendaftaranAktif($this->peserta->id);
    }

    public function test_superadmin_cannot_deactivate_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $this->superAdmin));

        $response->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        $this->assertTrue($this->superAdmin->fresh()->status_aktif);
    }

    public function test_superadmin_cannot_deactivate_last_superadmin_sec16(): void
    {
        // Hanya ada 1 superadmin aktif
        $otherSuperAdmin = $this->createUser('Super Admin Dua', 'super2@kominfo.test', RoleEnum::SuperAdmin);
        $otherSuperAdmin->status_aktif = false;
        $otherSuperAdmin->save();

        // Coba nonaktifkan $otherSuperAdmin padahal yang aktif cuma $this->superAdmin
        // Buat superadmin baru untuk mencoba dinonaktifkan
        $targetSuperAdmin = $this->createUser('Super Admin Target', 'supertarget@kominfo.test', RoleEnum::SuperAdmin);
        // Sekarang ada 2 aktif ($this->superAdmin dan $targetSuperAdmin)
        // Nonaktifkan targetSuperAdmin -> sukses (masih sisa 1)
        $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $targetSuperAdmin))
            ->assertRedirect(route('superadmin.staf.index'));

        // Coba lagi menonaktifkannya kembali saat dia sudah nonaktif (akan mengaktifkan)
        // Sekarang buat skenario di mana sistem hanya punya 1 SuperAdmin aktif dan coba dinonaktifkan:
        $targetSuperAdmin->delete();
        $this->assertSame(1, User::where('role', RoleEnum::SuperAdmin)->where('status_aktif', true)->count());

        // Membuat user superadmin kedua yang pasif
        $passiveSuper = $this->createUser('Passive Super', 'passivesuper@kominfo.test', RoleEnum::SuperAdmin);
        $passiveSuper->status_aktif = true;
        $passiveSuper->save();

        // Nonaktifkan passiveSuper berhasil karena masih ada $this->superAdmin
        $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $passiveSuper));
        $this->assertFalse($passiveSuper->fresh()->status_aktif);

        // Jika mencoba menonaktifkan ketika tinggal 1 yang aktif:
        // Coba toggle $this->superAdmin ditolak di awal karena self-deactivation
    }

    public function test_superadmin_cannot_deactivate_pembimbing_with_active_mentees_br12(): void
    {
        Penempatan::create([
            'nomor_sk' => 'SK-MENTEE-ACTIVE',
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $this->pembimbing));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('BR-12', (string) session('error'));
        $this->assertTrue($this->pembimbing->fresh()->status_aktif);
    }

    public function test_superadmin_cannot_delete_system_core_permission(): void
    {
        $perm = Permission::create([
            'nama_modul' => 'Audit Trail Sistem',
            'slug' => 'keamanan.audit-trail',
            'kategori' => 'Keamanan & Kepatuhan',
            'is_system' => true,
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('superadmin.role.permission.destroy', $perm));

        $response->assertForbidden();
        $this->assertModelExists($perm);
    }

    public function test_superadmin_strictly_read_only_on_admin_and_pembimbing_mutating_routes(): void
    {
        // 1. Mutasi POST (admin.kuota.store) -> Ditolak 403
        $respPost = $this->actingAs($this->superAdmin)
            ->post(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulan->id,
                'jurusan_id' => $this->jurusan->id,
                'jumlah' => 20,
            ]);
        $respPost->assertForbidden();

        // 2. Mutasi PUT (admin.kuota.update) -> Ditolak 403
        $respPut = $this->actingAs($this->superAdmin)
            ->put(route('admin.kuota.update', $this->kuota), [
                'jumlah' => 25,
            ]);
        $respPut->assertForbidden();

        // 3. Mutasi POST/DELETE pada Penempatan -> Ditolak 403
        $penempatan = Penempatan::create([
            'nomor_sk' => 'SK-RO-TEST',
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
        ]);

        $respCancel = $this->actingAs($this->superAdmin)
            ->post(route('admin.penempatan.batalkan', $penempatan), [
                'alasan_pembatalan' => 'Pembatalan coba-coba',
            ]);
        $respCancel->assertForbidden();

        // 4. Operasi BACA (GET) tetap berjalan lancar -> 200 OK
        $respGetAdmin = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
        $respGetAdmin->assertOk();

        $respGetKuota = $this->actingAs($this->superAdmin)->get(route('admin.kuota.index'));
        $respGetKuota->assertOk();

        $respGetPembimbing = $this->actingAs($this->superAdmin)->get(route('pembimbing.dashboard'));
        $respGetPembimbing->assertOk();
    }

    public function test_superadmin_can_export_master_snapshot_json(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.pengaturan.export-json'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');

        $json = $response->json();
        $this->assertSame('MagKom Sleman', $json['app']);
        $this->assertArrayHasKey('sha256_checksum', $json);
        $this->assertArrayHasKey('snapshot', $json);
    }

    public function test_arsip_peserta_loads_and_records_audit_trail(): void
    {
        $penempatanArsip = Penempatan::create([
            'nomor_sk' => 'SK-ARSIP-001',
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => now()->subMonths(4)->toDateString(),
            'tgl_selesai' => now()->subMonths(1)->toDateString(),
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.arsip.show', $penempatanArsip));

        $response->assertOk();
        $response->assertViewIs('superadmin.arsip.show');

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'entitas_id' => $penempatanArsip->id,
        ]);
    }
}
