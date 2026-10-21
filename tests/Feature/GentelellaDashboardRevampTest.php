<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class GentelellaDashboardRevampTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $admin;

    private User $pembimbing;

    private User $peserta;

    private Bidang $bidang;

    private Jurusan $jurusan;

    private BulanMagang $bulanAktif;

    private Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

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

        $this->bulanAktif = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanAktif->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->superAdmin = $this->createUser(
            'Super Administrator',
            'superadmin@slemankab.go.id',
            RoleEnum::SuperAdmin
        );

        $this->admin = $this->createUser(
            'Admin Operasional Dinas',
            'admin@slemankab.go.id',
            RoleEnum::Admin,
            $this->bidang->id
        );

        $this->pembimbing = $this->createUser(
            'Pembimbing Lapangan',
            'pembimbing@slemankab.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta = $this->createUser(
            'Peserta Mahasiswa',
            'peserta@student.ac.id',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '21000999',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567899',
        ]);
    }

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

    /**
     * 1. Pengujian Akses HTTP 200 OK untuk SuperAdmin.
     */
    public function test_superadmin_can_access_dashboard_with_http_200(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard SuperAdmin');
        $response->assertSee('Super Administrator');
    }

    /**
     * 2. Pengujian Akses HTTP 200 OK untuk Admin Operasional.
     */
    public function test_admin_can_access_dashboard_with_http_200(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Operasional Administrasi');
        $response->assertSee('Admin Operasional Dinas');
    }

    /**
     * 3. Pengujian Akses HTTP 200 OK untuk Pembimbing Lapangan.
     */
    public function test_pembimbing_can_access_dashboard_with_http_200(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Pembimbing');
        $response->assertSee('Pembimbing Lapangan');
    }

    /**
     * 4. Pengujian Akses HTTP 200 OK untuk Peserta Magang.
     */
    public function test_peserta_can_access_dashboard_with_http_200(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Peserta');
        $response->assertSee('Peserta Mahasiswa');
    }

    /**
     * 5. Pengujian Keamanan: Pengguna Tamu (Guest) Diredirect ke Login.
     */
    public function test_unauthenticated_guests_are_redirected_to_login_for_all_dashboards(): void
    {
        $this->get(route('superadmin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('pembimbing.dashboard'))->assertRedirect(route('login'));
        $this->get(route('peserta.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * 6. Pengujian Otorisasi RBAC: Isolasi Akses Antar Role (Cross-Role Forbidden).
     */
    public function test_rbac_strictly_forbids_cross_role_access(): void
    {
        // Peserta dilarang membuka portal internal dinas
        $this->actingAs($this->peserta)->get(route('superadmin.dashboard'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('pembimbing.dashboard'))->assertForbidden();

        // Pembimbing dilarang membuka portal admin dan superadmin
        $this->actingAs($this->pembimbing)->get(route('superadmin.dashboard'))->assertForbidden();
        $this->actingAs($this->pembimbing)->get(route('admin.dashboard'))->assertForbidden();

        // Admin dilarang membuka portal superadmin
        $this->actingAs($this->admin)->get(route('superadmin.dashboard'))->assertForbidden();
    }

    /**
     * 7. Pengujian Komponen Blade Reusable Khas Gentelella (Tugas 2).
     */
    public function test_gentelella_blade_components_render_correctly(): void
    {
        // 7.1 Komponen tile-stat
        $viewTile = $this->blade(
            '<x-gentelella.tile-stat title="Total Pendaftar" value="128" trendText="+12%" trendType="success" subText="Bulan ini" />'
        );
        $viewTile->assertSee('tile_stats_count');
        $viewTile->assertSee('Total Pendaftar');
        $viewTile->assertSee('128');
        $viewTile->assertSee('+12%');
        $viewTile->assertSee('Bulan ini');

        // 7.2 Komponen x-panel
        $viewPanel = $this->blade(
            '<x-gentelella.x-panel title="Daftar Peserta" subtitle="Aktif" badge="Baru"><p>Konten Tabel Peserta</p></x-gentelella.x-panel>'
        );
        $viewPanel->assertSee('x_panel');
        $viewPanel->assertSee('x_title');
        $viewPanel->assertSee('Daftar Peserta');
        $viewPanel->assertSee('Aktif');
        $viewPanel->assertSee('Baru');
        $viewPanel->assertSee('Konten Tabel Peserta');
        $viewPanel->assertSee('collapse-link');

        // 7.3 Komponen page-title
        $viewTitle = $this->blade(
            '<x-gentelella.page-title title="Dashboard Eksekutif" description="Deskripsi sistem">'
            .'<x-slot:actions><button type="button">Ekspor Data</button></x-slot:actions>'
            .'</x-gentelella.page-title>'
        );
        $viewTitle->assertSee('page-title');
        $viewTitle->assertSee('Dashboard Eksekutif');
        $viewTitle->assertSee('Deskripsi sistem');
        $viewTitle->assertSee('Ekspor Data');
    }

    /**
     * 8. Pengujian Integrasi Kerangka Tata Letak Gentelella pada Master Layout (Tugas 3).
     */
    public function test_master_layout_renders_complete_gentelella_framework_structure(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();

        // 8.1 Pembungkus utama Gentelella
        $response->assertSee('class="container body"', false);
        $response->assertSee('class="main_container', false);

        // 8.2 Sidebar Kiri (left_col)
        $response->assertSee('class="col-md-3 left_col', false);
        $response->assertSee('class="site_title"', false);
        $response->assertSee('MagKom Sleman');
        $response->assertSee('class="profile clearfix"', false);
        $response->assertSee('class="profile_pic"', false);
        $response->assertSee('class="profile_info"', false);
        $response->assertSee('Selamat Datang,');
        $response->assertSee('sidebar-footer');

        // 8.3 Top Navigation (top_nav & nav_menu)
        $response->assertSee('class="top_nav', false);
        $response->assertSee('class="nav_menu', false);
        $response->assertSee('id="menu_toggle"', false);

        // 8.4 Content Area (right_col)
        $response->assertSee('class="right_col', false);

        // 8.5 Footer Gentelella & Script Tema
        $response->assertSee('class="gentelella-footer"', false);
        $response->assertSee('Portal MagKom &bull; Dinas Komunikasi dan Informatika Kabupaten Sleman', false);
        $response->assertSee('gentelella-theme.js', false);
    }

    /**
     * 9. Pengujian Integrasi Komponen Gentelella pada Dashboard SuperAdmin (Tugas 4).
     */
    public function test_superadmin_dashboard_renders_gentelella_tile_count_and_x_panels(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        // 9.1 Baris metrik tile_count dan tile_stats_count
        $response->assertSee('tile_count', false);
        $response->assertSee('tile_stats_count', false);
        $response->assertSee('Total pengguna');
        $response->assertSee('Menunggu verifikasi');
        $response->assertSee('Peserta aktif');
        $response->assertSee('Akun nonaktif');

        // 9.2 Kartu x_panel Gentelella
        $response->assertSee('x_panel', false);
        $response->assertSee('x_title', false);
        $response->assertSee('x_content', false);
        $response->assertSee('Pengguna terbaru');
        $response->assertSee('Audit log terbaru');
        $response->assertSee('Pusat Metrik Keberhasilan Produk & KPI Dinas', false);
    }

    /**
     * 10. Pengujian Integrasi Komponen Gentelella pada Dashboard Admin Operasional (Tugas 5).
     */
    public function test_admin_dashboard_renders_gentelella_tile_count_and_x_panels(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();

        // 10.1 Baris metrik tile_count dan tile_stats_count
        $response->assertSee('tile_count', false);
        $response->assertSee('tile_stats_count', false);
        $response->assertSee('Verifikasi Tertunda');
        $response->assertSee('Perlu Verifikasi Segera');
        $response->assertSee('Kapasitas Kuota Terisi');
        $response->assertSee('Antrean Waiting List');
        $response->assertSee('Peserta Magang Aktif');

        // 10.2 Kartu x_panel Gentelella
        $response->assertSee('x_panel', false);
        $response->assertSee('x_title', false);
        $response->assertSee('x_content', false);
        $response->assertSee('Antrean Verifikasi Berkas Pendaftaran');
        $response->assertSee('Alokasi & Keterisian Kuota Magang Periode', false);
        $response->assertSee('Status Operasional & Tim Kerja', false);
    }

    /**
     * 11. Pengujian Integrasi Komponen Gentelella pada Dashboard Pembimbing (Tugas 6).
     */
    public function test_pembimbing_dashboard_renders_gentelella_tile_count_and_x_panels(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.dashboard'));
        $response->assertOk();

        // 11.1 Baris metrik tile_count dan tile_stats_count
        $response->assertSee('tile_count', false);
        $response->assertSee('tile_stats_count', false);
        $response->assertSee('Beban Mentee Aktif');
        $response->assertSee('Reviu Logbook');
        $response->assertSee('Progres Tugas');
        $response->assertSee('Tugas Berjalan');

        // 11.2 Kartu x_panel Gentelella
        $response->assertSee('x_panel', false);
        $response->assertSee('x_title', false);
        $response->assertSee('x_content', false);
        $response->assertSee('Tenggat Waktu Tugas Mendekati Batas Akhir & Penugasan Mentee', false);
        $response->assertSee('Aktivitas Mentoring Minggu Ini');
        $response->assertSee('Daftar Mentee Bimbingan Aktif (Ringkasan)');
    }

    /**
     * 12. Pengujian Integrasi Komponen Gentelella pada Dashboard Peserta - Fase Onboarding (Tugas 7).
     */
    public function test_peserta_dashboard_renders_gentelella_onboarding_phase_panel(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));
        $response->assertOk();

        // 12.1 Header dan x_panel onboarding
        $response->assertSee('page-title', false);
        $response->assertSee('Dashboard Peserta');
        $response->assertSee('x_panel', false);
        $response->assertSee('x_title', false);
        $response->assertSee('Panduan Alur Pendaftaran Magang');
        $response->assertSee('Lengkapi Biodata & Profil', false);
        $response->assertSee('Pilih Periode & Kuota', false);
        $response->assertSee('Unggah Berkas & Ajukan', false);
    }

    /**
     * 13. Pengujian Integrasi Komponen Gentelella pada Dashboard Peserta - Fase Aktif (Tugas 7).
     */
    public function test_peserta_dashboard_renders_gentelella_active_phase_tiles_and_panels(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'kuota_id' => $this->kuota->id,
            'tgl_mulai_daftar' => now()->toDateString(),
            'tgl_selesai_daftar' => now()->addDays(30)->toDateString(),
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(50)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Integrasi API Dashboard Gentelella',
            'deskripsi' => 'Rampungkan tugas dashboard peserta',
            'kategori' => 'Development',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(3)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => now()->subDay()->toDateString(),
            'kegiatan' => 'Implementasi komponen Blade Gentelella untuk peserta',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));
        $response->assertOk();

        // 13.1 Banner dan Status Penempatan Aktif
        $response->assertSee('Status Penempatan: Magang Aktif');
        $response->assertSee('Pembimbing Lapangan');

        // 13.2 Baris Metrik tile_count & tile_stats_count Gentelella
        $response->assertSee('tile_count', false);
        $response->assertSee('tile_stats_count', false);
        $response->assertSee('Tugas Selesai');
        $response->assertSee('Logbook Mingguan');
        $response->assertSee('Sisa Hari Magang');
        $response->assertSee('Status Evaluasi');

        // 13.3 Kartu x_panel Gentelella untuk Tugas dan Logbook
        $response->assertSee('x_panel', false);
        $response->assertSee('Tugas Magang Mendesak');
        $response->assertSee('Integrasi API Dashboard Gentelella');
        $response->assertSee('Logbook Mingguan Terkini');
        $response->assertSee('Implementasi komponen Blade Gentelella untuk peserta');
    }

    /**
     * 14. Pengujian Interaktivitas UI & Responsivitas Mobile Tema Gentelella (Tugas 8).
     */
    public function test_gentelella_theme_assets_exist_and_provide_mobile_interactivity(): void
    {
        $jsPath = public_path('js/gentelella-theme.js');
        $cssPath = public_path('css/gentelella-theme.css');

        $this->assertFileExists($jsPath);
        $this->assertFileExists($cssPath);

        $jsContent = file_get_contents($jsPath);
        $this->assertStringContainsString('toggleGentelellaSidebar', $jsContent);
        $this->assertStringContainsString('toggleGentelellaPanel', $jsContent);
        $this->assertStringContainsString('closeGentelellaPanel', $jsContent);
        $this->assertStringContainsString('nav-sm', $jsContent);
        $this->assertStringContainsString('nav-md', $jsContent);
        $this->assertStringContainsString('drawer-open', $jsContent);
        $this->assertStringContainsString('sidebarBackdrop', $jsContent);
        $this->assertStringContainsString('Escape', $jsContent);

        $cssContent = file_get_contents($cssPath);
        $this->assertStringContainsString('max-width: 991.98px', $cssContent);
        $this->assertStringContainsString('drawer-open', $cssContent);
        $this->assertStringContainsString('#menu_toggle', $cssContent);

        // Pastikan kedua master layout menyematkan file tema Gentelella
        $adminResp = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminResp->assertSee('css/gentelella-theme.css', false);
        $adminResp->assertSee('js/gentelella-theme.js', false);

        $pesertaResp = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));
        $pesertaResp->assertSee('css/gentelella-theme.css', false);
        $pesertaResp->assertSee('js/gentelella-theme.js', false);
    }

    /**
     * 15. Pengujian Dukungan Dark Mode & Standar Aksesibilitas WCAG (Tugas 9).
     */
    public function test_gentelella_dark_mode_and_wcag_accessibility_attributes(): void
    {
        $cssPath = public_path('css/gentelella-theme.css');
        $cssContent = file_get_contents($cssPath);

        // Dukungan CSS Data-Theme Dark
        $this->assertStringContainsString('[data-theme="dark"]', $cssContent);
        $this->assertStringContainsString('[data-theme="dark"] .x_panel', $cssContent);
        $this->assertStringContainsString('[data-theme="dark"] .tile_stats_inner', $cssContent);
        $this->assertStringContainsString('[data-theme="dark"] .top_nav', $cssContent);
        $this->assertStringContainsString('[data-theme="dark"] .gentelella-footer', $cssContent);

        // Aksesibilitas WCAG pada Komponen x-panel
        $viewPanel = $this->blade(
            '<x-gentelella.x-panel title="Panel Aksesibilitas" subtitle="WCAG Test" :closeable="true"><p>Konten</p></x-gentelella.x-panel>'
        );

        $viewPanel->assertSee('role="region"', false);
        $viewPanel->assertSee('aria-label="Panel Aksesibilitas"', false);
        $viewPanel->assertSee('aria-expanded="true"', false);
        $viewPanel->assertSee('class="collapse-link"', false);
        $viewPanel->assertSee('class="close-link"', false);
        $viewPanel->assertSee('aria-label="Sembunyikan Panel"', false);
    }
}
