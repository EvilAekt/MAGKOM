<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PesertaProfil;
use App\Models\Rubrik;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    protected function setUp(): void
    {
        parent::setUp();

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
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        $this->pembimbing = new User([
            'nama' => 'Budi Pembimbing',
            'email' => 'pembimbing.portal@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->admin = new User([
            'nama' => 'Citra Admin',
            'email' => 'admin.portal@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->admin->role = RoleEnum::Admin;
        $this->admin->email_verified_at = now();
        $this->admin->save();
    }

    /** @test */
    public function tamu_tidak_bisa_mengakses_dashboard_peserta(): void
    {
        $response = $this->get(route('peserta.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function role_non_peserta_dilarang_mengakses_dashboard_peserta(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('peserta.dashboard'));
        $response->assertStatus(403);

        $responseAdmin = $this->actingAs($this->admin)->get(route('peserta.dashboard'));
        $responseAdmin->assertStatus(403);
    }

    /** @test */
    public function login_peserta_mengarahkan_ke_dashboard_peserta(): void
    {
        $response = $this->post(route('login'), [
            'email' => $this->peserta->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));
    }

    /** @test */
    public function peserta_baru_melihat_fase_onboarding_pada_dashboard(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));

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
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));

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
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(50)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Membuat API Dashboard',
            'deskripsi' => 'Kerjakan controller dan view',
            'kategori' => 'Development',
            'kompleksitas' => \App\Enums\KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => now()->subDay()->toDateString(),
            'kegiatan' => 'Mempelajari arsitektur database',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.dashboard'));

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
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(50)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $tugas = Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Tugas Pengujian Frontend',
            'deskripsi' => 'Testing tampilan peserta',
            'kategori' => 'Testing',
            'kompleksitas' => \App\Enums\KompleksitasEnum::Rendah,
            'deadline' => now()->addDays(3)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        // 1. Dashboard
        $this->actingAs($this->peserta)->get(route('peserta.dashboard'))->assertOk();

        // 2. Profil
        $this->actingAs($this->peserta)->get(route('peserta.profil.show'))->assertOk();

        // 3. Pendaftaran Index
        $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.index'))->assertOk();

        // 4. Pendaftaran Detail
        $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.detail', $pendaftaran))->assertOk();

        // 5. Logbook Index
        $this->actingAs($this->peserta)->get(route('peserta.logbook.index'))->assertOk();

        // 6. Tugas Index
        $this->actingAs($this->peserta)->get(route('peserta.tugas.index'))->assertOk();

        // 7. Tugas Show
        $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas))->assertOk();

        // 8. Nilai Show
        $this->actingAs($this->peserta)->get(route('peserta.nilai.show'))->assertOk();
    }
}
