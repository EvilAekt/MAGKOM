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

    public function test_pembimbing_peserta_show_menggunakan_desain_mockup_modern_tanpa_status_penilaian(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.show', $this->penempatan->id));

        $response->assertOk();

        // 1. Topbar Title & Strip Bar Tanggal & Tombol Kembali
        $response->assertSee('class="topbar-title"', false);
        $response->assertSee('Profil Peserta Bimbingan');
        $response->assertDontSee('class="breadcrumb-trail"', false);
        $response->assertSee('class="mentee-date-action-bar"', false);
        $response->assertSee('date-pill-amber');
        $response->assertSee('btn-back-navy');
        $response->assertSee('Kembali');

        // 2. Mentee Hero Card (Adaptasi Kontekstual Peserta SMK)
        $response->assertSee('class="mentee-hero-card"', false);
        $response->assertSee('mentee-avatar-gold-ring');
        $response->assertSee('btn-pill-logbook');
        $response->assertSee('btn-pill-tugas');
        $response->assertSee('DATA PESERTA');
        $response->assertSee('SMK Negeri 2 Depok Sleman');
        $response->assertSee('Naufal Pratama');
        $response->assertSee('KOMPETENSI KEAHLIAN');
        $response->assertSee('NISN');
        $response->assertDontSee('Data guru pembimbing sekolah belum diisi.');
        $response->assertDontSee('Data dosen dan kampus belum diisi.');
        $response->assertSee('peserta-show');

        // 3. Status Penilaian telah dihapus sepenuhnya
        $response->assertDontSee('STATUS PENILAIAN');
        $response->assertDontSee('Status Penilaian');
        $response->assertDontSee('Berdasarkan 5 kriteria rubrik');

        // 4. Tiga Kartu KPI Ringkasan & Legenda Donut Kapsul Progres Tugas
        $response->assertSee('class="mentee-kpi-summary-grid"', false);
        $response->assertSee('PROGRES TUGAS');
        $response->assertSee('donut-legend-pill', false);
        $response->assertSee('dot-selesai');
        $response->assertSee('dot-dikerjakan');
        $response->assertSee('dot-belum');
        $response->assertSee('PERIODE MAGANG');
        $response->assertSee('class="periode-progress-fill"', false);
        $response->assertSee('style="width: 11%;"', false);
        $response->assertDontSee('style="width: 0%;"');
        $response->assertSee('LOGBOOK');

        // 5. Riwayat Penugasan Kanban Board
        $response->assertSee('Riwayat Penugasan');
        $response->assertSee('Kelola Tugas');
        $response->assertSee('kanban-columns-grid');

        // 6. Stylesheet peserta-show dimuat via Vite
        $response->assertSee('peserta-show', false);
    }

    public function test_pembimbing_peserta_show_menyesuaikan_label_dan_catatan_untuk_mahasiswa_perguruan_tinggi(): void
    {
        $mahasiswaUser = User::factory()->create([
            'nama' => 'Aditya Pratama',
            'email' => 'aditya@kampus.ac.id',
            'role' => RoleEnum::Peserta,
        ]);

        PesertaProfil::create([
            'user_id' => $mahasiswaUser->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '21/473829/TK/52100',
            'no_hp' => '081234567888',
            'nama_pembimbing_sekolah' => 'Dr. Eng. Ir. Supriyanto, M.T.',
        ]);

        $penempatanMhs = Penempatan::create([
            'peserta_id' => $mahasiswaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->subDays(5),
            'tgl_selesai' => now()->addDays(85),
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.show', $penempatanMhs->id));

        $response->assertOk();
        $response->assertSee('Aditya Pratama');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('PROGRAM STUDI');
        $response->assertSee('NIM');
        $response->assertSee('Dosen Pembimbing: Dr. Eng. Ir. Supriyanto, M.T.');
        $response->assertDontSee('KOMPETENSI KEAHLIAN');
        $response->assertDontSee('Data guru pembimbing sekolah belum diisi.');
    }
}
