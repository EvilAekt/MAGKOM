<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalitikTahunanVisualTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $peserta;

    protected User $pembimbing;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Jurusan::count() === 0 || BulanMagang::count() === 0) {
            $this->seed();
        }

        $this->bulan = BulanMagang::first();
        $this->jurusan = Jurusan::first();
        $this->kuota = Kuota::where('bulan_magang_id', $this->bulan->id)->where('jurusan_id', $this->jurusan->id)->first() ?? Kuota::first();

        $this->admin = $this->createTestUser('Admin Analitik', 'admin.analitik@slemankab.go.id', RoleEnum::Admin);
        $this->superAdmin = $this->createTestUser('SuperAdmin Analitik', 'superadmin.analitik@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->pembimbing = $this->createTestUser('Pembimbing ASN', 'pembimbing.analitik@slemankab.go.id', RoleEnum::Pembimbing);
        $this->peserta = $this->createTestUser('Budi Santoso', 'budi.analitik@gmail.com', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '21/123456/TK/54321',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, D.I. Yogyakarta',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    protected function createTestUser(string $nama, string $email, RoleEnum $role): User
    {
        $u = new User;
        $u->nama = $nama;
        $u->email = $email;
        $u->password = 'Password123!';
        $u->role = $role;
        $u->status_aktif = true;
        $u->must_change_password = false;
        $u->email_verified_at = now();
        $u->save();

        return $u;
    }

    public function test_admin_can_view_analitik_tahunan_page_with_plan_growth_and_modern_buttons(): void
    {
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tgl_mulai_daftar' => '2026-10-01',
            'tgl_selesai_daftar' => '2026-12-31',
            'created_at' => Carbon::parse('2026-10-02 09:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/laporan/tahunan?tahun=2026');

        $response->assertOk();

        // 1. Verifikasi Toolbar & Tombol Elegan
        $response->assertSee('Hub Laporan Utama');
        $response->assertSee('btn-tahunan-hub', false);
        $response->assertSee('Unduh Rekap CSV');
        $response->assertSee('btn-tahunan-unduh', false);

        // 2. Verifikasi 4 Hero KPI Cards dengan Top Accent Border
        $response->assertSee('card-pendaftar', false);
        $response->assertSee('card-diterima', false);
        $response->assertSee('card-alumni', false);
        $response->assertSee('card-durasi', false);
        $response->assertSee('Total Pendaftar Masuk');
        $response->assertSee('Peserta Diterima Magang');
        $response->assertSee('Peserta Selesai Magang');
        $response->assertSee('Rata-rata Durasi Magang');

        // 3. Verifikasi Grid 2 Kolom Berdampingan: Card Tabel Bulanan & Card Goals
        $response->assertSee('tahunan-mid-grid', false);
        $response->assertSee('tahunan-table-card', false);
        $response->assertSee('goals-card', false);
        $response->assertSee('Goals');
        $response->assertSee('Penyerapan Kuota Magang');
        $response->assertSee('Kelulusan Seleksi Pendaftar');
        $response->assertSee('Penyelesaian Masa Magang');
        $response->assertSee('Jangkauan Institusi Mitra');
        $response->assertSee('Cakupan Periode Aktif');
        $response->assertSee('goal-teal', false);
        $response->assertSee('goal-green', false);
        $response->assertSee('goal-blue', false);
        $response->assertSee('goal-amber', false);
        $response->assertSee('goal-purple', false);

        // 4. Verifikasi Standardisasi Label Selesai (Bebas dari Istilah Alumni/Lulus)
        $response->assertDontSee('Grafik Proporsi');
        $response->assertDontSee('proportional-bar-wrapper');
        $response->assertDontSee('Lulus / Selesai');
        $response->assertDontSee('Alumni Selesai');
        $response->assertDontSee('Alumni Magang Lulus');
        $response->assertSee('Tren Penyerapan Bulanan (Januari - Desember 2026)');
        $response->assertSee('Rasio Penerimaan');

        // 5. Verifikasi Proteksi Nilai A-20 untuk Admin
        $response->assertDontSee('Sebaran Predikat');
        $response->assertSee('Rekapitulasi Mutu Nilai Dilindungi');
    }

    public function test_superadmin_can_view_analitik_tahunan_with_predikat_mutu_nilai(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tgl_mulai_daftar' => '2026-10-01',
            'tgl_selesai_daftar' => '2026-12-31',
            'created_at' => Carbon::parse('2026-10-02 09:00:00'),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->jurusan->bidang_id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-10-01',
            'tgl_selesai' => '2026-12-31',
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/admin/laporan/tahunan?tahun=2026');

        $response->assertOk();
        $response->assertSee('Sebaran Predikat & Mutu Nilai', false);
        $response->assertSee('SuperAdmin Only (A-20)');
    }

    public function test_export_analitik_tahunan_csv_stream(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/laporan/tahunan/export?tahun=2026');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Indikator / Bulan', $response->streamedContent());
        $this->assertStringContainsString('TOTAL TAHUN 2026', $response->streamedContent());
    }
}
