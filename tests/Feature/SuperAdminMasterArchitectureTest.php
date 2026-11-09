<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SuperAdminMasterArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication(): Application
    {
        $basePath = dirname(__DIR__, 2);
        putenv("APP_BASE_PATH={$basePath}");
        $_ENV['APP_BASE_PATH'] = $basePath;
        $_SERVER['APP_BASE_PATH'] = $basePath;

        $app = require $basePath.'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Setup User SuperAdmin
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Admin Sleman';
        $this->superAdmin->email = 'superadmin.master@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        // 2. Setup User Admin
        $this->admin = new User;
        $this->admin->nama = 'Admin Dinas MagKom';
        $this->admin->email = 'admin.master@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        // 3. Setup User Pembimbing
        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Master';
        $this->pembimbing->email = 'pembimbing.master@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        // 4. Setup User Peserta
        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang Master';
        $this->peserta->email = 'peserta.master@universitas.ac.id';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    /**
     * TEST 1: SuperAdmin dapat mengakses Audit Log Multi-Kanal dan melihat 15 kanal.
     */
    public function test_superadmin_can_access_multi_channel_audit_log_and_view_15_channels(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Auth,
            'channel' => 'auth-session',
            'severity' => 'info',
            'aksi' => 'User login berhasil',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.index'));

        $response->assertStatus(200);
        $response->assertSee('KANAL PERISTIWA');
        $response->assertSee('Semua Aktivitas Stream');
        $response->assertSee('Autentikasi & Sesi');
        $response->assertSee('Pendaftaran & Berkas');
        $response->assertSee('Verifikasi Admin');
        $response->assertSee('Penempatan & Mentor');
        $response->assertSee('Kuota & Periode');
        $response->assertSee('Tugas & Progres');
        $response->assertSee('Logbook & Presensi');
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Konfigurasi & Master');
        $response->assertSee('Peringatan Keamanan');

        // Pastikan kanal fitur yang telah ditiadakan tidak tampil di antarmuka
        $response->assertDontSee('Modul Kustom Dinamis');
        $response->assertDontSee('Penilaian & Sertifikat');
        $response->assertDontSee('Cadangan Basis Data');
    }

    /**
     * TEST 2: Filter Audit Log berdasarkan channel spesifik dan severity.
     */
    public function test_superadmin_can_filter_audit_log_by_specific_channel_and_severity(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'security-alert',
            'severity' => 'danger',
            'aksi' => 'Percobaan serangan brute-force terdeteksi',
            'waktu' => now(),
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Auth,
            'channel' => 'auth-session',
            'severity' => 'info',
            'aksi' => 'User normal login',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.channel', [
            'channel' => 'security-alert',
            'severity' => 'danger',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Percobaan serangan brute-force terdeteksi');
        $response->assertDontSee('User normal login');
    }

    /**
     * TEST 3: Ekspor Audit Log CSV streaming tersanitasi.
     */
    public function test_superadmin_can_export_audit_log_by_channel_to_csv(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Auth,
            'channel' => 'auth-session',
            'severity' => 'info',
            'aksi' => 'Login SuperAdmin Ekspor',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.export', ['channel' => 'auth-session']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }

    /**
     * TEST 4: SuperAdmin dapat mengakses Telemetri Kesehatan Sistem dan melihat SLA Uptime banner.
     */
    public function test_superadmin_can_access_system_health_monitor_and_see_sla_banner(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('superadmin.health.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Telemetri Infrastruktur');
        $response->assertSee('ALL NOMINAL');
        $response->assertSee('DB Ping');
        $response->assertSee('Host');
        $response->assertSee('Aksi Pemeliharaan Proaktif');
    }

    /**
     * TEST 5: Menjalankan ke-4 aksi diagnostik 1-klik di Telemetri Kesehatan.
     */
    public function test_superadmin_can_execute_all_four_diagnostic_actions_successfully(): void
    {
        $this->actingAs($this->superAdmin);

        // Aksi 1: Security Sweep
        $resp1 = $this->post(route('superadmin.health.security-sweep'));
        $resp1->assertRedirect();
        $resp1->assertSessionHas('status_keberhasilan');

        // Aksi 2: Optimasi Basis Data
        $resp2 = $this->post(route('superadmin.health.optimize-db'));
        $resp2->assertRedirect();
        $resp2->assertSessionHas('status_keberhasilan');

        // Aksi 3: Bersihkan Cache Sistem
        $resp3 = $this->post(route('superadmin.health.clear-cache'));
        $resp3->assertRedirect();
        $resp3->assertSessionHas('status_keberhasilan');

        // Aksi 4: Uji Izin Tulis Storage Disk
        $resp4 = $this->post(route('superadmin.health.test-storage'));
        $resp4->assertRedirect();
        $resp4->assertSessionHas('status_keberhasilan');
    }

    /**
     * TEST 6: Verifikasi bahwa rute modul builder dan modul dinamis sudah ditiadakan (404 Not Found).
     */
    public function test_modul_builder_and_dynamic_module_routes_are_decommissioned(): void
    {
        $this->actingAs($this->superAdmin);

        $response1 = $this->get('/superadmin/modul-builder');
        $response1->assertStatus(404);

        $response2 = $this->get('/superadmin/modul/inventaris-laptop');
        $response2->assertStatus(404);

        $response3 = $this->get('/publik/modul/kunjungan-tamu');
        $response3->assertStatus(404);
    }
}
