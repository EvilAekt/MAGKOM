<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PesertaFullPortalFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;

    protected User $pembimbing;

    protected User $admin;

    protected Jurusan $jurusan;

    protected Bidang $bidang;

    protected BulanMagang $bulanMagang;

    protected Kuota $kuota;

    /**
     * @var array<int, string>
     */
    protected array $originalViewPaths = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalViewPaths = View::getFinder()->getPaths();
        $localViewsPath = realpath(__DIR__ . '/../../resources/views');
        if ($localViewsPath && is_dir($localViewsPath)) {
            View::getFinder()->setPaths([$localViewsPath]);
        }

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Pengembangan aplikasi dan layanan e-government',
            'aktif' => true,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'bulan_mulai' => '2027-01-01',
            'tgl_buka' => '2026-12-01',
            'tgl_tutup' => '2026-12-25',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->peserta = new User([
            'nama' => 'Ahmad Peserta',
            'email' => 'peserta.portal@magkom.test',
            'password' => Hash::make('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = Carbon::now();
        $this->peserta->save();

        $this->pembimbing = new User([
            'nama' => 'Budi Pembimbing',
            'email' => 'pembimbing.portal@magkom.test',
            'password' => Hash::make('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = Carbon::now();
        $this->pembimbing->save();

        $this->admin = new User([
            'nama' => 'Citra Admin',
            'email' => 'admin.portal@magkom.test',
            'password' => Hash::make('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->admin->role = RoleEnum::Admin;
        $this->admin->email_verified_at = Carbon::now();
        $this->admin->save();
    }

    protected function tearDown(): void
    {
        if (!empty($this->originalViewPaths)) {
            View::getFinder()->setPaths($this->originalViewPaths);
        }

        parent::tearDown();
    }

    /** @test */
    public function tamu_tidak_bisa_mengakses_dashboard_peserta(): void
    {
        $response = $this->get(URL::route('peserta.dashboard'));
        $response->assertRedirect(URL::route('login'));
    }

    /** @test */
    public function role_non_peserta_dilarang_mengakses_dashboard_peserta(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(URL::route('peserta.dashboard'));
        $response->assertStatus(403);

        $responseAdmin = $this->actingAs($this->admin)->get(URL::route('peserta.dashboard'));
        $responseAdmin->assertStatus(403);
    }

    /** @test */
    public function login_peserta_mengarahkan_ke_dashboard_peserta(): void
    {
        $response = $this->post(URL::route('login'), [
            'email' => $this->peserta->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(URL::route('peserta.dashboard'));
    }

    /** @test */
    public function peserta_baru_melihat_fase_onboarding_pada_dashboard(): void
    {
        $response = $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('peserta.dashboard');
        $response->assertSee('Ahmad Peserta');
        $response->assertSee('Panduan Alur Pendaftaran Magang');
        $response->assertSee('Lengkapi Biodata & Profil', false);
    }

    /** @test */
    public function peserta_dengan_pendaftaran_melihat_fase_seleksi(): void
    {
        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'institusi' => 'SMK Negeri 1 Sleman',
            'nim_nisn' => '12345678',
            'jenjang' => JenjangEnum::Smk,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234567890',
        ]);

        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Status Pendaftaran Magang Aktif');
        $response->assertSee('Diajukan');
        $response->assertSee('Buka Berkas & Detail Pendaftaran', false);
    }

    /** @test */
    public function peserta_aktif_melihat_fase_aktif_dan_kpi_statistik(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
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
            'tgl_mulai' => Carbon::now()->subDays(10)->toDateString(),
            'tgl_selesai' => Carbon::now()->addDays(50)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Membuat API Dashboard',
            'deskripsi' => 'Kerjakan controller dan view',
            'kategori' => 'Development',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::now()->addDays(5)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => Carbon::now()->subDay()->toDateString(),
            'kegiatan' => 'Mempelajari arsitektur database',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Status Penempatan: Magang Aktif');
        $response->assertSee('Budi Pembimbing');
        $response->assertSee('Membuat API Dashboard');
        $response->assertSee('Isi Logbook Mingguan');
    }

    /** @test */
    public function seluruh_halaman_portal_peserta_dapat_dibuka_sukses(): void
    {
        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'institusi' => 'SMK Negeri 1 Sleman',
            'nim_nisn' => '12345678',
            'jenjang' => JenjangEnum::Smk,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234567890',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
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
            'tgl_mulai' => Carbon::now()->subDays(10)->toDateString(),
            'tgl_selesai' => Carbon::now()->addDays(50)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $tugas = Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Tugas Pengujian Frontend',
            'deskripsi' => 'Testing tampilan peserta',
            'kategori' => 'Testing',
            'kompleksitas' => KompleksitasEnum::Rendah,
            'deadline' => Carbon::now()->addDays(3)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        // 1. Dashboard
        $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'))->assertOk();

        // 2. Profil
        $this->actingAs($this->peserta)->get(URL::route('peserta.profil.show'))->assertOk();

        // 3. Pendaftaran Index
        $this->actingAs($this->peserta)->get(URL::route('peserta.pendaftaran.index'))->assertOk();

        // 4. Pendaftaran Detail
        $this->actingAs($this->peserta)->get(URL::route('peserta.pendaftaran.detail', $pendaftaran))->assertOk();

        // 5. Logbook Index
        $this->actingAs($this->peserta)->get(URL::route('peserta.logbook.index'))->assertOk();

        // 6. Tugas Index
        $this->actingAs($this->peserta)->get(URL::route('peserta.tugas.index'))->assertOk();

        // 7. Tugas Show
        $this->actingAs($this->peserta)->get(URL::route('peserta.tugas.show', $tugas))->assertOk();

        // 8. Nilai Show
        $this->actingAs($this->peserta)->get(URL::route('peserta.nilai.show'))->assertOk();
    }

    /** @test */
    public function dashboard_peserta_memenuhi_standar_modernisasi_task_018(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
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
            'tgl_mulai' => Carbon::now()->subDays(10)->toDateString(),
            'tgl_selesai' => Carbon::now()->addDays(50)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Tugas Integrasi Frontend',
            'deskripsi' => 'Pengujian antarmuka peserta',
            'kategori' => 'Development',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::now()->addDays(5)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => Carbon::now()->subDay()->toDateString(),
            'kegiatan' => 'Implementasi UI DS-MagKom dashboard peserta',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'));
        $response->assertOk();
        $content = $response->getContent();

        // AC-1: Atmospheric Hero Banner Clean & Sleman Gold Badge
        $response->assertSee('saas-hero-banner', false);
        $response->assertSee('Pemkab Sleman');
        $response->assertSee('Status Penempatan: Magang Aktif');
        $this->assertStringNotContainsString('data:image/svg+xml', $content);

        // AC-2: 4 Kartu Metrik KPI Modern & Nilai Numerik Real
        $response->assertSee('saas-kpi-grid', false);
        $response->assertSee('saas-kpi-card', false);
        $response->assertSee('Logbook Aktivitas');
        $response->assertSeeInOrder(['1', 'Laporan']);
        $response->assertSee('Logbook Mingguan tercatat rapi');
        $response->assertSee('Tugas Magang');
        $response->assertSeeInOrder(['1', 'Aktif']);
        $response->assertSee('Sisa Hari Magang');
        $response->assertSee('Hari');
        $response->assertSee('Status Evaluasi / Sertifikat');
        $response->assertSee('Budi Pembimbing');

        // AC-4: Aside Context Profil Pembimbing, Aksi Cepat & Ketentuan Logbook (BR-09)
        $response->assertSee('saas-aside-card', false);
        $response->assertSee('Pembimbing Lapangan');
        $response->assertSee('Budi Pembimbing');
        $response->assertSee($this->pembimbing->email);
        $response->assertSee('Aksi Cepat Peserta');
        $response->assertSee('saas-quick-grid', false);
        $response->assertSee('Ketentuan Logbook (BR-09)');

        // AC-5: Pure CSS & Zero Inline Hover JS
        $this->assertStringNotContainsString('onmouseover', strtolower($content));
        $this->assertStringNotContainsString('onmouseout', strtolower($content));
        $response->assertSee('[data-theme="dark"]', false);
    }

    /** @test */
    public function peserta_aktif_dengan_pembimbing_tanpa_hp_menampilkan_fallback_helpdesk(): void
    {
        $pembimbingTanpaHp = new User([
            'nama' => 'Pembimbing Tanpa Kontak',
            'email' => 'pembimbing.nokontak@kominfo.test',
            'password' => Hash::make('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $pembimbingTanpaHp->role = RoleEnum::Pembimbing;
        $pembimbingTanpaHp->email_verified_at = Carbon::now();
        $pembimbingTanpaHp->save();

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $pembimbingTanpaHp->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::now()->subDays(5)->toDateString(),
            'tgl_selesai' => Carbon::now()->addDays(55)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'));
        $response->assertOk();

        // Assert fallback aside & KPI bar
        $response->assertSee('Pembimbing Tanpa Kontak');
        $response->assertSee('Hubungi Helpdesk Magang');
    }

    /** @test */
    public function view_partial_aside_dan_kpi_aman_saat_relasi_pembimbing_null(): void
    {
        $penempatanTanpaPembimbing = new Penempatan([
            'tgl_selesai' => Carbon::now()->addDays(30)->toDateString(),
        ]);
        $penempatanTanpaPembimbing->id = 999;
        $penempatanTanpaPembimbing->setRelation('pembimbing', null);
        $penempatanTanpaPembimbing->setRelation('bidang', null);

        $renderedAside = View::make('peserta.dashboard.partials._aside', [
            'fase' => 'aktif',
            'penempatan' => $penempatanTanpaPembimbing,
            'user' => $this->peserta,
        ])->render();

        $this->assertStringContainsString('Belum Ditugaskan', $renderedAside);
        $this->assertStringContainsString('Dinas Kominfo Sleman', $renderedAside);
        $this->assertStringContainsString('Hubungi Helpdesk Magang', $renderedAside);

        $renderedKpi = View::make('peserta.dashboard.partials._kpi_bar', [
            'penempatan' => $penempatanTanpaPembimbing,
            'totalLogbook' => 0,
            'logbookPerluRevisi' => 0,
            'tugasBelum' => 0,
            'tugasSelesai' => 0,
            'sisaHari' => 30,
            'isFinalNilai' => false,
        ])->render();

        $this->assertStringContainsString('Tim Diskominfo', $renderedKpi);
    }

    /** @test */
    public function peserta_fase_selesai_melihat_predikat_nilai_dan_tombol_unduh(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
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
            'tgl_mulai' => Carbon::now()->subDays(90)->toDateString(),
            'tgl_selesai' => Carbon::now()->subDays(5)->toDateString(),
            'status' => StatusPenempatanEnum::Selesai,
        ]);

        Penilaian::create([
            'penempatan_id' => $penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'nilai_akhir' => 92.50,
            'predikat' => 'A',
            'catatan' => 'Sangat berdedikasi dan performa istimewa selama magang.',
            'status' => StatusPenilaianEnum::Final,
        ]);

        $response = $this->actingAs($this->peserta)->get(URL::route('peserta.dashboard'));
        $response->assertOk();

        // Assert konten fase selesai
        $response->assertSee('Penyelesaian Program Magang Diskominfo Sleman');
        $response->assertSee('Lulus &amp; Selesai', false);
        $response->assertSeeInOrder(['Predikat Akhir', 'A']);
        $response->assertSee('92.50');
        $response->assertSee('Sangat berdedikasi dan performa istimewa selama magang.');
        $response->assertSee(URL::route('peserta.nilai.show'));
        $response->assertSee(URL::route('peserta.nilai.unduh'));
    }
}
