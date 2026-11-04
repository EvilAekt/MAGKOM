<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AgendaBimbingan;
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

    public function test_direktori_peserta_merender_hero_banner_dan_empat_kartu_tile_stat(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.index'));

        $response->assertOk();
        $response->assertSee('Direktori Peserta Bimbingan');
        $response->assertSee('Budi Santoso, S.Kom');
        $response->assertSee('+ Beri Tugas Baru');
        $response->assertSee('Total Mentee');
        $response->assertSee('Peserta Aktif');
        $response->assertSee('Perlu Perhatian');
        $response->assertSee('Alumni Selesai');
        $response->assertSee('SMK Negeri 2 Depok Sleman');
        $response->assertSee('Naufal Pratama');
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
        $response->assertSee('Mengerjakan implementasi antarmuka portal MagKom.');
    }

    public function test_evaluasi_rubrik_merender_hero_banner_dan_empat_kartu_tile_stat(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.penilaian.index'));

        $response->assertOk();
        $response->assertSee('Evaluasi Rubrik &amp; Penilaian Magang', false);
        $response->assertSee('Ekspor Rekap Bimbingan (CSV)');
        $response->assertSee('Total Peserta');
        $response->assertSee('Belum Dinilai');
        $response->assertSee('Draf Tersimpan');
        $response->assertSee('Final Resmi');
        $response->assertSee('Petunjuk Evaluasi Rubrik Resmi Diskominfo Sleman');
    }

    public function test_agenda_mentoring_merender_hero_banner_dan_empat_kartu_tile_stat(): void
    {
        AgendaBimbingan::create([
            'penempatan_id' => $this->penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul_kegiatan' => 'Evaluasi Pekanan Modul Portal',
            'tipe' => 'tatap_muka',
            'tgl_bimbingan' => now()->addDays(2),
            'jam_mulai' => '10:00',
            'lokasi_ruang' => 'Ruang Rapat Bidang',
            'status' => 'terjadwal',
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.agenda.index'));

        $response->assertOk();
        $response->assertSee('Agenda Mentoring &amp; Notulensi', false);
        $response->assertSee('+ Jadwalkan Mentoring Baru');
        $response->assertSee('Total Agenda');
        $response->assertSee('Agenda Terjadwal');
        $response->assertSee('Selesai Terlaksana');
        $response->assertSee('Metode Sesi');
        $response->assertSee('Evaluasi Pekanan Modul Portal');
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
}
