<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
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
use Tests\TestCase;

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
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
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
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Admin MagKom';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User();
        $this->admin->nama = 'Admin Dinas';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Lapangan Aptika';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Magang Test';
        $this->peserta->email = 'peserta@gmail.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
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
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->endOfMonth(),
        ]);

        // Pendaftaran 2: Jalur Khusus
        $userPeserta2 = User::create([
            'nama' => 'Peserta Jalur Khusus',
            'email' => 'peserta2@gmail.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
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
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->endOfMonth(),
        ]);

        // Pendaftaran 3: Mengundurkan Diri
        $userPeserta3 = User::create([
            'nama' => 'Peserta Mundur',
            'email' => 'peserta3@gmail.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
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
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->endOfMonth(),
        ]);

        // Pendaftaran 4: Diajukan (Menunggu Seleksi)
        $userPeserta4 = User::create([
            'nama' => 'Peserta Antre Diajukan',
            'email' => 'peserta4@gmail.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
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
            'waktu' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

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
        $resAdminDash = $this->actingAs($this->admin)->get(route('superadmin.dashboard'));
        $resAdminDash->assertForbidden();

        $resAdminAudit = $this->actingAs($this->admin)->get(route('superadmin.audit-log.index'));
        $resAdminAudit->assertForbidden();

        // Peserta
        $resPesertaDash = $this->actingAs($this->peserta)->get(route('superadmin.dashboard'));
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
            'waktu' => now()->subHours(2),
        ]);

        AuditLog::create([
            'request_id' => 'req-beta-002',
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::Auth,
            'aksi' => 'Otentikasi Login Berhasil',
            'entitas' => 'users',
            'entitas_id' => $this->admin->id,
            'ip' => '10.0.0.88',
            'waktu' => now()->subHour(),
        ]);

        // 1. Tanpa filter: keduanya muncul
        $resAll = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index'));
        $resAll->assertOk();
        $resAll->assertSee('Update Status Pendaftaran Khusus');
        $resAll->assertSee('Otentikasi Login Berhasil');

        // 2. Filter keyword q: mencari "req-alpha"
        $resQ = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index', ['q' => 'req-alpha']));
        $resQ->assertOk();
        $resQ->assertSee('Update Status Pendaftaran Khusus');
        $resQ->assertDontSee('Otentikasi Login Berhasil');

        // 3. Filter tipe: "auth"
        $resTipe = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index', ['tipe' => 'auth']));
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
            'waktu' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.export'));

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
}
