<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Rubrik;
use App\Models\SlotKhusus;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminPortalVisualTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Bidang $bidang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Pengelolaan perangkat lunak dan sistem informasi',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'status_override' => null,
            'aktif' => true,
        ]);

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator Visual';
        $this->superAdmin->email = 'superadmin.visual@kominfo.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Operasional Visual';
        $this->admin->email = 'admin.visual@kominfo.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan Visual';
        $this->pembimbing->email = 'mentor.visual@kominfo.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Uji Visual';
        $this->peserta->email = 'peserta.visual@gmail.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    public function test_superadmin_can_access_bulan_magang_page_with_modals_and_data(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.bulan.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Bulan Magang');
        $response->assertSee($this->bulan->bulan_mulai->translatedFormat('F Y'));
        $response->assertSee('modal-tambah-bulan');
        $response->assertSee('modal-edit-bulan');
    }

    public function test_superadmin_can_access_slot_khusus_page_with_data(): void
    {
        SlotKhusus::create([
            'bulan_mulai' => now()->startOfMonth(),
            'jumlah' => 4,
            'terisi' => 1,
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.slot-khusus.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Slot Khusus Internal');
        $response->assertSee('modalTambahSlot');
    }

    public function test_superadmin_can_access_master_data_pages(): void
    {
        // 1. Master Jurusan
        $resJurusan = $this->actingAs($this->superAdmin)->get(route('superadmin.jurusan.index'));
        $resJurusan->assertStatus(200);
        $resJurusan->assertSee('Master Data Jurusan');
        $resJurusan->assertSee('Teknik Informatika');
        $resJurusan->assertSee('modalTambahJurusan');

        // 2. Master Bidang
        $resBidang = $this->actingAs($this->superAdmin)->get(route('superadmin.bidang.index'));
        $resBidang->assertStatus(200);
        $resBidang->assertSee('Master Bidang Dinas');
        $resBidang->assertSee('Bidang Aplikasi Informatika');
        $resBidang->assertSee('modalTambahBidang');

        // 3. Master Rubrik
        $resRubrik = $this->actingAs($this->superAdmin)->get(route('superadmin.rubrik.index'));
        $resRubrik->assertStatus(200);
        $resRubrik->assertSee('Rubrik Penilaian');
        $resRubrik->assertSee('modalEditRubrik');
    }

    public function test_superadmin_can_access_staf_management_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.staf.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Staf Internal');
        $response->assertSee('Pembimbing Lapangan Visual');
        $response->assertSee('modalTambahStaf');
    }

    public function test_superadmin_can_access_audit_log_viewer_with_filters(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index'));

        $response->assertStatus(200);
        $response->assertSee('Jejak Audit Sistem');
        $response->assertSee('modalLogDetail');
    }

    public function test_superadmin_and_admin_can_access_laporan_hub_with_metrics_and_tabs(): void
    {
        // SuperAdmin can access all tabs including nilai
        $resSuper = $this->actingAs($this->superAdmin)->get(route('admin.laporan.index', ['tab' => 'nilai']));
        $resSuper->assertStatus(200);
        $resSuper->assertSee('Hub Laporan');
        $resSuper->assertSee('Laporan Rekapitulasi Nilai');

        // Admin can access laporan index but restricted from nilai tab (falls back to pendaftaran)
        $resAdmin = $this->actingAs($this->admin)->get(route('admin.laporan.index', ['tab' => 'nilai']));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Laporan Rekapitulasi Pendaftaran');
    }

    public function test_superadmin_can_access_monitoring_peserta_lintas_bidang(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('Monitoring Peserta');
    }
}
