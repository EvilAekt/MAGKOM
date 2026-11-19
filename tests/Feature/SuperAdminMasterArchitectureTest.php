<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertSee('Logbook');
        $response->assertDontSee('Logbook & Presensi');
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
     * TEST 4: Verifikasi bahwa rute telemetri kesehatan infrastruktur sudah ditiadakan secara permanen (404 Not Found).
     */
    public function test_telemetry_health_monitor_routes_are_decommissioned(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/superadmin/health-monitor');
        $response->assertStatus(404);

        $responseApi = $this->get('/superadmin/health-monitor/telemetry-data');
        $responseApi->assertStatus(404);

        $responseSweep = $this->post('/superadmin/health-monitor/security-sweep');
        $responseSweep->assertStatus(404);
    }

    /**
     * TEST 5: Verifikasi bahwa rute modul builder dan modul dinamis sudah ditiadakan (404 Not Found).
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

    /**
     * TEST 6: SuperAdmin dapat memfilter kanal logbook dan melihat log baru maupun warisan logbook-presensi.
     */
    public function test_superadmin_can_filter_audit_log_by_logbook_channel_and_see_legacy_logs(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'logbook',
            'severity' => 'info',
            'aksi' => 'Peserta mengisi logbook harian baru',
            'waktu' => now(),
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'logbook-presensi',
            'severity' => 'info',
            'aksi' => 'Pembimbing menyetujui logbook warisan lama',
            'waktu' => now()->subHour(),
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'security-alert',
            'severity' => 'danger',
            'aksi' => 'Percobaan intrusion terdeteksi',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.channel', [
            'channel' => 'logbook',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Peserta mengisi logbook harian baru');
        $response->assertSee('Pembimbing menyetujui logbook warisan lama');
        $response->assertDontSee('Percobaan intrusion terdeteksi');
    }

    /**
     * TEST 7: URL warisan /superadmin/audit-log/channel/logbook-presensi dipetakan ke kanal logbook.
     */
    public function test_legacy_logbook_presensi_url_maps_to_logbook_channel(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'logbook',
            'severity' => 'info',
            'aksi' => 'Logbook verifikasi mentor',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.channel', [
            'channel' => 'logbook-presensi',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Logbook verifikasi mentor');
        $response->assertViewHas('activeChannel', 'logbook');
    }

    /**
     * TEST 8: AuditService resolveChannel memetakan entitas dan teks logbook/presensi ke kanal logbook.
     */
    public function test_audit_service_resolves_logbook_channel(): void
    {
        /** @var AuditService $auditService */
        $auditService = app(AuditService::class);

        $this->assertSame('logbook', $auditService->resolveChannel('logbook', 'isi_jurnal'));
        $this->assertSame('logbook', $auditService->resolveChannel('progres_log', 'submit_mingguan'));
        $this->assertSame('logbook', $auditService->resolveChannel(null, 'peserta mengisi logbook'));
        $this->assertSame('logbook', $auditService->resolveChannel(null, 'konfirmasi presensi kehadiran'));
    }

    /**
     * TEST 9: Counter 24 jam kanal logbook mengakumulasikan data baru dan data legacy logbook-presensi.
     */
    public function test_superadmin_logbook_channel_24h_counter_accumulates_legacy_presensi_logs(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'logbook',
            'severity' => 'info',
            'aksi' => 'Peserta mengisi logbook hari ini',
            'waktu' => now()->subHours(2),
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'logbook-presensi',
            'severity' => 'info',
            'aksi' => 'Pembimbing verifikasi logbook presensi kemarin',
            'waktu' => now()->subHours(5),
        ]);

        $response = $this->get(route('superadmin.audit-log.index'));
        $response->assertStatus(200);

        /** @var array<string, array<string, mixed>> $channels */
        $channels = $response->viewData('channels');

        $this->assertArrayHasKey('logbook', $channels);
        $this->assertArrayNotHasKey('logbook-presensi', $channels);
        $this->assertSame(2, $channels['logbook']['count_24h']);
    }
}
