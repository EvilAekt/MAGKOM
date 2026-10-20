<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\SurveiKepuasan;
use App\Models\User;
use App\Services\DocumentVerificationService;
use Carbon\Carbon;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdvancedAdminAndSuperAdminPhaseFiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $peserta;

    protected User $pembimbing;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Bidang $bidang;

    protected DocumentVerificationService $verificationService;

    protected function createTestUser(string $nama, string $email, RoleEnum $role, bool $statusAktif = true): User
    {
        $u = new User;
        $u->nama = $nama;
        $u->email = $email;
        $u->password = 'Password123!';
        $u->role = $role;
        $u->status_aktif = $statusAktif;
        $u->must_change_password = false;
        $u->email_verified_at = now();
        $u->save();

        return $u;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'pt',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => Carbon::parse('2026-11-01'),
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Pengembangan e-government',
        ]);

        $this->superAdmin = $this->createTestUser('Super Administrator', 'superadmin@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->admin = $this->createTestUser('Admin Dinas', 'admin@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing = $this->createTestUser('Pembimbing ASN', 'pembimbing@slemankab.go.id', RoleEnum::Pembimbing);
        $this->peserta = $this->createTestUser('Budi Santoso', 'budi@ugm.ac.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '21/123456/TK/54321',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, D.I. Yogyakarta',
        ]);

        $this->verificationService = app(DocumentVerificationService::class);
    }

    protected function createPendaftaran(User $user, StatusPendaftaranEnum $status = StatusPendaftaranEnum::Diajukan, ?Carbon $waktu = null): Pendaftaran
    {
        $time = $waktu ?? now();

        $p = Pendaftaran::create([
            'user_id' => $user->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => $status,
            'diajukan_at' => $time,
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        return $p;
    }

    protected function createPenempatan(Pendaftaran $pendaftaran, StatusPenempatanEnum $status = StatusPenempatanEnum::Selesai): Penempatan
    {
        return Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $pendaftaran->user_id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->subMonths(1),
            'tgl_selesai' => now(),
            'status' => $status,
        ]);
    }

    /**
     * TEST 1: Portal Verifikasi Dokumen Publik QR Code
     */
    public function test_dokumen_verifikasi_terbit_dan_dapat_diakses_publik_tanpa_login(): void
    {
        $pendaftaran = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);
        $penempatan = $this->createPenempatan($pendaftaran, StatusPenempatanEnum::Aktif);

        $doc = $this->verificationService->getOrCreateVerification(
            $penempatan,
            'surat_diterima',
            'SK/KOMINFO/2026/001'
        );

        $this->assertDatabaseHas('dokumen_verifikasi', [
            'id' => $doc->id,
            'kode_unik' => $doc->kode_unik,
            'status_keabsahan' => 'sah',
            'nomor_dokumen' => 'SK/KOMINFO/2026/001',
        ]);

        // Akses publik tanpa autentikasi
        $response = $this->get('/verifikasi-dokumen/'.$doc->kode_unik);

        $response->assertStatus(200);
        $response->assertSee('Surat Keterangan Diterima Magang');
        $response->assertSee('Budi Santoso');
        $response->assertSee('SK/KOMINFO/2026/001');
        $response->assertSee('Dokumen Resmi Terverifikasi');

        // Pastikan NFR-S6: Tidak mengekspos no HP atau alamat lengkap pribadi di halaman publik
        $response->assertDontSee('081234567890');
    }

    public function test_verifikasi_dokumen_dengan_token_invalid_mengembalikan_404(): void
    {
        $response = $this->get('/verifikasi-dokumen/MK-VRF-INVALID123');
        $response->assertStatus(404);
    }

    public function test_verifikasi_dokumen_yang_dibatalkan_menampilkan_status_batal(): void
    {
        $pendaftaran = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Dibatalkan);
        $penempatan = $this->createPenempatan($pendaftaran, StatusPenempatanEnum::MengundurkanDiri);

        $doc = $this->verificationService->getOrCreateVerification(
            $penempatan,
            'sertifikat_magang',
            'SERT/2026/999'
        );
        $doc->update(['status_keabsahan' => 'dicabut']);

        $response = $this->get('/verifikasi-dokumen/'.$doc->kode_unik);

        $response->assertStatus(200);
        $response->assertSee('DICABUT');
    }

    /**
     * TEST 2: Dashboard Analitik Tahunan & Penegakan A-20
     */
    public function test_admin_dapat_melihat_analitik_tahunan_tetapi_tidak_bisa_melihat_rekap_nilai(): void
    {
        $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);

        $response = $this->actingAs($this->admin)->get('/admin/laporan/tahunan?tahun=2026');

        $response->assertStatus(200);
        $response->assertSee('Tren Penyerapan Bulanan');
        $response->assertSee('Universitas Gadjah Mada');

        // Asumsi A-20: Admin tidak boleh melihat sebaran nilai
        $response->assertDontSee('Sebaran Predikat');
        $response->assertSee('Rekapitulasi Mutu Nilai Dilindungi');
    }

    public function test_superadmin_dapat_melihat_distribusi_predikat_nilai_di_analitik_tahunan(): void
    {
        $pendaftaran = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);
        $penempatan = $this->createPenempatan($pendaftaran, StatusPenempatanEnum::Selesai);

        Penilaian::create([
            'penempatan_id' => $penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'nilai_akhir' => 95.0,
            'predikat' => 'A',
            'catatan' => 'Sangat kompeten',
            'status' => StatusPenilaianEnum::Final,
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/admin/laporan/tahunan?tahun=2026');

        $response->assertStatus(200);
        $response->assertSee('Sebaran Predikat');
        $response->assertSee('SuperAdmin Only (A-20)');
    }

    public function test_export_analitik_tahunan_menghasilkan_csv_valid(): void
    {
        $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);

        $response = $this->actingAs($this->admin)->get('/admin/laporan/tahunan/export?tahun=2026');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Indikator / Bulan', $response->streamedContent());
        $this->assertStringContainsString('TOTAL TAHUN 2026', $response->streamedContent());
    }

    /**
     * TEST 3: Survei Kepuasan Magang Peserta & Dashboard Admin
     */
    public function test_peserta_selesai_dapat_mengisi_survei_kepuasan(): void
    {
        $pendaftaran = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);
        $penempatan = $this->createPenempatan($pendaftaran, StatusPenempatanEnum::Selesai);

        $showResponse = $this->actingAs($this->peserta)->get('/peserta/survei');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Kuesioner Evaluasi');

        $storeResponse = $this->actingAs($this->peserta)->post('/peserta/survei', [
            'skor_mentoring' => 5,
            'skor_tugas' => 5,
            'skor_fasilitas' => 4,
            'skor_nps' => 10,
            'umpan_balik' => 'Pembimbing sangat ramah dan supportif.',
            'saran' => 'Fasilitas internet meja sudut mohon diperkuat.',
        ]);

        $storeResponse->assertRedirect(route('peserta.survei.show'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('survei_kepuasan', [
            'penempatan_id' => $penempatan->id,
            'skor_mentoring' => 5,
            'skor_nps' => 10,
            'umpan_balik' => 'Pembimbing sangat ramah dan supportif.',
        ]);
    }

    public function test_peserta_yang_sudah_mengisi_survei_menampilkan_data_tersimpan(): void
    {
        $pendaftaran = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);
        $penempatan = $this->createPenempatan($pendaftaran, StatusPenempatanEnum::Selesai);

        SurveiKepuasan::create([
            'penempatan_id' => $penempatan->id,
            'skor_mentoring' => 5,
            'skor_tugas' => 4,
            'skor_fasilitas' => 4,
            'skor_nps' => 9,
            'umpan_balik' => 'Sangat mendidik',
            'saran' => 'Pertahankan',
        ]);

        $response = $this->actingAs($this->peserta)->get('/peserta/survei');
        $response->assertStatus(200);
        $response->assertSee('Sangat mendidik');
    }

    public function test_admin_dapat_melihat_dashboard_survei_dan_export(): void
    {
        $pendaftaran = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diterima);
        $penempatan = $this->createPenempatan($pendaftaran, StatusPenempatanEnum::Selesai);

        SurveiKepuasan::create([
            'penempatan_id' => $penempatan->id,
            'skor_mentoring' => 5,
            'skor_tugas' => 4,
            'skor_fasilitas' => 5,
            'skor_nps' => 9,
            'umpan_balik' => 'Pengalaman luar biasa di Dinas Kominfo',
            'saran' => 'Sudah sangat optimal',
        ]);

        $indexResponse = $this->actingAs($this->admin)->get('/admin/survei');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Indeks Kepuasan Magang');
        $indexResponse->assertSee('Net Promoter Score (NPS)');
        $indexResponse->assertSee('Pengalaman luar biasa di Dinas Kominfo');

        $exportResponse = $this->actingAs($this->admin)->get('/admin/survei/export');
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Pengalaman luar biasa di Dinas Kominfo', $exportResponse->streamedContent());
    }

    /**
     * TEST 4: SLA Watchdog Antrean Verifikasi Admin
     */
    public function test_sla_watchdog_memfilter_antrean_berdasarkan_keterlambatan(): void
    {
        // 1 pendaftaran breach (> 5 hari lalu)
        $pesertaBreach = $this->createTestUser('Peserta Lama', 'lama@example.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $pesertaBreach->id,
            'nim_nisn' => '11111111',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '0899999999',
            'alamat' => 'Sleman',
        ]);
        $pendaftaranBreach = $this->createPendaftaran($pesertaBreach, StatusPendaftaranEnum::Diajukan, now()->subDays(6));

        // 1 pendaftaran aman (hari ini)
        $pendaftaranAman = $this->createPendaftaran($this->peserta, StatusPendaftaranEnum::Diajukan, now());

        // Cek filter breach
        $responseBreach = $this->actingAs($this->admin)->get('/admin/pendaftaran?sla=breach');
        $responseBreach->assertStatus(200);
        $responseBreach->assertSee($pendaftaranBreach->nomor_pendaftaran);
        $responseBreach->assertDontSee($pendaftaranAman->nomor_pendaftaran);

        // Cek filter aman
        $responseAman = $this->actingAs($this->admin)->get('/admin/pendaftaran?sla=aman');
        $responseAman->assertStatus(200);
        $responseAman->assertSee($pendaftaranAman->nomor_pendaftaran);
        $responseAman->assertDontSee($pendaftaranBreach->nomor_pendaftaran);
    }

    /**
     * TEST 5: Pemeliharaan Retensi Otomatis Audit Log (NFR-A5)
     */
    public function test_command_audit_purge_hanya_menghapus_log_akses_data_dan_menyimpan_log_aksi_dan_auth(): void
    {
        // Buat log lampau (> 365 hari lalu)
        $oldAksesData = AuditLog::create([
            'request_id' => (string) Str::uuid(),
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::AksesData,
            'aksi' => 'akses.profil',
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'waktu' => now()->subDays(400),
        ]);

        $oldAuth = AuditLog::create([
            'request_id' => (string) Str::uuid(),
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::Auth,
            'aksi' => 'auth.login',
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'waktu' => now()->subDays(400),
        ]);

        $oldAksi = AuditLog::create([
            'request_id' => (string) Str::uuid(),
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'mutasi.penempatan',
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'waktu' => now()->subDays(400),
        ]);

        // Buat log akses data baru (< 30 hari lalu)
        $newAksesData = AuditLog::create([
            'request_id' => (string) Str::uuid(),
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::AksesData,
            'aksi' => 'akses.pendaftaran',
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'waktu' => now()->subDays(10),
        ]);

        // Eksekusi artisan command purge dengan default 365 hari
        $this->artisan('magkom:audit-purge', ['--days' => 365])
            ->assertExitCode(0);

        // Verifikasi log akses_data lama TERHAPUS
        $this->assertDatabaseMissing('audit_log', ['id' => $oldAksesData->id]);

        // Verifikasi log akses_data baru MASIH ADA
        $this->assertDatabaseHas('audit_log', ['id' => $newAksesData->id]);

        // Verifikasi NFR-A5: Log tipe auth dan aksi lama TETAP ADA
        $this->assertDatabaseHas('audit_log', ['id' => $oldAuth->id]);
        $this->assertDatabaseHas('audit_log', ['id' => $oldAksi->id]);
    }

    public function test_superadmin_dapat_memicu_pembersihan_audit_log_dari_antarmuka_pengaturan(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/superadmin/pengaturan/purge-audit');

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');
    }
}
