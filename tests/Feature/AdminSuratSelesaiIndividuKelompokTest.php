<?php

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
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
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSuratSelesaiIndividuKelompokTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $andi;

    protected User $budi;

    protected User $citra;

    protected User $individu;

    protected Penempatan $penempatanAndi;

    protected Penempatan $penempatanBudi;

    protected Penempatan $penempatanCitra;

    protected Penempatan $penempatanIndividu;

    protected KelompokMagang $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin->value,
            'email' => 'admin@magkom.test',
        ]);

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'kode' => 'APTIKA',
            'kuota' => 20,
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

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 4,
            'aktif' => true,
        ]);

        $pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $bidang->id,
            'email' => 'pembimbing@magkom.test',
        ]);

        // Setup Kelompok: 3 Orang
        $this->andi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Andi Pratama']);
        PesertaProfil::create(['user_id' => $this->andi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '101', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081111']);

        $this->budi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Budi Santoso']);
        PesertaProfil::create(['user_id' => $this->budi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '102', 'jenjang' => JenjangEnum::S1, 'no_hp' => '082222']);

        $this->citra = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Citra Lestari']);
        PesertaProfil::create(['user_id' => $this->citra->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '103', 'jenjang' => JenjangEnum::S1, 'no_hp' => '083333']);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Dev Diskominfo',
            'ketua_user_id' => $this->andi->id,
            'kode_kelompok' => 'DEV-01',
            'institusi' => 'UGM',
        ]);

        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->budi->id, 'nama' => $this->budi->nama, 'email' => $this->budi->email]);
        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->citra->id, 'nama' => $this->citra->nama, 'email' => $this->citra->email]);

        $pendaftaranKelompok = Pendaftaran::create([
            'user_id' => $this->andi->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $this->kelompok->id,
            'tipe' => 'kelompok',
            'durasi_bulan' => 3,
            'status' => 'diterima',
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanAndi = Penempatan::create([
            'pendaftaran_id' => $pendaftaranKelompok->id,
            'peserta_id' => $this->andi->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanBudi = Penempatan::create([
            'pendaftaran_id' => $pendaftaranKelompok->id,
            'peserta_id' => $this->budi->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanCitra = Penempatan::create([
            'pendaftaran_id' => $pendaftaranKelompok->id,
            'peserta_id' => $this->citra->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        // Setup Individu: 1 Orang
        $this->individu = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Rahmat Hidayat']);
        PesertaProfil::create(['user_id' => $this->individu->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UNY', 'nim_nisn' => '201', 'jenjang' => JenjangEnum::S1, 'no_hp' => '084444']);

        $pendaftaranIndividu = Pendaftaran::create([
            'user_id' => $this->individu->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => null,
            'tipe' => 'individu',
            'durasi_bulan' => 3,
            'status' => 'diterima',
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $this->penempatanIndividu = Penempatan::create([
            'pendaftaran_id' => $pendaftaranIndividu->id,
            'peserta_id' => $this->individu->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);
    }

    public function test_admin_melihat_indikator_kelompok_pada_halaman_detail_penempatan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.show', $this->penempatanAndi->id));
        $response->assertOk();
        $response->assertSee('Andi Pratama');
        $response->assertSee('Tim Dev Diskominfo');
        $response->assertSee('Ketua Kelompok');
        $response->assertSee('Informasi Penempatan Peserta');
        $response->assertSee('Biodata Peserta');
        $response->assertSee('Institusi / Asal');
        $response->assertSee('NIM / NISN');
        $response->assertSee('Formulir Unggah Surat Selesai');
        $response->assertDontSee('Daftar Anggota Kelompok: Tim Dev Diskominfo');
        $response->assertDontSee('Tabel khusus rincian anggota kelompok');
        $response->assertDontSee('Aksi Individual');

        // Peserta Individu tidak menampilkan badge kelompok
        $responseInd = $this->actingAs($this->admin)->get(route('admin.penempatan.show', $this->penempatanIndividu->id));
        $responseInd->assertOk();
        $responseInd->assertDontSee('Tim Dev Diskominfo');
        $responseInd->assertSee('Rahmat Hidayat');
    }

    public function test_upload_surat_selesai_kelompok_mendistribusikan_ke_3_anggota_sekaligus(): void
    {
        $file = UploadedFile::fake()->create('surat_selesai_resmi.pdf', 800, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
                'file_surat' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->penempatanAndi->refresh();
        $this->penempatanBudi->refresh();
        $this->penempatanCitra->refresh();

        // Ketiga anggota kelompok memiliki berkas yang sama
        $this->assertNotNull($this->penempatanAndi->file_surat_selesai);
        $this->assertEquals($this->penempatanAndi->file_surat_selesai, $this->penempatanBudi->file_surat_selesai);
        $this->assertEquals($this->penempatanAndi->file_surat_selesai, $this->penempatanCitra->file_surat_selesai);

        // Ketiga anggota kelompok dapat mengunduh berkas resmi tersebut dari endpoint mandirinya
        $this->actingAs($this->andi)->get(route('peserta.selesai-magang.unduh'))->assertOk();
        $this->actingAs($this->budi)->get(route('peserta.selesai-magang.unduh'))->assertOk();
        $this->actingAs($this->citra)->get(route('peserta.selesai-magang.unduh'))->assertOk();
    }

    public function test_upload_surat_selesai_individu_hanya_mempengaruhi_peserta_terkait(): void
    {
        $file = UploadedFile::fake()->create('surat_rahmat.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu->id), [
                'file_surat' => $file,
            ]);

        $response->assertRedirect();
        $this->penempatanIndividu->refresh();
        $this->assertNotNull($this->penempatanIndividu->file_surat_selesai);

        // Anggota kelompok lain tetap belum memiliki surat
        $this->penempatanAndi->refresh();
        $this->assertNull($this->penempatanAndi->file_surat_selesai);

        $this->actingAs($this->individu)->get(route('peserta.selesai-magang.unduh'))->assertOk();
        $this->actingAs($this->andi)->get(route('peserta.selesai-magang.unduh'))->assertNotFound();
    }

    public function test_admin_dapat_mengakses_halaman_surat_selesai_index(): void
    {
        // 1. Tier 1: Tab Default: Daftar Master Individu
        $response = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index'));
        $response->assertOk();
        $response->assertSee('Daftar Surat Selesai Magang');
        $response->assertDontSee('Data Penempatan');
        $response->assertDontSee('Arsip Alumni');
        $response->assertDontSee('Entri Terdata');
        $response->assertSee('Magang Selesai');
        $response->assertSee('Lembar Halaman');
        $response->assertSee('Total Selesai');
        $response->assertSee('Belum Diunggah');
        $response->assertSee('Surat Sudah Terbit');
        $response->assertSee('Total Peserta Selesai');
        $response->assertSee('Semua');
        $response->assertSee('Peserta Individu');
        $response->assertSee('Tim');
        $response->assertSee('Rahmat Hidayat');
        $response->assertSee('Tim Dev Diskominfo');
        $response->assertSee('tab=individu&amp;masuk_individu='.$this->penempatanIndividu->id, false);
        $response->assertSee('tab=kelompok&amp;masuk_kelompok='.$this->penempatanAndi->pendaftaran_id, false);

        // 1b. Tab Semua mendukung pencarian lintas individu & kelompok
        $semuaFilterResp = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'semua',
            'q' => 'Rahmat',
        ]));
        $semuaFilterResp->assertOk();
        $semuaFilterResp->assertSee('Rahmat Hidayat');
        $semuaFilterResp->assertDontSee('Tim Dev Diskominfo');

        // 2. Tier 1: Tab Kelompok: Daftar Master Kelompok (Tanpa anggota inline)
        $kelompokResp = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', ['tab' => 'kelompok']));
        $kelompokResp->assertOk();
        $kelompokResp->assertSee('Tim Dev Diskominfo');
        $kelompokResp->assertSee('3 Anggota Terdaftar');
        $kelompokResp->assertSee('tab=kelompok&amp;masuk_kelompok='.$this->penempatanAndi->pendaftaran_id, false);
        $kelompokResp->assertDontSee('Rahmat Hidayat');
    }

    public function test_admin_dapat_masuk_ke_mode_fokus_individu_tier_2(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'individu',
            'masuk_individu' => $this->penempatanIndividu->id,
        ]));

        $response->assertOk();
        $response->assertSee('Unggah Surat Selesai: Rahmat Hidayat');
        $response->assertSee('Kembali ke Daftar Peserta');
        $response->assertSee('Informasi Penempatan Magang');
        $response->assertSee('Biodata Peserta');
        $response->assertSee('Institusi / Asal');
        $response->assertSee('NIM / NISN');
        $response->assertSee('Formulir Unggah Surat Selesai');
        $response->assertSee('focused-left-column', false);
        $response->assertSee('focused-profile-card', false);
        $response->assertDontSee('focused-identity-bar', false);
    }

    public function test_admin_dapat_masuk_ke_mode_fokus_kelompok_dan_melihat_tabel_anggota_mandiri_tier_2(): void
    {
        $pendaftaranId = $this->penempatanAndi->pendaftaran_id;
        $response = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'kelompok',
            'masuk_kelompok' => $pendaftaranId,
        ]));

        $response->assertOk();
        $response->assertSee('Unggah Surat Selesai: Tim Dev Diskominfo');
        $response->assertSee('Kembali ke Daftar Kelompok');
        $response->assertDontSee('Informasi Penempatan Kelompok');
        $response->assertDontSee('Formulir Unggah Surat Selesai');
        $response->assertDontSee('Unggah Surat Resmi');
        $response->assertSee('Daftar Anggota Kelompok: Tim Dev Diskominfo (3 Orang)');
        // Anggota tampil di tabel khusus kelompok
        $response->assertSee('Andi Pratama');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Citra Lestari');
        $response->assertSee('Ketua Kelompok');
        $response->assertDontSee('Rahmat Hidayat');
    }

    public function test_menu_surat_selesai_magang_muncul_di_sidebar_admin_dan_peserta(): void
    {
        // 1. Sidebar Admin
        $adminDashboard = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminDashboard->assertOk();
        $adminDashboard->assertSee(route('admin.surat-selesai.index'));
        $adminDashboard->assertSee('Surat Selesai Magang');

        // 2. Sidebar Peserta
        $pesertaDashboard = $this->actingAs($this->andi)->get(route('peserta.dashboard'));
        $pesertaDashboard->assertOk();
        $pesertaDashboard->assertSee(route('peserta.selesai-magang.index'));
        $pesertaDashboard->assertSee('Surat Selesai Magang');
    }

    public function test_filter_dan_pencarian_surat_selesai_berdasarkan_tab_masing_masing(): void
    {
        // Pencarian di Tab Kelompok: Cari nama kelompok atau nama anggotanya
        $kelompokResp = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', ['tab' => 'kelompok', 'q' => 'Dev']));
        $kelompokResp->assertOk();
        $kelompokResp->assertSee('Tim Dev Diskominfo');

        // Pencarian di Tab Kelompok berdasarkan nama anggota tetap menemukan kelompok terkait
        $kelompokCariAnggota = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', ['tab' => 'kelompok', 'q' => 'Andi']));
        $kelompokCariAnggota->assertOk();
        $kelompokCariAnggota->assertSee('Tim Dev Diskominfo');

        // Pencarian di Tab Individu
        $individuResp = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', ['tab' => 'individu', 'q' => 'Rahmat']));
        $individuResp->assertOk();
        $individuResp->assertSee('Rahmat Hidayat');
        $individuResp->assertDontSee('Tim Dev Diskominfo');
    }

    public function test_detail_penempatan_dari_surat_selesai_menampilkan_hero_banner_dan_link_kembali_ke_surat_selesai(): void
    {
        // 1. Pada halaman surat selesai kelompok, link aksi individual detail membawa ref=surat-selesai
        $suratKelompokResp = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'kelompok',
            'masuk_kelompok' => $this->penempatanAndi->pendaftaran_id,
        ]));
        $suratKelompokResp->assertOk();
        $suratKelompokResp->assertSee(route('admin.penempatan.show', [
            $this->penempatanAndi,
            'ref' => 'surat-selesai',
            'tab' => 'kelompok',
            'masuk_kelompok' => $this->penempatanAndi->pendaftaran_id,
        ]));

        // 2. Akses halaman detail penempatan dengan konteks surat selesai
        $detailDenganRef = $this->actingAs($this->admin)->get(route('admin.penempatan.show', [
            $this->penempatanAndi,
            'ref' => 'surat-selesai',
            'tab' => 'kelompok',
            'masuk_kelompok' => $this->penempatanAndi->pendaftaran_id,
        ]));
        $detailDenganRef->assertOk();
        $detailDenganRef->assertSee('adm-hero-banner');
        $detailDenganRef->assertSee('Unggah Surat Selesai:');
        $detailDenganRef->assertDontSee('Detail Penempatan &amp; Surat Selesai', false);
        $detailDenganRef->assertSee(route('admin.surat-selesai.index', [
            'tab' => 'kelompok',
            'masuk_kelompok' => $this->penempatanAndi->pendaftaran_id,
        ]));
        $detailDenganRef->assertSee('Kembali ke Surat Selesai Tim');
        $detailDenganRef->assertDontSee('Pratinjau Surat');
        $detailDenganRef->assertDontSee('Unduh Surat Diterima');
        $detailDenganRef->assertSee('<a href="'.route('admin.surat-selesai.index').'" class="nav-item-link active">', false);
        $detailDenganRef->assertSee('<a href="'.route('admin.penempatan.index').'" class="nav-item-link ">', false);

        // 3. Akses halaman detail penempatan standar tanpa konteks surat selesai
        $detailStandar = $this->actingAs($this->admin)->get(route('admin.penempatan.show', $this->penempatanAndi));
        $detailStandar->assertOk();
        $detailStandar->assertDontSee('adm-hero-banner');
        $detailStandar->assertSee(route('admin.penempatan.index'));
        $detailStandar->assertSee('<a href="'.route('admin.penempatan.index').'" class="nav-item-link active">', false);
        $detailStandar->assertSee('<a href="'.route('admin.surat-selesai.index').'" class="nav-item-link ">', false);
    }

    public function test_halaman_peserta_selesai_magang_menggunakan_layout_peserta_dan_class_judul_putih(): void
    {
        $response = $this->actingAs($this->andi)->get(route('peserta.selesai-magang.index'));

        $response->assertOk();
        $response->assertSee('data-action="toggle-sidebar-peserta"', false);
        $response->assertSee('class="peserta-selesai-hero-title"', false);
        $response->assertSee('Status Kelulusan &amp; Surat Selesai Magang', false);
    }
}
