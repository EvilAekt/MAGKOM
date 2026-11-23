<?php

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingTugasFilterAndProjectBersamaTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected KelompokMagang $kelompok;

    protected User $andi;

    protected User $budi;

    protected User $citra;

    protected User $dimas;

    protected Penempatan $penempatanAndi;

    protected Penempatan $penempatanBudi;

    protected Penempatan $penempatanCitra;

    protected Penempatan $penempatanDimas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang E-Government',
            'kode' => 'EGOV',
            'kuota' => 20,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
            'email' => 'pembimbing@magkom.test',
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_buka' => '2026-01-01',
            'tgl_tutup' => '2026-01-31',
            'aktif' => true,
        ]);

        // Buat 4 user peserta: Andi (Ketua), Budi, Citra, Dimas
        $this->andi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Andi Pratama', 'email' => 'andi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->andi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1001', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567891']);

        $this->budi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Budi Santoso', 'email' => 'budi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->budi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1002', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567892']);

        $this->citra = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Citra Lestari', 'email' => 'citra@magkom.test']);
        PesertaProfil::create(['user_id' => $this->citra->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1003', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567893']);

        $this->dimas = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Dimas Wahyu', 'email' => 'dimas@magkom.test']);
        PesertaProfil::create(['user_id' => $this->dimas->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1004', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567894']);

        // Kelompok Magang
        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Garuda E-Gov',
            'ketua_user_id' => $this->andi->id,
            'kode_kelompok' => 'GARUDA-01',
            'institusi' => 'UGM',
        ]);

        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->budi->id, 'nama' => $this->budi->nama, 'email' => $this->budi->email]);
        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->citra->id, 'nama' => $this->citra->nama, 'email' => $this->citra->email]);
        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->dimas->id, 'nama' => $this->dimas->nama, 'email' => $this->dimas->email]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 4,
            'aktif' => true,
        ]);

        // Pendaftaran kelompok
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->andi->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $this->kelompok->id,
            'durasi_bulan' => 3,
            'tipe_pendaftaran' => 'kelompok',
            'status' => 'diterima',
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        // Penempatan untuk masing-masing peserta
        $this->penempatanAndi = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->andi->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanBudi = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->budi->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanCitra = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->citra->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanDimas = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->dimas->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);
    }

    public function test_pembimbing_can_filter_tugas_by_semua_individu_and_kelompok(): void
    {
        // Tugas individu untuk Andi
        Tugas::create([
            'penempatan_id' => $this->penempatanAndi->id,
            'tipe' => 'individu',
            'judul' => 'Tugas Individu Andi',
            'deskripsi' => 'Riset mandiri arsitektur',
            'kategori' => 'Riset',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Belum,
            'status_publikasi' => 'dipublikasikan',
        ]);

        // Filter Semua
        $responseSemua = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', ['jenis_tugas' => 'semua']));
        $responseSemua->assertOk();
        $responseSemua->assertSee('Semua');

        // Filter Individu
        $responseIndividu = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', ['jenis_tugas' => 'individu']));
        $responseIndividu->assertOk();

        // Filter Kelompok menampilkan tabel daftar kelompok bimbingan
        $responseKelompok = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', ['jenis_tugas' => 'kelompok']));
        $responseKelompok->assertOk();
        $responseKelompok->assertSee('Tim Garuda E-Gov');
        $responseKelompok->assertSee('Andi Pratama');
        $responseKelompok->assertSee('4 Peserta');
        $responseKelompok->assertSee(route('pembimbing.tugas.kelompok.show', $this->kelompok->id));
    }

    public function test_pembimbing_can_view_kelompok_detail_and_tabs(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.kelompok.show', $this->kelompok->id));
        $response->assertOk();
        $response->assertSee('Tim Garuda E-Gov');
        $response->assertSee('Review Individu');
        $response->assertSee('Project Bersama');
        $response->assertSee('Andi Pratama');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Citra Lestari');
        $response->assertSee('Dimas Wahyu');
    }

    public function test_project_bersama_assignment_and_strict_access_control(): void
    {
        // Pembimbing membuat Project Bersama 1 dan HANYA menugaskan Andi, Budi, dan Citra (Dimas TIDAK ditugaskan)
        $projectData = [
            'judul' => 'Project Bersama 1 - Redesign Portal Sleman',
            'deskripsi' => 'Kerjakan bersama arsitektur dan komponen baru',
            'kategori' => 'Implementasi Fitur',
            'kompleksitas' => 'tinggi',
            'deadline' => now()->addDays(14)->format('Y-m-d'),
            'peserta_ids' => [$this->andi->id, $this->budi->id, $this->citra->id],
        ];

        $postResponse = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.kelompok.project.store', $this->kelompok->id), $projectData);

        $postResponse->assertRedirect(route('pembimbing.tugas.kelompok.show', ['kelompok' => $this->kelompok->id, 'tab' => 'project']));

        $tugasProject = Tugas::where('judul', 'Project Bersama 1 - Redesign Portal Sleman')->first();
        $this->assertNotNull($tugasProject);
        $this->assertEquals('kelompok', $tugasProject->tipe);
        $this->assertEquals($this->kelompok->id, $tugasProject->kelompok_magang_id);

        // Verifikasi tugas_peserta hanya berisi Andi, Budi, Citra
        $assignedUserIds = $tugasProject->pesertaDitugaskan()->pluck('users.id')->all();
        $this->assertContains($this->andi->id, $assignedUserIds);
        $this->assertContains($this->budi->id, $assignedUserIds);
        $this->assertContains($this->citra->id, $assignedUserIds);
        $this->assertNotContains($this->dimas->id, $assignedUserIds);

        // 1. Dashboard Andi (assigned) -> Terlihat dan bisa diakses
        $andiDashboard = $this->actingAs($this->andi)->get(route('peserta.tugas.index'));
        $andiDashboard->assertOk();
        $andiDashboard->assertSee('Project Bersama 1 - Redesign Portal Sleman');

        $andiDetail = $this->actingAs($this->andi)->get(route('peserta.tugas.show', $tugasProject->id));
        $andiDetail->assertOk();

        // 2. Dashboard Budi (assigned) -> Terlihat dan bisa diakses
        $budiDashboard = $this->actingAs($this->budi)->get(route('peserta.tugas.index'));
        $budiDashboard->assertOk();
        $budiDashboard->assertSee('Project Bersama 1 - Redesign Portal Sleman');

        $budiDetail = $this->actingAs($this->budi)->get(route('peserta.tugas.show', $tugasProject->id));
        $budiDetail->assertOk();

        // 3. Dashboard Citra (assigned) -> Terlihat dan bisa diakses
        $citraDashboard = $this->actingAs($this->citra)->get(route('peserta.tugas.index'));
        $citraDashboard->assertOk();
        $citraDashboard->assertSee('Project Bersama 1 - Redesign Portal Sleman');

        $citraDetail = $this->actingAs($this->citra)->get(route('peserta.tugas.show', $tugasProject->id));
        $citraDetail->assertOk();

        // 4. Dashboard Dimas (TIDAK DITUGASKAN) -> TIDAK TERLIHAT di index & 403 Forbidden di detail!
        $dimasDashboard = $this->actingAs($this->dimas)->get(route('peserta.tugas.index'));
        $dimasDashboard->assertOk();
        $dimasDashboard->assertDontSee('Project Bersama 1 - Redesign Portal Sleman');

        $dimasDetail = $this->actingAs($this->dimas)->get(route('peserta.tugas.show', $tugasProject->id));
        $dimasDetail->assertForbidden();

        // 5. Pembimbing mengupdate assignment: sekarang Dimas dimasukkan
        $updateResponse = $this->actingAs($this->pembimbing)
            ->put(route('pembimbing.tugas.project.assignment', $tugasProject->id), [
                'peserta_ids' => [$this->andi->id, $this->budi->id, $this->citra->id, $this->dimas->id],
            ]);
        $updateResponse->assertRedirect();

        // Sekarang Dimas bisa melihat dan mengakses project bersama
        $dimasDashboardUpdated = $this->actingAs($this->dimas)->get(route('peserta.tugas.index'));
        $dimasDashboardUpdated->assertOk();
        $dimasDashboardUpdated->assertSee('Project Bersama 1 - Redesign Portal Sleman');

        $dimasDetailUpdated = $this->actingAs($this->dimas)->get(route('peserta.tugas.show', $tugasProject->id));
        $dimasDetailUpdated->assertOk();
    }
}
