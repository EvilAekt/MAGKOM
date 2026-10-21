<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class NextLevelEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $admin;

    private User $pembimbing;

    private Bidang $bidang;

    private Jurusan $jurusan;

    private BulanMagang $bulanMagang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Bidang Aptika Kominfo Sleman',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->superAdmin = $this->createUser('Super Administrator MagKom', 'superadmin@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->admin = $this->createUser('Admin Magang Diskominfo', 'admin@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing = $this->createUser('Pembimbing Aptika', 'pembimbing@slemankab.go.id', RoleEnum::Pembimbing, $this->bidang->id);
    }

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->role = $role;
        $user->status_aktif = true;
        $user->must_change_password = false;
        $user->email_verified_at = now();
        $user->bidang_id = $bidangId;
        $user->save();

        return $user;
    }

    private function createPeserta(string $nama, string $email, JenjangEnum $jenjang = JenjangEnum::S1): User
    {
        $peserta = $this->createUser($nama, $email, RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '123456789',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => $jenjang,
            'no_hp' => '081234567890',
        ]);

        return $peserta;
    }

    public function test_pembimbing_can_bulk_approve_logbooks_and_records_audit_trail(): void
    {
        $peserta = $this->createPeserta('Peserta Alpha', 'alpha@magkom.test');

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'tgl_mulai' => now()->subDays(10),
            'tgl_selesai' => now()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum->value,
            'sumber' => SumberPenempatanEnum::Online->value,
        ]);

        $logbook1 = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => now()->subDays(2),
            'kegiatan' => 'Implementasi modul autentikasi sistem',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $logbook2 = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => now()->subDay(),
            'kegiatan' => 'Integrasi API dan unit testing fitur',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.logbook.bulk-review'), [
                'logbook_ids' => [$logbook1->id, $logbook2->id],
            ]);

        $response->assertRedirect(route('pembimbing.logbook.index'));
        $response->assertSessionHas('success');

        $this->assertSame(StatusLogbookEnum::Disetujui, $logbook1->fresh()->status);
        $this->assertSame(StatusLogbookEnum::Disetujui, $logbook2->fresh()->status);

        $auditCount = AuditLog::where('entitas', 'logbook')
            ->whereIn('entitas_id', [$logbook1->id, $logbook2->id])
            ->where('aksi', 'bulk_review_logbook')
            ->count();
        $this->assertSame(2, $auditCount);
    }

    public function test_pembimbing_cannot_bulk_approve_other_pembimbing_mentee_logbooks(): void
    {
        $otherPembimbing = $this->createUser('Pembimbing Lain', 'lain@slemankab.go.id', RoleEnum::Pembimbing, $this->bidang->id);

        $peserta = $this->createPeserta('Peserta Beta', 'beta@magkom.test');

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $otherPembimbing->id,
            'bidang_id' => $this->bidang->id,
            'tgl_mulai' => now()->subDays(10),
            'tgl_selesai' => now()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum->value,
            'sumber' => SumberPenempatanEnum::Online->value,
        ]);

        $unownedLogbook = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => now()->subDay(),
            'kegiatan' => 'Penyusunan basis data',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.logbook.bulk-review'), [
                'logbook_ids' => [$unownedLogbook->id],
            ]);

        $response->assertForbidden();
        $this->assertSame(StatusLogbookEnum::Terkirim, $unownedLogbook->fresh()->status);
    }

    public function test_pembimbing_can_export_rekap_bimbingan_bidang_csv_safely(): void
    {
        $peserta = $this->createPeserta('=FormulaInjection', 'formula@magkom.test');

        Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'tgl_mulai' => now()->subDays(10),
            'tgl_selesai' => now()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum->value,
            'sumber' => SumberPenempatanEnum::Online->value,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.penilaian.ekspor-bidang'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString("'=FormulaInjection", $content);
    }

    public function test_last_superadmin_protection_prevents_deactivation(): void
    {
        // 1. Trying to toggle own account is rejected
        $responseSelf = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $this->superAdmin));

        $responseSelf->assertRedirect();
        $responseSelf->assertSessionHas('error');

        // 2. Add second superadmin
        $otherSuperAdmin = $this->createUser('Super Admin Kedua', 'super2@slemankab.go.id', RoleEnum::SuperAdmin);

        // Deactivating the second superadmin is permitted (leaving 1)
        $responseDeactivate = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $otherSuperAdmin));
        $responseDeactivate->assertSessionHas('success');
        $this->assertFalse($otherSuperAdmin->fresh()->status_aktif);

        // Now active superadmins = 1 (only $this->superAdmin).
        // Try toggling when only 1 active exists
        $otherSuperAdmin->status_aktif = true;
        $otherSuperAdmin->save();
        $this->superAdmin->status_aktif = false;
        $this->superAdmin->save();

        // Now $otherSuperAdmin is the LAST active superadmin, trying to toggle self
        $responseLast = $this->actingAs($otherSuperAdmin)
            ->patch(route('superadmin.staf.toggle', $otherSuperAdmin));
        $responseLast->assertSessionHas('error');
    }

    public function test_superadmin_deactivating_staf_revokes_remember_token(): void
    {
        $this->admin->remember_token = 'secret_remember_token_string';
        $this->admin->save();

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.staf.toggle', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($this->admin->fresh()->status_aktif);
        $this->assertNull($this->admin->fresh()->remember_token);
    }

    public function test_superadmin_can_update_system_configuration_and_clears_cache(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan.update'), [
                'masa_tenggang_bulan' => 6,
                'kuota_umum_maks_per_bulan' => 18,
                'slot_khusus_default' => 3,
                'maks_peserta_aktif_per_pembimbing' => 7,
                'logbook_maks_mundur_hari' => 4,
                'berkas_maks_mb' => 5,
                'deadline_mendekati_hari' => 3,
                'batas_predikat_a' => 88,
                'batas_predikat_b' => 75,
                'batas_predikat_c' => 60,
                'batas_predikat_d' => 45,
            ]);

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertSame('18', Pengaturan::get('kuota_umum_maks_per_bulan'));
        $this->assertSame('4', Pengaturan::get('logbook_maks_mundur_hari'));
    }

    public function test_superadmin_can_export_filtered_audit_log_csv_safely(): void
    {
        AuditLog::create([
            'waktu' => now(),
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => '@InsecureActionLog',
            'entitas' => 'pengaturan',
            'entitas_id' => 1,
            'ip' => '127.0.0.1',
            'metode' => 'PUT',
            'rute' => '/superadmin/pengaturan',
            'status_http' => 302,
            'durasi_ms' => 45,
            'request_id' => 'REQ-TEST-AUDIT-1',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.audit-log.export', ['q' => 'InsecureActionLog']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString("'@InsecureActionLog", $content);
    }

    public function test_admin_can_bulk_close_waiting_list_queue_for_started_month(): void
    {
        // Past month
        $pastMonth = BulanMagang::create([
            'bulan_mulai' => now()->subMonth()->startOfMonth()->toDateString(),
            'tgl_buka' => now()->subMonths(2)->toDateString(),
            'tgl_tutup' => now()->subMonth()->subDays(5)->toDateString(),
            'aktif' => true,
        ]);

        $kuotaPast = Kuota::create([
            'bulan_magang_id' => $pastMonth->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 5,
            'aktif' => true,
        ]);

        $peserta = $this->createPeserta('Peserta Waiting', 'waiting@magkom.test');

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $pastMonth->id,
            'kuota_id' => $kuotaPast->id,
            'status' => StatusPendaftaranEnum::WaitingList,
            'diajukan_at' => now()->subDays(15),
            'durasi_bulan' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.waiting-list.tutup', $pastMonth));

        $response->assertRedirect(route('admin.waiting-list.index'));
        $response->assertSessionHas('success');

        $this->assertSame(StatusPendaftaranEnum::Ditolak, $pendaftaran->fresh()->status);
        $this->assertStringContainsString('ditutup secara otomatis', (string) $pendaftaran->fresh()->catatan);
    }

    public function test_admin_cannot_close_waiting_list_queue_for_future_month(): void
    {
        $futureMonth = BulanMagang::create([
            'bulan_mulai' => now()->addMonths(2)->startOfMonth()->toDateString(),
            'tgl_buka' => now()->addMonth()->toDateString(),
            'tgl_tutup' => now()->addMonths(2)->subDays(5)->toDateString(),
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.waiting-list.tutup', $futureMonth));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_export_rekapitulasi_surat_diterima_csv(): void
    {
        $peserta = $this->createPeserta('+DangerousPeserta', 'dangerous@magkom.test');

        Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addMonth(),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum->value,
            'sumber' => SumberPenempatanEnum::Online->value,
            'no_surat' => '500.12/0042/DISKOMINFO/2026',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.ekspor-surat-diterima'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString("'+DangerousPeserta", $content);
        $this->assertStringContainsString('500.12/', $content);
        $this->assertStringContainsString('/DISKOMINFO/', $content);
    }

    public function test_admin_laporan_hub_renders_visual_analytics_data(): void
    {
        Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 8,
            'aktif' => true,
        ]);

        $this->createPeserta('Peserta SMK', 'smk@magkom.test', JenjangEnum::Smk);
        $this->createPeserta('Peserta D3', 'd3@magkom.test', JenjangEnum::D3);
        $this->createPeserta('Peserta S1', 's1@magkom.test', JenjangEnum::S1);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index'));

        $response->assertOk();
        $response->assertViewHas('analyticsKuotaJurusan');
        $response->assertViewHas('analyticsJenjang');
        $response->assertSee('Tingkat Keterisian Kuota per Jurusan');
        $response->assertSee('Proporsi Jenjang Calon Peserta');
        $response->assertSee('Teknik Informatika');
    }
}
