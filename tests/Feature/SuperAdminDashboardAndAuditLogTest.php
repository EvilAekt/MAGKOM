<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

// Pastikan controller dan resource views dari worktree di-load terlebih dahulu
$worktreeRoot = dirname(__DIR__, 2);
if (file_exists($worktreeRoot . '/app/Http/Controllers/SuperAdmin/AuditLogController.php')) {
    require_once $worktreeRoot . '/app/Http/Controllers/SuperAdmin/AuditLogController.php';
}

class SuperAdminDashboardAndAuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Bidang $bidang;

    protected Kuota $kuota;

    /**
     * Pastikan aplikasi berjalan penuh di root worktree saat ini.
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $worktreeRoot = dirname(__DIR__, 2);
        putenv('APP_BASE_PATH=' . $worktreeRoot);
        $_ENV['APP_BASE_PATH'] = $worktreeRoot;

        $app = require $worktreeRoot . '/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $app['config']->set('view.paths', [$worktreeRoot . '/resources/views']);
        $app['view.finder']->setPaths([$worktreeRoot . '/resources/views']);

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Setup Master
        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan dan tata kelola sistem aplikasi',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => Carbon::now()->startOfMonth(),
            'tgl_buka' => Carbon::now()->subDays(10)->toDateString(),
            'tgl_tutup' => Carbon::now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 15,
            'terisi' => 2,
            'aktif' => true,
        ]);

        // 2. Setup Pengguna
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Admin MagKom';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = Carbon::now();
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Dinas';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = Carbon::now();
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan Aptika';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = Carbon::now();
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang Test';
        $this->peserta->email = 'peserta@gmail.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = Carbon::now();
        $this->peserta->save();
    }

    /**
     * Uji Dashboard Eksekutif memuat seluruh metrik tanpa SQL error.
     */
    public function test_superadmin_can_access_executive_dashboard_with_all_metrics(): void
    {
        // Pendaftaran 1: Jalur Umum (Aktif di penempatan)
        $p1 = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        Penempatan::create([
            'pendaftaran_id' => $p1->id,
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => Carbon::now()->startOfMonth(),
            'tgl_selesai' => Carbon::now()->endOfMonth(),
        ]);

        // Pendaftaran 2: Jalur Khusus
        $userPeserta2 = User::create([
            'nama' => 'Peserta Jalur Khusus',
            'email' => 'peserta2@gmail.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => Carbon::now(),
        ]);

        $p2 = Pendaftaran::create([
            'user_id' => $userPeserta2->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        Penempatan::create([
            'pendaftaran_id' => $p2->id,
            'peserta_id' => $userPeserta2->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => Carbon::now()->startOfMonth(),
            'tgl_selesai' => Carbon::now()->endOfMonth(),
        ]);

        // Pendaftaran 3: Mengundurkan Diri
        $userPeserta3 = User::create([
            'nama' => 'Peserta Mundur',
            'email' => 'peserta3@gmail.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => Carbon::now(),
        ]);

        $p3 = Pendaftaran::create([
            'user_id' => $userPeserta3->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        Penempatan::create([
            'pendaftaran_id' => $p3->id,
            'peserta_id' => $userPeserta3->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPenempatanEnum::MengundurkanDiri,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => Carbon::now()->startOfMonth(),
            'tgl_selesai' => Carbon::now()->endOfMonth(),
        ]);

        // Pendaftaran 4: Diajukan (Menunggu Seleksi)
        $userPeserta4 = User::create([
            'nama' => 'Peserta Antre Diajukan',
            'email' => 'peserta4@gmail.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => Carbon::now(),
        ]);

        Pendaftaran::create([
            'user_id' => $userPeserta4->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
        ]);

        // Slot Khusus aktif periode ini
        SlotKhusus::create([
            'bulan_mulai' => $this->bulan->bulan_mulai,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        // Audit Log terbaru
        AuditLog::create([
            'request_id' => 'req-init-101',
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'Konfigurasi Kuota Periode Baru',
            'entitas' => 'kuota',
            'entitas_id' => $this->kuota->id,
            'nilai_lama' => null,
            'nilai_baru' => ['jumlah' => 15],
            'detail' => ['keterangan' => 'Penyesuaian daya tampung'],
            'metode' => 'POST',
            'rute' => '/superadmin/kuota',
            'status_http' => 200,
            'durasi_ms' => 45,
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit/Test',
            'waktu' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Eksekutif Tata Kelola MagKom');
        $response->assertSee('Jalur Umum');
        $response->assertSee('Jalur Khusus');
        $response->assertSee('Mengundurkan Diri');
        $response->assertSee('Perhatian Antrean Pendaftaran');
        $response->assertSee('Konfigurasi Kuota Periode Baru');
    }

    /**
     * Non-superadmin dilarang mengakses dashboard & audit log superadmin.
     */
    public function test_non_superadmin_forbidden_from_dashboard_and_audit_log(): void
    {
        // Admin biasa
        $resAdminDash = $this->actingAs($this->admin)->get(URL::route('superadmin.dashboard'));
        $resAdminDash->assertForbidden();

        $resAdminAudit = $this->actingAs($this->admin)->get(URL::route('superadmin.audit-log.index'));
        $resAdminAudit->assertForbidden();

        // Peserta
        $resPesertaDash = $this->actingAs($this->peserta)->get(URL::route('superadmin.dashboard'));
        $resPesertaDash->assertForbidden();
    }

    /**
     * Uji pencarian multi-kolom dan filter tipe pada Audit Log.
     */
    public function test_superadmin_can_filter_audit_logs_including_keyword_q(): void
    {
        AuditLog::create([
            'request_id' => 'req-alpha-001',
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'Update Status Pendaftaran Khusus',
            'entitas' => 'pendaftaran',
            'entitas_id' => 99,
            'ip' => '192.168.1.55',
            'waktu' => Carbon::now()->subHours(2),
        ]);

        AuditLog::create([
            'request_id' => 'req-beta-002',
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::Auth,
            'aksi' => 'Otentikasi Login Berhasil',
            'entitas' => 'users',
            'entitas_id' => $this->admin->id,
            'ip' => '10.0.0.88',
            'waktu' => Carbon::now()->subHour(),
        ]);

        // 1. Tanpa filter: keduanya muncul
        $resAll = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index'));
        $resAll->assertOk();
        $resAll->assertSee('Update Status Pendaftaran Khusus');
        $resAll->assertSee('Otentikasi Login Berhasil');

        // 2. Filter keyword q: mencari "req-alpha"
        $resQ = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index', ['q' => 'req-alpha']));
        $resQ->assertOk();
        $resQ->assertSee('Update Status Pendaftaran Khusus');
        $resQ->assertDontSee('Otentikasi Login Berhasil');

        // 3. Filter tipe: "auth"
        $resTipe = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index', ['tipe' => 'auth']));
        $resTipe->assertOk();
        $resTipe->assertSee('Otentikasi Login Berhasil');
        $resTipe->assertDontSee('Update Status Pendaftaran Khusus');
    }

    /**
     * Uji ekspor Audit Log ke format CSV dengan BOM UTF-8 dan sanitasi formula injection.
     */
    public function test_superadmin_can_export_audit_log_csv_with_sanitization_and_trail(): void
    {
        // Buat entri dengan karakter berisiko formula injection: =, +, @
        AuditLog::create([
            'request_id' => 'req-sec-999',
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => '=SUM(1,2)', // Harus tersanitasi menjadi '=SUM(1,2)
            'entitas' => '@users_table', // Harus tersanitasi menjadi '@users_table
            'entitas_id' => 123,
            'ip' => '+62811111111', // Harus tersanitasi menjadi '+62811111111
            'waktu' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // 1. Verifikasi UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        // 2. Verifikasi Header Baris
        $this->assertStringContainsString('Waktu (WIB)', $content);
        $this->assertStringContainsString('Request ID', $content);
        $this->assertStringContainsString('IP Address', $content);

        // 3. Verifikasi Formula Injection Sanitasi
        $this->assertStringContainsString("'=SUM(1,2)", $content);
        $this->assertStringContainsString("'@users_table", $content);
        $this->assertStringContainsString("'+62811111111", $content);

        // 4. Verifikasi bahwa ekspor ini sendiri dicatat di audit log
        $this->assertDatabaseHas('audit_log', [
            'tipe' => TipeAuditEnum::AksesData->value,
            'entitas' => 'audit_log',
        ]);
    }

    /**
     * Uji Redesign Jejak Audit SuperAdmin Paritas Canvas 5a4d7a8b & Export Popover Pola A (Node 68749aa8).
     */
    public function test_superadmin_audit_log_view_matches_canvas_pure_audit_redesign_parity(): void
    {
        AuditLog::create([
            'request_id' => 'req-parity-888',
            'user_id' => $this->superAdmin->id,
            'channel' => 'kuota-periode',
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'kuota.capacity_update',
            'entitas' => 'kuota',
            'entitas_id' => 12,
            'nilai_lama' => ['kuota' => 15],
            'nilai_baru' => ['kuota' => 20],
            'severity' => 'info',
            'ip' => '192.168.1.24',
            'waktu' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index'));

        $response->assertOk();

        // AC-1: Zero Telemetry & Pure Audit Header
        $response->assertSee('Jejak Audit &amp; Kepatuhan', false);
        $response->assertSee('Portal MagKom');
        $response->assertSee('SHA-256 Valid');
        $response->assertSee('Retensi 365 Hari');
        $response->assertDontSee('Pusat Telemetri Infrastruktur');
        $response->assertDontSee('DB Ping');

        // AC-2: 4 Hero KPI Metrics Strip
        $response->assertSee('Total Log (24 Jam)');
        $response->assertSee('Mutasi Data');
        $response->assertSee('Sesi Autentikasi');
        $response->assertSee('Peringatan Kritis');

        // AC-3: 15-Channel Navigator & Compliance Panel
        $response->assertSee('Kanal Audit');
        $response->assertSee('Semua Aktivitas');
        $response->assertSee('Autentikasi &amp; Sesi', false);
        $response->assertSee('Pendaftaran &amp; Berkas', false);
        $response->assertSee('Integritas Log');
        $response->assertSee('WORM Policy');
        $response->assertSee('ISO/IEC 27001');

        // AC-4: Stream Header & Filter Chips
        $response->assertSee('#all-actions-stream');
        $response->assertSee('Semua Aktor');
        $response->assertSee('Semua Entitas');

        // AC-5: High-Density Enterprise Table
        $response->assertSee('Waktu &amp; ID', false);
        $response->assertSee('Snapshot Diff');
        $response->assertSee('kuota.capacity_update');
        $response->assertSee('Audit Log Immutable');

        // AC-6: Forensic Diff Inspector & Test Compatibility
        $response->assertSee('Inspeksi Mutasi: #');
        $response->assertSee('Salin JSON');
        $response->assertSee('Bukti Forensik PDF');
        $response->assertSee('NILAI LAMA (BEFORE)');
        $response->assertSee('NILAI BARU (AFTER)');
        $response->assertSee('Nilai Lama (Sebelum Mutasi)');
        $response->assertSee('Nilai Baru (Setelah Mutasi)');
        $response->assertSee('modalLogDetail');

        // AC-7: Export Contextual Popover Pola A
        $response->assertSee('exportPopover');
        $response->assertSee('Ekspor Log Cepat');
        $response->assertSee('CSV Tabular');
        $response->assertSee('NDJSON SIEM Stream');
        $response->assertSee('Berita Acara Resmi');
        $response->assertSee('Sertakan Hash HMAC-SHA256');
        $response->assertSee('Sertakan Snapshot Diff Mutasi');
        $response->assertSee('Unduh CSV');
    }

    /**
     * Uji fitur pilihan menampilkan 10, 20, 30 entri per halaman dan navigasi lembar halaman (1, 2, 3, 4...).
     */
    public function test_superadmin_can_select_audit_log_per_page_options_and_navigate_lembar_pages(): void
    {
        DB::table('audit_log')->delete();

        $rows = [];
        for ($i = 1; $i <= 35; $i++) {
            $rows[] = [
                'request_id' => 'req-page-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'user_id' => $this->superAdmin->id,
                'tipe' => TipeAuditEnum::Aksi->value,
                'aksi' => 'Aksi audit log batch item #' . $i,
                'entitas' => 'kuota',
                'entitas_id' => $i,
                'ip' => '127.0.0.1',
                'waktu' => Carbon::now()->subMinutes(35 - $i),
                'channel' => 'all-actions-stream',
                'severity' => 'info',
            ];
        }
        DB::table('audit_log')->insert($rows);

        // 1. Default request (tanpa ?per_page): harus default ke 10 entri per halaman
        $responseDefault = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index'));
        $responseDefault->assertOk();
        $responseDefault->assertSee('Menampilkan:');
        $responseDefault->assertSee('Lembar Halaman');
        $responseDefault->assertSee('Lembar 1');
        $responseDefault->assertSee('Lembar 2');
        $responseDefault->assertSee('Lembar 3');
        $responseDefault->assertSee('Lembar 4');
        $responseDefault->assertSee('Aksi audit log batch item #35');
        $responseDefault->assertDontSee('Aksi audit log batch item #20');

        // 2. Request dengan per_page=20: 35 entri / 20 = 2 lembar halaman
        $response20 = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index', ['per_page' => 20]));
        $response20->assertOk();
        $response20->assertSee('Lembar Halaman <span class="font-bold text-slate-900 dark:text-white">1</span> dari <span class="font-bold text-slate-900 dark:text-white">2</span>', false);
        $response20->assertSee('Aksi audit log batch item #35');
        $response20->assertSee('Aksi audit log batch item #16');
        $response20->assertDontSee('Aksi audit log batch item #15');

        // 3. Request ke lembar halaman 2 dengan per_page=20
        $response20Page2 = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index', ['per_page' => 20, 'page' => 2]));
        $response20Page2->assertOk();
        $response20Page2->assertSee('Lembar Halaman <span class="font-bold text-slate-900 dark:text-white">2</span> dari <span class="font-bold text-slate-900 dark:text-white">2</span>', false);
        $response20Page2->assertSee('Aksi audit log batch item #15');
        $response20Page2->assertSee('Aksi audit log batch item #1');

        // 4. Request dengan per_page=30: 35 entri / 30 = 2 lembar halaman
        $response30 = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index', ['per_page' => 30]));
        $response30->assertOk();
        $response30->assertSee('Aksi audit log batch item #6');
        $response30->assertDontSee('Aksi audit log batch item #5');

        // 5. Parameter tidak valid (?per_page=999) otomatis fallback ke 10
        $responseInvalid = $this->actingAs($this->superAdmin)->get(URL::route('superadmin.audit-log.index', ['per_page' => 999]));
        $responseInvalid->assertOk();
        $responseInvalid->assertSee('Lembar Halaman <span class="font-bold text-slate-900 dark:text-white">1</span> dari <span class="font-bold text-slate-900 dark:text-white">4</span>', false);
    }
}
