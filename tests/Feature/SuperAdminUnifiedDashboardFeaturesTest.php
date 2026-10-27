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
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperAdminUnifiedDashboardFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Penempatan $penempatan;

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

        // 1. Akun SuperAdmin
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'SuperAdmin Eksekutif';
        $this->superAdmin->email = 'superadmin.unified@slemankab.go.id';
        $this->superAdmin->password = bcrypt('Password123!');
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        SlotKhusus::create([
            'institusi' => 'Universitas Gadjah Mada',
            'bulan_mulai' => $this->bulan->bulan_mulai,
            'jumlah' => 5,
            'terisi' => 1,
            'keterangan' => 'MoU Kerjasama Sleman',
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        // 2. Akun Admin
        $this->admin = new User;
        $this->admin->nama = 'Admin Operasional';
        $this->admin->email = 'admin.unified@slemankab.go.id';
        $this->admin->password = bcrypt('Password123!');
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->must_change_password = false;
        $this->admin->save();

        // 3. Akun Pembimbing
        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan Utama';
        $this->pembimbing->email = 'pembimbing.unified@slemankab.go.id';
        $this->pembimbing->password = bcrypt('Password123!');
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        // 4. Akun Peserta
        $this->peserta = new User;
        $this->peserta->nama = 'Budi Santoso';
        $this->peserta->email = 'budi.santoso@example.com';
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
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);

        // Pendaftaran Berkas Diajukan
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDay(),
        ]);

        // Penempatan Aktif
        $this->penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->superAdmin->id,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Tugas Aktif
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Pengembangan Modul Dashboard Terpadu',
            'deskripsi' => 'Rancang antarmuka dashboard eksekutif',
            'kategori' => 'Pengembangan Sistem',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => StatusTugasEnum::Proses,
        ]);

        // Logbook Menunggu Reviu
        Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Menyusun komponen tab controller dashboard',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Audit Log Entry
        AuditLog::create([
            'request_id' => 'req-test-unified-001',
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'Memperbarui parameter konfigurasi sistem MagKom',
            'entitas' => 'pengaturan',
            'entitas_id' => 1,
            'metode' => 'PUT',
            'rute' => '/superadmin/pengaturan',
            'status_http' => 200,
            'durasi_ms' => 25,
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit/Test',
            'waktu' => now(),
        ]);
    }

    /**
     * Memastikan hero section bersih tanpa toolbar pintasan yang redundant (Declutter UX).
     */
    public function test_dashboard_renders_hero_with_clean_decluttered_layout(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Eksekutif Tata Kelola MagKom');
        $response->assertSee('Dashboard SuperAdmin');

        // Memastikan baris pintasan dan card aksi cepat tidak dirender (Clean UX)
        $response->assertDontSee('Pintasan Tindakan:');
        $response->assertDontSee('Aksi cepat');
    }

    /**
     * Memastikan 4 Segmented Tabs ter-render dengan label dan role atribut yang tepat.
     */
    public function test_dashboard_renders_segmented_tab_controller(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Utama');
        $response->assertSee('Seleksi & Kuota', false);
        $response->assertSee('Pelaksanaan & Bimbingan', false);
        $response->assertSee('Tata Kelola Sistem & Laporan', false);

        // Memastikan id tab panel ada
        $response->assertSee('id="tab-ringkasan"', false);
        $response->assertSee('id="tab-seleksi"', false);
        $response->assertSee('id="tab-bimbingan"', false);
        $response->assertSee('id="tab-sistem"', false);
    }

    /**
     * Memastikan Tab 2 (Seleksi & Kuota) memuat antrean berkas tertunda FIFO dan utilisasi kuota.
     */
    public function test_tab_seleksi_renders_pending_queue_and_quota_utilization(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Antrean Verifikasi Berkas Tertunda (FIFO)');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('Teknik Informatika');
        $response->assertSee('Tinjau & Putuskan', false);
        $response->assertSee('Utilisasi Kuota per Jurusan');
        $response->assertSee('Antrean Waiting List (BR-05)');
        $response->assertSee('Slot Khusus Kemitraan Sleman');
    }

    /**
     * Memastikan Tab 3 (Pelaksanaan & Bimbingan) memuat metrik bimbingan dan beban pembimbing BR-13.
     */
    public function test_tab_bimbingan_renders_mentoring_metrics_and_supervision_load(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Tugas Aktif');
        $response->assertSee('Logbook Pending Reviu');
        $response->assertSee('Penilaian Final Selesai');
        $response->assertSee('Matriks Utilisasi Beban Pembimbing Lapangan (BR-13)');
        $response->assertSee('Pembimbing Lapangan Utama');
        $response->assertSee('1 Mentee');
        $response->assertSee('Toleransi Logbook Mundur');
        $response->assertSee('Maks. 7 Hari (BR-09)');
    }

    /**
     * Memastikan Tab 4 (Tata Kelola Sistem & Laporan) memuat Hub Ekspor 4 Laporan Dinas dan pintasan sistem.
     */
    public function test_tab_sistem_renders_instant_export_hub_and_governance_controls(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Hub Ekspor Instan Laporan Resmi Dinas (AC-17-1)');

        // Memastikan 4 Laporan Dinas tersedia dengan link ekspor CSV instan
        $response->assertSee('Laporan Pendaftaran');
        $response->assertSee('Laporan Penempatan');
        $response->assertSee('Realisasi Kuota');
        $response->assertSee('Rekapitulasi Nilai');
        $response->assertSee(route('admin.laporan.pendaftaran'));
        $response->assertSee(route('admin.laporan.penempatan'));
        $response->assertSee(route('admin.laporan.kuota'));
        $response->assertSee(route('admin.laporan.nilai'));

        // Memastikan kontrol sistem penting terhubung
        $response->assertSee('Matriks Peran & Hak Akses', false);
        $response->assertSee(route('superadmin.role.index'));
        $response->assertSee('Gudang Arsip Peserta Magang (F-12)');
        $response->assertSee(route('superadmin.arsip.index'));
        $response->assertSee('Jejak Audit Trail & Log Keamanan', false);
        $response->assertSee(route('superadmin.audit-log.index'));

        // Memastikan widget integritas infrastruktur (TDD Bab 10)
        $response->assertSee('Integritas Infrastruktur & Status Layanan (TDD Bab 10)', false);
        $response->assertSee('Koneksi Basis Data');
        $response->assertSee('Private Disk Storage');
        $response->assertSee('Audit Trail Append-Only');
    }

    /**
     * Memastikan SuperAdmin dapat beralih bulan magang secara dinamis via query ?bulan_id=.
     */
    public function test_dashboard_supports_dynamic_month_selection_query(): void
    {
        $bulanBaru = BulanMagang::create([
            'bulan_mulai' => now()->addMonths(2)->startOfMonth(),
            'tgl_buka' => now()->addMonth()->toDateString(),
            'tgl_tutup' => now()->addMonth()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        Kuota::create([
            'bulan_magang_id' => $bulanBaru->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 15,
            'terisi' => 0,
            'aktif' => true,
        ]);

        // Akses dashboard dengan parameter ?bulan_id=
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard', ['bulan_id' => $bulanBaru->id]));

        $response->assertStatus(200);
        $response->assertSee($bulanBaru->nama_bulan);
        $response->assertSee('0 / 15 (0%)');
    }

    /**
     * Memastikan dashboard menampilkan live landing page status badge dan indikator pagu kuota BR-01.
     */
    public function test_dashboard_renders_live_landing_page_status_and_quota_ceiling_indicator(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // Status live landing page (F-01, BR-04)
        $response->assertSee('Status Publik:');
        $response->assertSee('Pendaftaran Tersedia');

        // Kepatuhan Pagu Kuota Dinas Bulanan (BR-01)
        $response->assertSee('Kepatuhan Pagu Kuota Dinas (BR-01)');
        $response->assertSee('/ 12 Kursi', false);
    }

    /**
     * Memastikan dashboard menampilkan indikator peserta masa tenggang (F-12) dan status pending sandi staf (BR-15).
     */
    public function test_dashboard_renders_grace_period_and_pending_staff_password(): void
    {
        // Buat peserta berstatus selesai dengan masa tenggang aktif
        Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->subDay()->toDateString(),
            'tgl_ditetapkan_selesai' => now()->subDay()->toDateString(),
            'tgl_nonaktif' => now()->addDays(30)->toDateString(),
        ]);

        // Buat staf yang belum aktivasi sandi awal (BR-15)
        $stafBaru = new User;
        $stafBaru->nama = 'Staf Baru Belum Ganti Sandi';
        $stafBaru->email = 'staf.pending@slemankab.go.id';
        $stafBaru->password = bcrypt('Secret123!');
        $stafBaru->role = RoleEnum::Admin;
        $stafBaru->status_aktif = true;
        $stafBaru->must_change_password = true;
        $stafBaru->save();

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Masa Tenggang');
        $response->assertSee('Staf Pending Sandi Awal');
    }

    /**
     * Memastikan dashboard merender panel Pusat Metrik Keberhasilan Produk & KPI Dinas (PRD Bab 2.3).
     */
    public function test_dashboard_renders_prd_kpi_metrics_and_sla_indicators(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // Memastikan 4 metrik keberhasilan produk PRD Bab 2.3
        $response->assertSee('Pusat Metrik Keberhasilan Produk & KPI Dinas', false);
        $response->assertSee('PRD Bab 2.3');
        $response->assertSee('Integritas Kuota (Target 0)');
        $response->assertSee('SLA Verifikasi (Target ≤ 5 Hari)', false);
        $response->assertSee('Lembar Nilai Alumni (Target 100%)');
        $response->assertSee('Audit Trail (Target 100%)');
        $response->assertSee('0 Pelanggaran');
        $response->assertSee('100% Tercatat');
    }

    /**
     * Memastikan dashboard merender Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10 & TDD Bab 8-9).
     */
    public function test_dashboard_renders_system_security_monitoring_panel_and_threat_radar(): void
    {
        // Buat anomali login gagal terbaru
        AuditLog::create([
            'request_id' => (string) Str::uuid(),
            'user_id' => null,
            'tipe' => TipeAuditEnum::Auth,
            'aksi' => 'login_gagal',
            'status_http' => 401,
            'ip' => '192.168.1.105',
            'payload' => ['email' => 'intruder@threat.local'],
            'waktu' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // Memastikan komponen monitoring keamanan utama hadir
        $response->assertSee('Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)', false);
        $response->assertSee('Radar Anomali Login (24 Jam)');
        $response->assertSee('Higienitas Kredensial Staf (BR-15)');
        $response->assertSee('Vault Dokumen Privat (NFR-S2)');
        $response->assertSee('Audit Trail Imutabel (NFR-S9)');

        // Memastikan rekaman radar anomali ter-render dengan detail IP
        $response->assertSee('Radar Anomali Login Terakhir');
        $response->assertSee('192.168.1.105');

        // Memastikan distribusi 4 kategori audit trail PRD Bab 10.3 dan postur hardening
        $response->assertSee('Distribusi Audit Trail (PRD Bab 10.3)');
        $response->assertSee('Auth (Login/Sesi)');
        $response->assertSee('Aksi (Mutasi Data)');
        $response->assertSee('Service (Scheduler)');
        $response->assertSee('Akses Data (Unduh)');
        $response->assertSee('HTTP Headers: X-Frame-Options DENY & nosniff (NFR-S8)', false);
    }

    /**
     * Memastikan dashboard merender pengawasan tugas terlambat (overdue) dan pelacak off-boarding magang.
     */
    public function test_dashboard_renders_overdue_tasks_and_offboarding_tracker(): void
    {
        // 1. Peserta aktif yang mendekati akhir magang (5 hari lagi)
        $penempatanBaru = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->subMonths(2)->toDateString(),
            'tgl_selesai' => now()->addDays(5)->toDateString(),
        ]);

        // 2. Tugas overdue (terlambat)
        Tugas::create([
            'penempatan_id' => $penempatanBaru->id,
            'judul' => 'Audit Keamanan Modul Auth',
            'deskripsi' => 'Pengujian celah keamanan',
            'kategori' => 'individu',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->subDays(2)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);

        // Memastikan kartu pengawasan tugas bimbingan hadir
        $response->assertSee('Tugas Terlambat (Overdue)');
        $response->assertSee('Mendekati Deadline (≤ 3 Hari)', false);
        $response->assertSee('Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)');
        $response->assertSee('5 Hari Lagi');
    }
}
