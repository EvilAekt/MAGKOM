<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\PlacementService;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPenempatanArchitectureAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing1;

    protected User $pembimbing2;

    protected User $peserta;

    protected User $pesertaLain;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

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

        $this->admin = $this->createTestUser('Admin Pengelola', 'admin.magang@slemankab.go.id', RoleEnum::Admin);
        $this->superAdmin = $this->createTestUser('SuperAdmin Monitor', 'superadmin@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->pembimbing1 = $this->createTestUser('Pembimbing Satu', 'pembimbing1@slemankab.go.id', RoleEnum::Pembimbing);
        $this->pembimbing2 = $this->createTestUser('Pembimbing Dua', 'pembimbing2@slemankab.go.id', RoleEnum::Pembimbing);
        $this->peserta = $this->createTestUser('Peserta Utama', 'peserta@gmail.com', RoleEnum::Peserta);
        $this->pesertaLain = $this->createTestUser('Peserta Lain', 'peserta.lain@gmail.com', RoleEnum::Peserta);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
            'bidang_id' => $this->bidang->id,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 1,
            'aktif' => true,
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);
    }

    public function test_superadmin_is_strictly_read_only_and_forbidden_from_mutating_penempatan(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
            'diputuskan_at' => now(),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(5)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // SuperAdmin attempts tetapkan -> 403 Forbidden (SEC-12)
        $this->actingAs($this->superAdmin)
            ->post(route('admin.penempatan.tetapkan'), [
                'pendaftaran_id' => $pendaftaran->id,
                'pembimbing_id' => $this->pembimbing1->id,
            ])
            ->assertForbidden();

        // SuperAdmin attempts pindah pembimbing -> 403 Forbidden
        $this->actingAs($this->superAdmin)
            ->post(route('admin.penempatan.pindah-pembimbing', $penempatan), [
                'pembimbing_id' => $this->pembimbing2->id,
            ])
            ->assertForbidden();

        // SuperAdmin attempts bulk reassign -> 403 Forbidden
        $this->actingAs($this->superAdmin)
            ->post(route('admin.penempatan.bulk-reassign'), [
                'penempatan_ids' => [$penempatan->id],
                'pembimbing_id' => $this->pembimbing2->id,
            ])
            ->assertForbidden();

        // SuperAdmin attempts batalkan penempatan -> 403 Forbidden
        $this->actingAs($this->superAdmin)
            ->post(route('admin.penempatan.batalkan', $penempatan), [
                'alasan' => 'Alasan pembatalan oleh superadmin yang tidak sah',
            ])
            ->assertForbidden();
    }

    public function test_non_admin_roles_are_forbidden_from_mutating_penempatan(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(5)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Pembimbing cannot mutate penempatan
        $this->actingAs($this->pembimbing1)
            ->post(route('admin.penempatan.pindah-pembimbing', $penempatan), [
                'pembimbing_id' => $this->pembimbing2->id,
            ])
            ->assertForbidden();

        // Peserta cannot mutate penempatan
        $this->actingAs($this->peserta)
            ->post(route('admin.penempatan.batalkan', $penempatan), [
                'alasan' => 'Alasan pengunduran diri mandiri',
            ])
            ->assertForbidden();
    }

    public function test_bulk_reassign_pembimbing_successfully_updates_active_penempatan_and_records_audit(): void
    {
        $p1 = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $p2 = Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.bulk-reassign'), [
                'penempatan_ids' => [$p1->id, $p2->id],
                'pembimbing_id' => $this->pembimbing2->id,
            ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        $this->assertEquals($this->pembimbing2->id, $p1->fresh()->pembimbing_id);
        $this->assertEquals($this->pembimbing2->id, $p2->fresh()->pembimbing_id);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'entitas_id' => $p1->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_bulk_reassign_pembimbing_ignores_historical_finished_or_cancelled_penempatan(): void
    {
        $activePenempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subMonths(4)->toDateString(),
            'tgl_selesai' => now()->subMonth()->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $finishedPenempatan = Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subMonths(6)->toDateString(),
            'tgl_selesai' => now()->subMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Selesai,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.penempatan.bulk-reassign'), [
                'penempatan_ids' => [$activePenempatan->id, $finishedPenempatan->id],
                'pembimbing_id' => $this->pembimbing2->id,
            ]);

        // Active penempatan updated
        $this->assertEquals($this->pembimbing2->id, $activePenempatan->fresh()->pembimbing_id);

        // Finished historical penempatan remains intact!
        $this->assertEquals($this->pembimbing1->id, $finishedPenempatan->fresh()->pembimbing_id);
    }

    public function test_bulk_reassign_pembimbing_rejects_when_mentor_capacity_is_exceeded(): void
    {
        Pengaturan::updateOrCreate(
            ['kunci' => 'maks_peserta_aktif_per_pembimbing'],
            ['nilai' => '1', 'tipe' => 'integer']
        );

        // Pembimbing 2 already has 1 active mentee
        Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing2->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $p1 = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.bulk-reassign'), [
                'penempatan_ids' => [$p1->id],
                'pembimbing_id' => $this->pembimbing2->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals($this->pembimbing1->id, $p1->fresh()->pembimbing_id);
    }

    public function test_tempatkan_manual_prevents_duplicate_active_placement_for_same_user(): void
    {
        $slotKhusus = SlotKhusus::create([
            'nama' => 'Slot Khusus Testing',
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        // Create an existing active placement
        Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'slot_khusus_id' => $slotKhusus->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $placementService = app(PlacementService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Peserta ini sudah memiliki data penempatan aktif.');

        $placementService->tempatkanManual(
            peserta: $this->peserta,
            slotKhusus: $slotKhusus,
            pembimbing: $this->pembimbing2,
            bidang: $this->bidang,
            bulanMulai: now()->addMonth()->startOfMonth()->toDateString(),
            durasiBulan: 3,
            admin: $this->admin
        );
    }

    public function test_batalkan_penempatan_before_start_date_releases_quota_safely_and_cancels_pendaftaran(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
            'diputuskan_at' => now(),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(10)->toDateString(), // Future start date
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->assertEquals(1, $this->kuota->fresh()->terisi);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.batalkan', $penempatan), [
                'alasan' => 'Peserta berhalangan mengikuti magang karena urusan studi mendadak.',
            ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        // Quota released safely to 0
        $this->assertEquals(0, $this->kuota->fresh()->terisi);

        // Pendaftaran status marked Dibatalkan
        $this->assertEquals(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->fresh()->status);

        // Penempatan status marked MengundurkanDiri
        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $penempatan->fresh()->status);

        // User account remains active
        $this->assertTrue($this->peserta->fresh()->status_aktif);
    }

    public function test_batalkan_penempatan_after_start_date_deactivates_user_and_burns_quota(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subDays(5)->toDateString(), // Past start date
            'tgl_selesai' => now()->addMonths(2)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.batalkan', $penempatan), [
                'alasan' => 'Peserta mengundurkan diri setelah magang berjalan karena diterima kerja.',
            ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        // Quota not returned (hangus)
        $this->assertEquals(1, $this->kuota->fresh()->terisi);

        // User account deactivated
        $this->assertFalse($this->peserta->fresh()->status_aktif);

        // Penempatan status marked MengundurkanDiri
        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $penempatan->fresh()->status);
    }

    public function test_surat_diterima_download_and_stream_authorized_for_admin_and_own_peserta_but_not_other_peserta(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(5)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Admin can download & preview
        $this->actingAs($this->admin)
            ->get(route('admin.penempatan.surat-diterima', $penempatan))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.penempatan.surat-diterima.pratinjau', $penempatan))
            ->assertOk();

        // Own Peserta can download & preview via peserta route
        $this->actingAs($this->peserta)
            ->get(route('peserta.penempatan.surat-diterima', $penempatan))
            ->assertOk();

        $this->actingAs($this->peserta)
            ->get(route('peserta.penempatan.surat-diterima.pratinjau', $penempatan))
            ->assertOk();

        // Other unrelated Peserta receives 403 Forbidden (prevent IDOR)
        $this->actingAs($this->pesertaLain)
            ->get(route('peserta.penempatan.surat-diterima', $penempatan))
            ->assertForbidden();

        $this->actingAs($this->pesertaLain)
            ->get(route('peserta.penempatan.surat-diterima.pratinjau', $penempatan))
            ->assertForbidden();
    }

    public function test_assign_peserta_online_locks_parent_user_and_prevents_duplicate_active_placement(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
            'diputuskan_at' => now(),
        ]);

        $action = app(\App\Actions\Admin\Penempatan\AssignPesertaAction::class);

        // First assignment succeeds
        $penempatan = $action->execute($pendaftaran, $this->admin, $this->pembimbing1, $this->bidang);
        $this->assertNotNull($penempatan->id);
        $this->assertEquals(StatusPenempatanEnum::Aktif, $penempatan->status);
        $this->assertEquals($this->peserta->id, $penempatan->peserta_id);

        // Second assignment with same pendaftaran fails
        $this->expectException(\App\Exceptions\Domain\StatusSudahBerubahException::class);
        $this->expectExceptionMessage('Peserta ini sudah memiliki data penempatan aktif.');

        $action->execute($pendaftaran, $this->admin, $this->pembimbing2, $this->bidang);
    }

    public function test_cross_channel_double_assignment_online_and_manual_is_strictly_prevented(): void
    {
        $slotKhusus = SlotKhusus::create([
            'nama' => 'Slot Khusus Testing',
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
            'diputuskan_at' => now(),
        ]);

        $assignAction = app(\App\Actions\Admin\Penempatan\AssignPesertaAction::class);
        $manualAction = app(\App\Actions\Admin\Penempatan\CreateManualPenempatanAction::class);

        // 1. Assign online first
        $assignAction->execute($pendaftaran, $this->admin, $this->pembimbing1, $this->bidang);

        // 2. Try manual placement for the same participant -> must be rejected
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Peserta ini sudah memiliki data penempatan aktif.');

        $manualAction->executeTerdaftar(
            peserta: $this->peserta,
            slotKhusus: $slotKhusus,
            pembimbing: $this->pembimbing2,
            bidang: $this->bidang,
            bulanMulai: now()->addMonth()->startOfMonth()->toDateString(),
            durasiBulan: 3,
            admin: $this->admin
        );
    }

    public function test_transaction_rollback_leaves_no_orphan_penempatan_record_when_exception_occurs(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
            'diputuskan_at' => now(),
        ]);

        // Simulasikan pendaftaran yang statusnya bukan Diterima saat transaksi berlangsung
        $pendaftaran->update(['status' => StatusPendaftaranEnum::Diajukan]);

        $action = app(\App\Actions\Admin\Penempatan\AssignPesertaAction::class);

        try {
            $action->execute($pendaftaran, $this->admin, $this->pembimbing1, $this->bidang);
            $this->fail('Harusnya melempar StatusSudahBerubahException');
        } catch (\App\Exceptions\Domain\StatusSudahBerubahException $e) {
            $this->assertStringContainsString('Diterima', $e->getMessage());
        }

        // Pastikan tidak ada penempatan yang dibuat
        $this->assertDatabaseMissing('penempatan', [
            'peserta_id' => $this->peserta->id,
        ]);
    }

    public function test_single_reassignment_follows_standard_lock_order_and_updates_mentor(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $action = app(\App\Actions\Admin\Penempatan\ReassignPembimbingAction::class);
        $result = $action->execute($penempatan, $this->pembimbing2, $this->admin);

        $this->assertEquals($this->pembimbing2->id, $result->pembimbing_id);
        $this->assertEquals($this->pembimbing2->id, $penempatan->fresh()->pembimbing_id);
    }

    public function test_bulk_reassign_sanitizes_duplicate_and_unsorted_ids_without_errors(): void
    {
        $p1 = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $p2 = Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $action = app(\App\Actions\Admin\Penempatan\BulkReassignPembimbingAction::class);

        // Pass duplicate IDs and in descending order [$p2->id, $p1->id, $p2->id]
        $updatedCount = $action->execute([$p2->id, $p1->id, $p2->id], $this->pembimbing2, $this->admin);

        $this->assertEquals(2, $updatedCount);
        $this->assertEquals($this->pembimbing2->id, $p1->fresh()->pembimbing_id);
        $this->assertEquals($this->pembimbing2->id, $p2->fresh()->pembimbing_id);
    }

    public function test_single_reassign_rejects_inactive_or_finished_penempatan(): void
    {
        $finishedPenempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subMonths(6)->toDateString(),
            'tgl_selesai' => now()->subMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Selesai,
        ]);

        $action = app(\App\Actions\Admin\Penempatan\ReassignPembimbingAction::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Pembimbing hanya dapat dipindahkan untuk penempatan yang berstatus aktif.');

        $action->execute($finishedPenempatan, $this->pembimbing2, $this->admin);
    }

    public function test_export_rekap_surat_diterima_accessible_by_admin_and_superadmin(): void
    {
        Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(5)->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Admin can export
        $this->actingAs($this->admin)
            ->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'csv']))
            ->assertOk();

        // SuperAdmin can export (read-only audit capability)
        $this->actingAs($this->superAdmin)
            ->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'csv']))
            ->assertOk();

        // Pembimbing cannot export admin report -> 403 Forbidden
        $this->actingAs($this->pembimbing1)
            ->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'csv']))
            ->assertForbidden();

        // Peserta cannot export admin report -> 403 Forbidden
        $this->actingAs($this->peserta)
            ->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'csv']))
            ->assertForbidden();
    }
}
