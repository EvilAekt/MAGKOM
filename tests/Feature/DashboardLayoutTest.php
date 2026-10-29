<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class DashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $localViewsPath = realpath(__DIR__ . '/../../resources/views');
        if ($localViewsPath && is_dir($localViewsPath)) {
            \Illuminate\Support\Facades\View::getFinder()->setPaths([$localViewsPath]);
        }

        Route::get('/testing/dashboard-layout-view', function () {
            return View::make('testing.dashboard-dummy');
        })->middleware(['web', 'auth'])->name('testing.dashboard-dummy');

        Route::get('/testing/peserta-layout-view', function () {
            return View::make('testing.peserta-dummy');
        })->middleware(['web', 'auth'])->name('testing.peserta-dummy');
    }

    private function createDummyBladeView(): void
    {
        $baseDir = realpath(__DIR__ . '/../../resources/views') ?: App::resourcePath('views');
        $dir = $baseDir . '/testing';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $viewContent = <<<'BLADE'
@extends('layouts.dashboard')

@section('title', 'Testing Dashboard Layout')
@section('page_title', 'Halaman Pengujian')
@section('page_description', 'Deskripsi pengujian dashboard layout terpadu')

@section('page_actions')
    <button type="button" id="dummyBtn">Tombol Aksi</button>
@endsection

@section('content')
    <div id="dummyContentArea">
        <p>Konten Pengujian Berhasil Dirender</p>
    </div>
@endsection
BLADE;

        file_put_contents($baseDir . '/testing/dashboard-dummy.blade.php', $viewContent);

        $pesertaViewContent = <<<'BLADE'
@extends('layouts.peserta')

@section('title', 'Testing Peserta Layout')
@section('page_title', 'Halaman Pengujian Peserta')

@section('content')
    <div id="dummyPesertaContentArea">
        <p>Konten Peserta Berhasil Dirender</p>
    </div>
@endsection
BLADE;

        file_put_contents($baseDir . '/testing/peserta-dummy.blade.php', $pesertaViewContent);
    }

    private function removeDummyBladeView(): void
    {
        $baseDir = realpath(__DIR__ . '/../../resources/views') ?: App::resourcePath('views');
        $file = $baseDir . '/testing/dashboard-dummy.blade.php';
        if (file_exists($file)) {
            unlink($file);
        }
        $filePeserta = $baseDir . '/testing/peserta-dummy.blade.php';
        if (file_exists($filePeserta)) {
            unlink($filePeserta);
        }
        $dir = $baseDir . '/testing';
        if (is_dir($dir)) {
            @rmdir($dir);
        }
    }

    private function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'PasswordRahasia123!';
        $user->role = $role;
        $user->email_verified_at = Carbon::now();
        $user->status_aktif = true;
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    public function test_peserta_renders_peserta_specific_menus(): void
    {
        $this->createDummyBladeView();

        try {
            $peserta = $this->createUser('Budi Santoso', 'budi@example.com', RoleEnum::Peserta);

            $response = $this->actingAs($peserta)->get('/testing/dashboard-layout-view');

            $response->assertOk();
            $response->assertSee('Portal MagKom');
            $response->assertSee('Dinas Kominfo Sleman');
            $response->assertSee('Budi Santoso');
            $response->assertSee('Peserta');

            // Menu Peserta
            $response->assertSee('Profil Saya');
            $response->assertSee('Pendaftaran Magang');
            $response->assertSee('Logbook Harian');
            $response->assertSeeText('Tugas & Progres', false);
            $response->assertSee('Lembar Nilai');

            // Tidak boleh ada menu Staf
            $response->assertDontSee('Verifikasi Berkas');
            $response->assertDontSee('Manajemen Staf');
            $response->assertDontSee('Jejak Audit Log');
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_pembimbing_renders_pembimbing_specific_menus(): void
    {
        $this->createDummyBladeView();

        try {
            $pembimbing = $this->createUser('Pak Pembimbing', 'pembimbing@sleman.go.id', RoleEnum::Pembimbing);

            $response = $this->actingAs($pembimbing)->get('/testing/dashboard-layout-view');

            $response->assertOk();
            $response->assertSee('Pak Pembimbing');
            $response->assertSee('Pembimbing');

            // Menu Pembimbing
            $response->assertSee('Tugas Peserta');
            $response->assertSee('Reviu Logbook');
            // Menu Profil & Pengaturan Pembimbing
            $response->assertSee('Profil Saya');
            $response->assertSee('Pengaturan Akun');

            // Tidak boleh ada menu Peserta/SuperAdmin
            $response->assertDontSee('Pendaftaran Magang');
            $response->assertDontSee('Manajemen Staf');
            $response->assertDontSee('Jejak Audit Log');
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_admin_renders_admin_specific_menus(): void
    {
        $this->createDummyBladeView();

        try {
            $admin = $this->createUser('Ibu Admin Dinas', 'admin@sleman.go.id', RoleEnum::Admin);

            $response = $this->actingAs($admin)->get('/testing/dashboard-layout-view');

            $response->assertOk();
            $response->assertSee('Ibu Admin Dinas');
            $response->assertSee('Admin');

            // Menu Admin
            $response->assertSee('Verifikasi Berkas');
            $response->assertSee('Manajemen Kuota');
            $response->assertSee('Waiting List');
            $response->assertSee('Penempatan');
            $response->assertSeeText('Laporan & Ekspor', false);

            // Tidak boleh ada menu SuperAdmin khusus
            $response->assertDontSee('Slot Khusus');
            $response->assertDontSee('Manajemen Staf');
            $response->assertDontSee('Jejak Audit Log');
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_superadmin_renders_superadmin_specific_menus(): void
    {
        $this->createDummyBladeView();

        try {
            $superadmin = $this->createUser('Super Administrator', 'superadmin@sleman.go.id', RoleEnum::SuperAdmin);

            $response = $this->actingAs($superadmin)->get('/testing/dashboard-layout-view');

            $response->assertOk();
            $response->assertSee('Super Administrator');
            $response->assertSee('Superadmin');

            // Menu SuperAdmin
            $response->assertSee('Bulan Magang');
            $response->assertSee('Slot Khusus');
            $response->assertSee('Manajemen Staf');
            $response->assertSee('Master Jurusan');
            $response->assertSee('Bidang Dinas');
            $response->assertSee('Rubrik Nilai');
            $response->assertSee('Pengaturan Sistem');
            $response->assertSee('Jejak Audit Log');
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_peserta_in_grace_period_shows_grace_period_banner(): void
    {
        $this->createDummyBladeView();

        try {
            $peserta = $this->createUser('Siswa Lulus', 'lulus@example.com', RoleEnum::Peserta);
            $pembimbing = $this->createUser('Pembimbing Lapangan', 'pembimbing.lap@sleman.go.id', RoleEnum::Pembimbing);

            $jurusan = Jurusan::create([
                'kode' => 'RPL',
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
                'kategori' => 'smk',
                'aktif' => true,
            ]);

            $bulan = BulanMagang::create([
                'bulan_mulai' => '2026-01-01',
                'tgl_buka' => '2025-12-01',
                'tgl_tutup' => '2025-12-25',
                'aktif' => true,
            ]);

            $kuota = Kuota::create([
                'bulan_magang_id' => $bulan->id,
                'jurusan_id' => $jurusan->id,
                'jumlah' => 10,
                'terisi' => 1,
                'aktif' => true,
            ]);

            $pendaftaran = Pendaftaran::create([
                'user_id' => $peserta->id,
                'bulan_magang_id' => $bulan->id,
                'kuota_id' => $kuota->id,
                'nomor_pendaftaran' => 'REG-TEST-001',
                'status' => 'diterima',
                'jenjang' => 'smk',
                'jalur' => 'umum',
                'asal_instansi' => 'SMK N 1 Sleman',
                'tgl_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                'durasi_bulan' => 3,
            ]);

            Penempatan::create([
                'pendaftaran_id' => $pendaftaran->id,
                'peserta_id' => $peserta->id,
                'pembimbing_id' => $pembimbing->id,
                'kuota_id' => $kuota->id,
                'jalur' => 'umum',
                'sumber' => 'online',
                'tgl_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                'tgl_selesai' => Carbon::now()->subDays(5)->toDateString(),
                'tgl_ditetapkan_selesai' => Carbon::now()->subDays(5)->toDateString(),
                'tgl_nonaktif' => Carbon::now()->addDays(25)->toDateString(),
                'status' => StatusPenempatanEnum::Selesai,
            ]);

            $response = $this->actingAs($peserta)->get('/testing/dashboard-layout-view');

            $response->assertOk();
            $response->assertSee('Program Magang Telah Selesai (Masa Tenggang)');
            $response->assertSee('Unduh Lembar Nilai');
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_sidebar_footer_theme_toggle_standardized_across_all_four_roles_in_dashboard_layout(): void
    {
        $this->createDummyBladeView();

        try {
            $roles = [
                'Admin' => RoleEnum::Admin,
                'Pembimbing' => RoleEnum::Pembimbing,
                'SuperAdmin' => RoleEnum::SuperAdmin,
                'Peserta' => RoleEnum::Peserta,
            ];

            foreach ($roles as $label => $role) {
                $user = $this->createUser("User {$label}", strtolower($label) . "@sleman.go.id", $role);
                $response = $this->actingAs($user)->get('/testing/dashboard-layout-view');

                $response->assertOk();

                // AC-1: Header topbar bersih dari tombol theme toggle lama
                $response->assertDontSee('id="themeToggleBtn"', false);
                $response->assertDontSee('topbar-theme-toggle-btn', false);

                // AC-2: Sidebar footer memiliki 3 aksi terstruktur
                $response->assertSee('sidebar-footer', false);
                $response->assertSee('sidebar-footer-btn', false);
                $response->assertSee('Pengaturan Akun', false);
                $response->assertSee(URL::route('logout'), false);

                // AC-3: Tombol sidebarThemeToggleBtn memiliki dual-icon (sun-icon & moon-icon) dan aria-pressed
                $response->assertSee('id="sidebarThemeToggleBtn"', false);
                $response->assertSee('togglePortalThemeQuick()', false);
                $response->assertSee('aria-pressed="false"', false);
                $response->assertSee('class="sun-icon"', false);
                $response->assertSee('class="moon-icon"', false);

                // AC-4: Script pendukung engine tema tersedia
                $response->assertSee('window.applyPortalTheme', false);
                $response->assertSee('window.togglePortalThemeQuick', false);
            }
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_peserta_layout_renders_standardized_sidebar_footer_theme_toggle(): void
    {
        $this->createDummyBladeView();

        try {
            $peserta = $this->createUser('Peserta Mandiri', 'peserta.mandiri@example.com', RoleEnum::Peserta);
            $response = $this->actingAs($peserta)->get('/testing/peserta-layout-view');

            $response->assertOk();

            // AC-1: Header topbar bersih dari tombol theme toggle lama
            $response->assertDontSee('id="themeToggleBtn"', false);
            $response->assertDontSee('topbar-theme-toggle-btn', false);

            // AC-2: Sidebar footer memiliki 3 aksi terstruktur
            $response->assertSee('sidebar-footer', false);
            $response->assertSee('sidebar-footer-btn', false);
            $response->assertSee(URL::route('peserta.pengaturan.index'), false);
            $response->assertSee(URL::route('logout'), false);

            // AC-3: Tombol sidebarThemeToggleBtn memiliki dual-icon (sun-icon & moon-icon) dan aria-pressed
            $response->assertSee('id="sidebarThemeToggleBtn"', false);
            $response->assertSee('togglePortalThemeQuick()', false);
            $response->assertSee('aria-pressed="false"', false);
            $response->assertSee('class="sun-icon"', false);
            $response->assertSee('class="moon-icon"', false);

            // F4: Dropdown profil tidak lagi memuat duplikat tombol mode tampilan
            $response->assertDontSee('onclick="togglePortalThemeQuick()" style="width: 100%', false);

            // AC-4: Script pendukung engine tema tersedia
            $response->assertSee('window.applyPortalTheme', false);
            $response->assertSee('window.togglePortalThemeQuick', false);
        } finally {
            $this->removeDummyBladeView();
        }
    }

    public function test_theme_preference_persistence_endpoint_for_all_roles(): void
    {
        $admin = $this->createUser('Admin Tema', 'admin.tema@sleman.go.id', RoleEnum::Admin);
        $respAdmin = $this->actingAs($admin)->post(URL::route('admin.pengaturan.preferensi.update'), [
            'tema' => 'gelap',
        ]);
        $respAdmin->assertRedirect();
        $respAdmin->assertSessionHas('tema_tampilan', 'gelap');
        $respAdmin->assertCookie('tema_tampilan', 'gelap');

        $pembimbing = $this->createUser('Pembimbing Tema', 'pembimbing.tema@sleman.go.id', RoleEnum::Pembimbing);
        $respPembimbing = $this->actingAs($pembimbing)->post(URL::route('pembimbing.pengaturan.preferensi.update'), [
            'tema' => 'gelap',
        ]);
        $respPembimbing->assertRedirect();
        $respPembimbing->assertSessionHas('tema_tampilan', 'gelap');
        $respPembimbing->assertCookie('tema_tampilan', 'gelap');

        $superadmin = $this->createUser('SuperAdmin Tema', 'superadmin.tema@sleman.go.id', RoleEnum::SuperAdmin);
        $respSuperadmin = $this->actingAs($superadmin)->post(URL::route('superadmin.akun.preferensi.update'), [
            'tema' => 'gelap',
        ]);
        $respSuperadmin->assertRedirect();
        $respSuperadmin->assertSessionHas('tema_tampilan', 'gelap');
        $respSuperadmin->assertCookie('tema_tampilan', 'gelap');

        $peserta = $this->createUser('Peserta Tema', 'peserta.tema@example.com', RoleEnum::Peserta);
        $respPeserta = $this->actingAs($peserta)->post(URL::route('peserta.pengaturan.preferensi.update'), [
            'tema' => 'gelap',
        ]);
        $respPeserta->assertRedirect();
        $respPeserta->assertSessionHas('tema_tampilan', 'gelap');
        $respPeserta->assertCookie('tema_tampilan', 'gelap');

        // Test toggle kembali ke 'terang'
        $respAdminLight = $this->actingAs($admin)->post(URL::route('admin.pengaturan.preferensi.update'), [
            'tema' => 'terang',
        ]);
        $respAdminLight->assertRedirect();
        $respAdminLight->assertSessionHas('tema_tampilan', 'terang');
        $respAdminLight->assertCookie('tema_tampilan', 'terang');
    }
}
