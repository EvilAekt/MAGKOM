<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeltaDashboardEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;
    protected Penempatan $penempatan;
    protected BulanMagang $bulan;
    protected Jurusan $jurusan;
    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
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
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 2,
            'aktif' => true,
        ]);

        // 1. SuperAdmin
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'SuperAdmin Test';
        $this->superAdmin->email = 'superadmin.delta@slemankab.go.id';
        $this->superAdmin->password = bcrypt('Password123!');
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        // 2. Admin
        $this->admin = new User();
        $this->admin->nama = 'Admin Test';
        $this->admin->email = 'admin.delta@slemankab.go.id';
        $this->admin->password = bcrypt('Password123!');
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->must_change_password = false;
        $this->admin->save();

        // 3. Pembimbing
        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Test';
        $this->pembimbing->email = 'pembimbing.delta@slemankab.go.id';
        $this->pembimbing->password = bcrypt('Password123!');
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->bidang_id = $bidang->id;
        $this->pembimbing->save();

        // 4. Peserta
        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Delta';
        $this->peserta->email = 'peserta.delta@student.ac.id';
        $this->peserta->password = bcrypt('Password123!');
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '21000189',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
        ]);
    }

    /**
     * Fase 1: Detail Penempatan Individual (A9).
     */
    public function test_admin_dan_superadmin_bisa_mengakses_detail_penempatan(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.penempatan.show', $this->penempatan));
        $responseAdmin->assertOk();
        $responseAdmin->assertViewIs('admin.penempatan.show');
        $responseAdmin->assertSee('Detail Penempatan Peserta');
        $responseAdmin->assertSee('Peserta Delta');

        $responseSa = $this->actingAs($this->superAdmin)->get(route('admin.penempatan.show', $this->penempatan));
        $responseSa->assertOk();

        // Non-admin dilarang mengakses
        $responsePembimbing = $this->actingAs($this->pembimbing)->get(route('admin.penempatan.show', $this->penempatan));
        $responsePembimbing->assertForbidden();

        $responsePeserta = $this->actingAs($this->peserta)->get(route('admin.penempatan.show', $this->penempatan));
        $responsePeserta->assertForbidden();
    }

    /**
     * Fase 1 & 2: Index penempatan merender link Detail, dan Sidebar merender Sertifikat & Survei.
     */
    public function test_sidebar_dan_index_merender_link_menu_delta(): void
    {
        // Index penempatan memiliki link detail
        $responseIndex = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee(route('admin.penempatan.show', $this->penempatan));

        // Sidebar Admin memiliki link Sertifikat dan Survei
        $responseAdminDash = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $responseAdminDash->assertOk();
        $responseAdminDash->assertSee('Sertifikat');
        $responseAdminDash->assertSee(route('admin.sertifikat.index'));
        $responseAdminDash->assertSee('Survei Kepuasan');
        $responseAdminDash->assertSee(route('admin.survei.index'));

        // Sidebar SuperAdmin memiliki sub-menu Sertifikat dan Survei
        $responseSaDash = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $responseSaDash->assertOk();
        $responseSaDash->assertSee('Sertifikat');
        $responseSaDash->assertSee(route('admin.sertifikat.index'));
    }

    /**
     * Fase 3: Admin Dashboard Quick Stats Enhancement (A11).
     */
    public function test_admin_dashboard_merender_quick_stats_enhancement(): void
    {
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => \App\Enums\StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now()->subDays(3),
            'diputuskan_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertViewHas(['avgVerifikasiHari', 'realisasiKuotaPersen', 'pendaftaranMingguIni']);
        $response->assertSee('Kecepatan Verifikasi Berkas');
        $response->assertSee('Tingkat Keterisian Kuota');
        $response->assertSee('Pendaftar 7 Hari Terakhir');
        $this->assertEquals(2.0, $response->viewData('avgVerifikasiHari'));
    }

    /**
     * Fase 4: Pembimbing Peserta Show merender chart visualisasi (B9).
     */
    public function test_pembimbing_peserta_show_merender_chart_tugas_dan_logbook(): void
    {
        // Buat tugas untuk peserta
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Integrasi API Dashboard',
            'deskripsi' => 'Kerjakan integrasi modul',
            'kategori' => 'Pengembangan Sistem',
            'kompleksitas' => \App\Enums\KompleksitasEnum::Sedang,
            'status' => StatusTugasEnum::Selesai,
            'deadline' => now()->addDays(5),
        ]);

        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.peserta.show', $this->penempatan));
        $response->assertOk();
        $response->assertViewHas(['tugasStats', 'completionRate', 'logbookPerMinggu']);
        $response->assertSee('Komposisi & Rasio Tugas Peserta');
        $response->assertSee('Pelacakan Mingguan Logbook Magang');
    }

    /**
     * Fase 5: Pembimbing Dashboard merender badge count dan aktivitas mentoring (B10, B11).
     */
    public function test_pembimbing_dashboard_merender_badge_dan_aktivitas_minggu_ini(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.dashboard'));
        $response->assertOk();
        $response->assertViewHas(['progresBaruCount', 'logbookPerluReviewCount', 'aktivitasMingguIni']);
        $response->assertSee('Aktivitas Mentoring Minggu Ini');
    }

    /**
     * Fase 6: SuperAdmin Dashboard merender telemetri sistem dan tren pendaftar (C11, C13).
     */
    public function test_superadmin_dashboard_merender_telemetri_sistem_dan_tren_pendaftar(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();
        $response->assertViewHas(['systemSummary', 'trenPendaftar']);
        $response->assertSee('Telemetri Infrastruktur & Sistem');
        $response->assertSee('Tren Pendaftaran 12 Bulan Terakhir');
    }

    /**
     * Fase 7: SuperAdmin Arsip Show merender tabbed view dan mencatat audit trail (C9).
     */
    public function test_superadmin_arsip_show_merender_tabbed_view_dan_mencatat_audit_log(): void
    {
        $this->penempatan->update(['status' => StatusPenempatanEnum::Selesai]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.arsip.show', $this->penempatan));
        $response->assertOk();
        $response->assertSee('Ringkasan & Biodata');
        $response->assertSee('Riwayat Tugas');
        $response->assertSee('Logbook Harian');
        $response->assertSee('Rubrik Penilaian Resmi');
        $response->assertSee('Dokumen Pendaftaran');

        // Verifikasi jejak audit trail tercatat
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'entitas_id' => $this->penempatan->id,
        ]);
    }
}
