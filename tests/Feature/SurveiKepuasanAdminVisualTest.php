<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SurveiKepuasan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SurveiKepuasanAdminVisualTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $peserta;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika']);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->admin = new User;
        $this->admin->nama = 'Admin Magang Sleman';
        $this->admin->email = 'admin.magang@slemankab.go.id';
        $this->admin->password = Hash::make('Password123!');
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->must_change_password = false;
        $this->admin->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Alumni Peserta MagKom';
        $this->peserta->email = 'alumni@kampus.ac.id';
        $this->peserta->password = Hash::make('Password123!');
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '20110002',
            'no_hp' => '081234567899',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->admin->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(60),
            'tgl_selesai' => Carbon::today()->subDays(5),
            'status' => StatusPenempatanEnum::Selesai,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_survei_kepuasan_dengan_tampilan_empty_state(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.survei.index'));

        $response->assertOk();
        $response->assertSee('Indeks Kepuasan Magang (IKM)');
        $response->assertSee('Net Promoter Score (NPS)');
        $response->assertSee('Bimbingan Mentor');
        $response->assertSee('Relevansi Tugas Proyek');
        $response->assertSee('Fasilitas & Ruang Kerja', false);
        $response->assertSee('Ekspor Hasil Survei (CSV)');
        $response->assertSee('Belum Ada Pengisian Survei Kepuasan');
    }

    public function test_admin_melihat_metrik_ikm_nps_dan_daftar_testimoni_saat_ada_data(): void
    {
        SurveiKepuasan::create([
            'penempatan_id' => $this->penempatan->id,
            'skor_mentoring' => 5,
            'skor_tugas' => 4,
            'skor_fasilitas' => 5,
            'skor_nps' => 10,
            'umpan_balik' => 'Program magang di Kominfo Sleman sangat luar biasa membimbing kami!',
            'saran' => 'Pertahankan kultur bimbingan yang ramah dan suportif.',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.survei.index'));

        $response->assertOk();
        $response->assertSee('Alumni Peserta MagKom');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('Program magang di Kominfo Sleman sangat luar biasa membimbing kami!');
        $response->assertSee('Pertahankan kultur bimbingan yang ramah dan suportif.');
        $response->assertSee('1 Alumni Peserta');
        $response->assertSee('Promoters (9-10)');
    }

    public function test_admin_bisa_mengunduh_ekspor_csv_survei(): void
    {
        SurveiKepuasan::create([
            'penempatan_id' => $this->penempatan->id,
            'skor_mentoring' => 5,
            'skor_tugas' => 5,
            'skor_fasilitas' => 4,
            'skor_nps' => 9,
            'umpan_balik' => 'Sangat memuaskan',
            'saran' => 'Tingkatkan kuota',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.survei.export'));

        $response->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('rekap-survei-kepuasan-', (string) $response->headers->get('Content-Disposition'));
    }
}
