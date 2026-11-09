<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingPortalStandardizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $otherPembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
            'deskripsi' => 'Bidang Pengembangan Sistem',
            'kuota' => 10,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer & Jaringan',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'pembimbing@magkom.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
        ]);

        $this->otherPembimbing = User::factory()->create([
            'nama' => 'Siti Aminah, M.Kom',
            'email' => 'siti@magkom.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
        ]);

        $pesertaUser = User::factory()->create([
            'nama' => 'Naufal Pratama',
            'email' => 'naufal@magkom.go.id',
            'role' => RoleEnum::Peserta,
        ]);

        PesertaProfil::create([
            'user_id' => $pesertaUser->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nim_nisn' => '12345678',
            'no_hp' => '081234567890',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $pesertaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->subDays(10),
            'tgl_selesai' => now()->addDays(80),
        ]);
    }

    public function test_direktori_peserta_merender_hero_banner_dan_tiga_kartu_tile_stat(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.index'));

        $response->assertOk();
        $response->assertSee('Direktori Peserta Bimbingan');
        $response->assertSee('Budi Santoso, S.Kom');
        $response->assertSee('Beri Tugas Baru');
        $response->assertDontSee('+ + Beri Tugas Baru');
        $response->assertSee('Total Mentee');
        $response->assertSee('Peserta Aktif');
        $response->assertSee('Alumni Selesai');
        $response->assertDontSee('Perlu Perhatian');
        $response->assertDontSee('Monitoring progres durasi dan status evaluasi peserta magang');
        $response->assertDontSee('Status Nilai');
        $response->assertSee('SMK Negeri 2 Depok Sleman');
        $response->assertSee('Naufal Pratama');
        $response->assertSee('peserta-filter-card');
        $response->assertSee('btn-search');
    }

    public function test_review_logbook_merender_hero_banner_dan_empat_kartu_tile_stat(): void
    {
        Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'tanggal' => now(),
            'kegiatan' => 'Mengerjakan implementasi antarmuka portal MagKom.',
            'status' => 'terkirim',
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index'));

        $response->assertOk();
        $response->assertSee('Review Logbook Harian Peserta');
        $response->assertSee('Total Logbook');
        $response->assertSee('Perlu Review');
        $response->assertSee('Disetujui');
        $response->assertSee('Perlu Revisi');
        $response->assertSee('logbook-filter-card');
        $response->assertSee('btn-filter-apply');
        $response->assertSee($this->penempatan->peserta->nama);

        // Pada mode masuk ke rincian mentee, entri kegiatan logbook tampil
        $detailResponse = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index', ['masuk' => $this->penempatan->id]));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Mengerjakan implementasi antarmuka portal MagKom.');
    }

    public function test_anti_idor_pembimbing_hanya_melihat_mentee_miliknya(): void
    {
        $otherPeserta = User::factory()->create([
            'nama' => 'Peserta Rahasia Lain',
            'email' => 'rahasia@magkom.go.id',
            'role' => RoleEnum::Peserta,
        ]);

        PesertaProfil::create([
            'user_id' => $otherPeserta->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'institusi' => 'Universitas Lain',
            'nim_nisn' => '99999999',
            'no_hp' => '081234567899',
        ]);

        Penempatan::create([
            'peserta_id' => $otherPeserta->id,
            'pembimbing_id' => $this->otherPembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addDays(90),
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.index'));

        $response->assertOk();
        $response->assertDontSee('Peserta Rahasia Lain');
    }

    public function test_pembimbing_peserta_show_menggunakan_desain_classroom_banner_tanpa_status_penilaian(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.show', $this->penempatan->id));

        $response->assertOk();

        // 1. Topbar Title & Classroom Navy Banner & Tombol Kembali
        $response->assertSee('class="topbar-title"', false);
        $response->assertDontSee('class="breadcrumb-trail"', false);
        $response->assertSee('class="classroom-banner"', false);
        $response->assertSee('Profil 360° Mentee Bimbingan');
        $response->assertSee('class="btn-banner-back"', false);
        $response->assertSee('Kembali ke Direktori');

        // 2. Mentee Hero Card
        $response->assertSee('class="mentee-hero-card"', false);
        $response->assertSee('Logbook Mentee');
        $response->assertSee('Tugas Mentee');

        // 3. Status Penilaian telah dihapus sepenuhnya
        $response->assertDontSee('STATUS PENILAIAN');
        $response->assertDontSee('Status Penilaian');
        $response->assertDontSee('Berdasarkan 5 kriteria rubrik');

        // 4. Dua Widget KPI Utama
        $response->assertSee('class="mentee-kpi-grid"', false);
        $response->assertSee('Penyelesaian Tugas');
        $response->assertSee('Kedisiplinan Logbook');

        // 5. Analitik & Data Diri
        $response->assertSee('Komposisi &amp; Rasio Tugas Peserta', false);
        $response->assertSee('Pelacakan Mingguan Logbook Magang');
        $response->assertSee('Data Pribadi &amp; Kontak Akademik', false);
        $response->assertSee('Timeline Periode &amp; Ketentuan Akun', false);
        $response->assertSee('Riwayat Penugasan');

        // 6. Stylesheet peserta-show dimuat via Vite
        $response->assertSee('peserta-show', false);
    }
}
