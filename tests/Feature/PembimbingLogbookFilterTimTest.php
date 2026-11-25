<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingLogbookFilterTimTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $pembimbingLain;

    protected KelompokMagang $kelompok;

    protected User $ketua;

    protected User $anggota;

    protected User $individu;

    protected Penempatan $penempatanKetua;

    protected Penempatan $penempatanAnggota;

    protected Penempatan $penempatanIndividu;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang EGOV',
            'kode' => 'EGOV',
            'kuota' => 20,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $bidang->id,
            'nama' => 'Budi Pembimbing',
            'email' => 'pembimbing-logbook@magkom.test',
        ]);

        $this->pembimbingLain = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $bidang->id,
            'nama' => 'Pembimbing Lain',
            'email' => 'pembimbing-lain-logbook@magkom.test',
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_buka' => '2026-01-01',
            'tgl_tutup' => '2026-12-31',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 3,
            'aktif' => true,
        ]);

        $this->ketua = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Arif Ketua', 'email' => 'arif@magkom.test']);
        PesertaProfil::create(['user_id' => $this->ketua->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '3001', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08130000001']);

        $this->anggota = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Dina Anggota', 'email' => 'dina@magkom.test']);
        PesertaProfil::create(['user_id' => $this->anggota->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '3002', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08130000002']);

        $this->individu = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Eka Mandiri', 'email' => 'eka@magkom.test']);
        PesertaProfil::create(['user_id' => $this->individu->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UNY', 'nim_nisn' => '3003', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08130000003']);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Inovasi Sleman',
            'ketua_user_id' => $this->ketua->id,
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $this->kelompok->id,
            'user_id' => $this->anggota->id,
            'nama' => $this->anggota->nama,
            'email' => $this->anggota->email,
        ]);

        $pendaftaranTim = Pendaftaran::create([
            'user_id' => $this->ketua->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $this->kelompok->id,
            'durasi_bulan' => 3,
            'tipe' => 'kelompok',
            'status' => 'diterima',
        ]);

        $pendaftaranIndividu = Pendaftaran::create([
            'user_id' => $this->individu->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => null,
            'durasi_bulan' => 3,
            'tipe' => 'individu',
            'status' => 'diterima',
        ]);

        $tglMulai = now()->subDays(10)->toDateString();
        $tglSelesai = now()->addMonths(2)->toDateString();

        $this->penempatanKetua = Penempatan::create([
            'pendaftaran_id' => $pendaftaranTim->id,
            'peserta_id' => $this->ketua->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        $this->penempatanAnggota = Penempatan::create([
            'pendaftaran_id' => $pendaftaranTim->id,
            'peserta_id' => $this->anggota->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        $this->penempatanIndividu = Penempatan::create([
            'pendaftaran_id' => $pendaftaranIndividu->id,
            'peserta_id' => $this->individu->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        Logbook::create([
            'penempatan_id' => $this->penempatanAnggota->id,
            'minggu_ke' => 1,
            'tanggal' => now()->subDays(2)->toDateString(),
            'kegiatan' => 'Logbook minggu 1 oleh Dina Anggota.',
            'status' => StatusLogbookEnum::Terkirim->value,
        ]);
    }

    public function test_pembimbing_can_filter_logbook_by_semua_individu_and_tim(): void
    {
        // Semua (2): 1 Individu + 1 Tim
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index', ['jenis' => 'semua']))
            ->assertOk()
            ->assertSee('Semua (2)')
            ->assertSee('Individu (1)')
            ->assertSee('Tim (1 Tim)')
            ->assertSee('Tim Inovasi Sleman')
            ->assertSee('Eka Mandiri');

        // Individu: hanya Eka Mandiri
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index', ['jenis' => 'individu']))
            ->assertOk()
            ->assertSee('Eka Mandiri')
            ->assertDontSee('Tim Inovasi Sleman');

        // Tim: hanya Tim Inovasi Sleman
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index', ['jenis' => 'kelompok']))
            ->assertOk()
            ->assertSee('Tim Inovasi Sleman')
            ->assertSee('2 Peserta')
            ->assertDontSee('Eka Mandiri');
    }

    public function test_clicking_tim_shows_ketua_and_members_and_opens_member_logbook(): void
    {
        // Drill-down ke dalam tim
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index', [
                'masuk_kelompok' => $this->kelompok->id,
                'jenis' => 'kelompok',
            ]))
            ->assertOk()
            ->assertSee('Tim Inovasi Sleman')
            ->assertSee('Arif Ketua')
            ->assertSee('Ketua Tim')
            ->assertSee('Dina Anggota')
            ->assertSee('Anggota')
            ->assertSee('1 Logbook')
            ->assertDontSee('Eka Mandiri');

        // Klik salah satu anggota -> buka logbook anggota tersebut + tombol kembali ke tim
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.logbook.index', [
                'masuk' => $this->penempatanAnggota->id,
                'dari_kelompok' => $this->kelompok->id,
                'jenis' => 'kelompok',
            ]))
            ->assertOk()
            ->assertSee('Dina Anggota')
            ->assertSee('Logbook minggu 1 oleh Dina Anggota.')
            ->assertSee('Kembali ke Detail Tim');
    }

    public function test_pembimbing_lain_cannot_view_other_pembimbing_tim_logbook(): void
    {
        $this->actingAs($this->pembimbingLain)
            ->get(route('pembimbing.logbook.index', [
                'masuk_kelompok' => $this->kelompok->id,
                'jenis' => 'kelompok',
            ]))
            ->assertOk()
            ->assertDontSee('Tim Inovasi Sleman')
            ->assertDontSee('Dina Anggota');
    }
}
