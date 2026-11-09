<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
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
        $response->assertSee('TOTAL PERIODE');
        $response->assertSee('PERIODE AKTIF');
        $response->assertSee('PENDAFTARAN BUKA');
        $response->assertSee('OVERRIDE STATUS');
        $response->assertSee($this->bulan->bulan_mulai->translatedFormat('F Y'));
        $response->assertSee('modal-tambah-bulan');
        $response->assertSee('modal-edit-bulan');
        $response->assertViewHas('totalBulan', 1);
        $response->assertViewHas('totalAktif', 1);
        $response->assertViewHas('pendaftaranBuka', 1);
        $response->assertViewHas('totalOverride', 0);
    }

    public function test_superadmin_can_access_slot_khusus_page_with_data(): void
    {
        $slot = SlotKhusus::create([
            'bulan_mulai' => now()->startOfMonth(),
            'jumlah' => 4,
            'terisi' => 1,
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '21120120140123',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
        ]);

        Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'slot_khusus_id' => $slot->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->superAdmin->id,
            'tgl_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_selesai' => now()->addMonths(2)->endOfMonth()->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.slot-khusus.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Slot Khusus Internal');
        $response->assertSee('Daftar Alokasi Slot Khusus Mitra');
        $response->assertSee('Daftar Peserta Magang Jalur Khusus');
        $response->assertSee('sa-text-slot-val');
        $response->assertSee('4 Slot');
        $response->assertSee('1 Slot');
        $response->assertSee($this->peserta->nama);
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('modalTambahSlot');
        $response->assertViewHas('totalAlokasiSlot', 4);
        $response->assertViewHas('totalSlotTerisi', 1);
        $response->assertViewHas('sisaSlotKhusus', 3);
        $response->assertViewHas('pesertaKhususList');
    }

    public function test_superadmin_can_access_create_peserta_khusus_page_with_hero_banner(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.peserta-khusus.create'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Peserta Jalur Khusus');
        $response->assertSee('Formulir Pendaftaran Peserta Jalur Khusus');
        $response->assertDontSee('(Langsung Aktif)');
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
        // SuperAdmin can access hub laporan
        $resSuper = $this->actingAs($this->superAdmin)->get(route('admin.laporan.index', ['tab' => 'penempatan']));
        $resSuper->assertStatus(200);
        $resSuper->assertSee('Hub Laporan');
        $resSuper->assertSee('Laporan Data Penempatan Magang');

        // Admin can access laporan index
        $resAdmin = $this->actingAs($this->admin)->get(route('admin.laporan.index', ['tab' => 'pendaftaran']));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Laporan Rekapitulasi Pendaftaran');
    }

    public function test_superadmin_sidebar_renders_updated_navigation_labels_for_staf_and_backup(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Data Staf');
        $response->assertSee('Backup Database');
        $response->assertDontSee('Data Pengguna');
        $response->assertDontSee('Cadangan Database');
    }

    public function test_superadmin_monitoring_peserta_route_is_removed(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/monitoring-peserta');

        $response->assertStatus(404);
    }
}
