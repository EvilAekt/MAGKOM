<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminFigmaDesignAlignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $kuota = \App\Models\Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 1,
            'aktif' => true,
        ]);

        // Akun SuperAdmin
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'SuperAdmin Kominfo Sleman';
        $this->superAdmin->email = 'superadmin.figma@slemankab.go.id';
        $this->superAdmin->password = bcrypt('Password123!');
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        // Akun Admin Operasional
        $this->admin = new User();
        $this->admin->nama = 'Admin Verifikasi Berkas';
        $this->admin->email = 'admin.figma@slemankab.go.id';
        $this->admin->password = bcrypt('Password123!');
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->must_change_password = false;
        $this->admin->save();

        // Akun Pembimbing
        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Lapangan Aptika';
        $this->pembimbing->email = 'pembimbing.figma@slemankab.go.id';
        $this->pembimbing->password = bcrypt('Password123!');
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        // Akun Peserta Aktif
        $this->peserta = new User();
        $this->peserta->nama = 'Aldi Artha Pratama';
        $this->peserta->email = 'aldi.peserta@example.com';
        $this->peserta->password = bcrypt('Password123!');
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '220101990',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
        ]);

        Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $kuota->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->superAdmin->id,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-10-01',
            'tgl_selesai' => '2026-12-31',
        ]);

        // Pendaftaran Menunggu Verifikasi
        $pesertaPending = new User();
        $pesertaPending->nama = 'Rina Wulandari';
        $pesertaPending->email = 'rina.pending@example.com';
        $pesertaPending->password = bcrypt('Password123!');
        $pesertaPending->role = RoleEnum::Peserta;
        $pesertaPending->status_aktif = true;
        $pesertaPending->email_verified_at = now();
        $pesertaPending->must_change_password = false;
        $pesertaPending->save();

        Pendaftaran::create([
            'user_id' => $pesertaPending->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // Catat sampel Audit Log
        AuditLog::create([
            'request_id' => 'req-test-figma-001',
            'user_id' => $this->superAdmin->id,
            'tipe' => \App\Enums\TipeAuditEnum::Aksi,
            'aksi' => 'Konfigurasi parameter sistem diperbarui',
            'entitas' => 'pengaturan',
            'entitas_id' => 1,
            'nilai_lama' => null,
            'nilai_baru' => ['param' => 'val'],
            'detail' => ['aksi' => 'Konfigurasi parameter sistem diperbarui'],
            'metode' => 'PUT',
            'rute' => '/superadmin/pengaturan',
            'status_http' => 200,
            'durasi_ms' => 30,
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit/Test',
            'waktu' => now(),
        ]);
    }

    /**
     * Memastikan dashboard SuperAdmin menampilkan elemen visual presisi sesuai Figma Node 560-26404.
     */
    public function test_superadmin_dashboard_renders_figma_hero_cards_and_columns(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // 1. Hero Sapaan Figma
        $response->assertSee('Dashboard SuperAdmin');
        $response->assertSee('Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi');

        // 2. Empat Stat Cards Utama Figma
        $response->assertSee('Total pengguna');
        $response->assertSee('Menunggu verifikasi');
        $response->assertSee('Peserta aktif');
        $response->assertSee('Akun nonaktif');

        // 3. Grid Dua Kolom Utama Figma
        $response->assertSee('Pengguna terbaru');
        $response->assertSee('Kelola pengguna');
        $response->assertSee('Aldi Artha Pratama');
        $response->assertSee('Audit log terbaru');
        $response->assertSee('Lihat semua');
        $response->assertSee('Konfigurasi parameter sistem diperbarui');

        // 4. Struktur Branding Sidebar Baru (Node 560-24892)
        $response->assertSee('MagKom');
        $response->assertSee('KOMINFO PORTAL');
        $response->assertSee('MODUL OPERASIONAL');
        $response->assertSee('SISTEM');
        $response->assertSee('Sistem internal Kominfo');
    }

    /**
     * Memastikan modul Role SuperAdmin (Figma SA-02) dapat diakses dan merender matriks wewenang.
     */
    public function test_superadmin_role_module_renders_rbac_matrix(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.role.index'));

        $response->assertStatus(200);
        $response->assertSee('Hak Akses dan Role Pengguna');
        $response->assertSee('Matriks Wewenang');
        $response->assertSee('SuperAdmin Mewarisi Seluruh Hak Akses');
        $response->assertSee('Administrasi');
        $response->assertSee('Pembimbing');
        $response->assertSee('Peserta Magang');
    }

    /**
     * Memastikan akses modul Role dilindungi hanya untuk SuperAdmin (RBAC).
     */
    public function test_non_superadmin_cannot_access_role_module(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('superadmin.role.index'));
        $response->assertStatus(403);
    }

    /**
     * Memastikan sidebar merender menu grup accordion interaktif (Konfigurasi, Administrasi, Peserta)
     * lengkap dengan tombol toggleNavGroup, aria-expanded, dan elemen submenu fleksibel.
     */
    public function test_sidebar_renders_collapsible_groups_with_interactive_toggle_markup(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // 1. Verifikasi markup accordion pada grup Konfigurasi
        $response->assertSee('Konfigurasi');
        $response->assertSee('toggleNavGroup(this)', false);
        $response->assertSee('aria-expanded=', false);
        $response->assertSee('nav-sub-menu', false);
        $response->assertSee('Pengaturan Sistem');
        $response->assertSee('Bulan Magang');
        $response->assertSee('Slot Khusus');
        $response->assertSee('Master Jurusan');
        $response->assertSee('Bidang Dinas');

        // 2. Verifikasi keberadaan definisi fungsi JavaScript toggleNavGroup & toggleSidebar
        $response->assertSee('function toggleNavGroup(btn)', false);
        $response->assertSee('function toggleSidebar(forceState)', false);
    }

    /**
     * Memastikan sidebar menerapkan teknik invisible scrollbar modern
     * agar bilah scrollbar abu-abu native Windows hilang total tanpa merusak navigasi.
     */
    public function test_sidebar_applies_seamless_invisible_scrollbar_styling(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // Memastikan aturan CSS scrollbar-width: none dan webkit scrollbar display: none terpasang
        $response->assertSee('.dashboard-sidebar {', false);
        $response->assertSee('.dashboard-sidebar::-webkit-scrollbar {', false);
        $response->assertSee('.sidebar-nav {', false);
        $response->assertSee('.sidebar-nav::-webkit-scrollbar {', false);
        $response->assertSee('scrollbar-width: none;', false);
    }
}
