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

class SuratSelesaiMagangIndividualTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $andi;

    protected User $budi;

    protected User $citra;

    protected User $pesertaIndividu;

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
            'kuota' => 15,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
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

        // ==========================================
        // 1. DATA KELOMPOK (3 Anggota: Andi, Budi, Citra)
        // ==========================================
        $this->andi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Andi Pratama', 'email' => 'andi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->andi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1001', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567891']);

        $this->budi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Budi Santoso', 'email' => 'budi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->budi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1002', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567892']);

        $this->citra = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Citra Lestari', 'email' => 'citra@magkom.test']);
        PesertaProfil::create(['user_id' => $this->citra->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1003', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567893']);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Aptika UGM',
            'ketua_user_id' => $this->andi->id,
            'kode_kelompok' => 'APTIKA-01',
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

        // Penempatan Andi (Ketua)
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

        // Penempatan Budi (Anggota 1)
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

        // Penempatan Citra (Anggota 2)
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

        // ==========================================
        // 2. DATA INDIVIDU (1 Peserta Mandiri)
        // ==========================================
        $this->pesertaIndividu = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Dewi Sartika', 'email' => 'dewi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->pesertaIndividu->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UNY', 'nim_nisn' => '2001', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567899']);

        $pendaftaranIndividu = Pendaftaran::create([
            'user_id' => $this->pesertaIndividu->id,
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
            'peserta_id' => $this->pesertaIndividu->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);
    }

    public function test_admin_cannot_upload_surat_selesai_if_status_not_selesai(): void
    {
        $this->penempatanAndi->update(['status' => StatusPenempatanEnum::Aktif]);

        $pdfFile = UploadedFile::fake()->create('surat_andi.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
                'file_surat' => $pdfFile,
            ]);

        $response->assertSessionHasErrors('file_surat');
        $this->penempatanAndi->refresh();
        $this->assertNull($this->penempatanAndi->file_surat_selesai);
    }

    public function test_admin_upload_surat_selesai_individu_only_affects_individual_participant(): void
    {
        $fileIndividu = UploadedFile::fake()->create('surat_dewi.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu->id), [
                'file_surat' => $fileIndividu,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->penempatanIndividu->refresh();
        $this->assertNotNull($this->penempatanIndividu->file_surat_selesai);
        $this->assertNotNull($this->penempatanIndividu->tgl_upload_surat_selesai);
        Storage::disk('local')->assertExists($this->penempatanIndividu->file_surat_selesai);

        // Pastikan penempatan kelompok tidak terpengaruh
        $this->penempatanAndi->refresh();
        $this->assertNull($this->penempatanAndi->file_surat_selesai);

        // Peserta individu dapat mengunduh surat miliknya
        $downloadResp = $this->actingAs($this->pesertaIndividu)->get(route('peserta.selesai-magang.unduh'));
        $downloadResp->assertOk();
        $downloadResp->assertHeader('Content-Type', 'application/pdf');

        // Dashboard utama peserta individu menampilkan tombol unduh
        $dashboardResp = $this->actingAs($this->pesertaIndividu)->get(route('peserta.dashboard'));
        $dashboardResp->assertOk();
        $dashboardResp->assertSee('Unduh Surat Selesai (PDF)');
    }

    public function test_admin_upload_surat_selesai_kelompok_automatically_distributes_to_all_members(): void
    {
        // Admin mengunggah 1 file surat keterangan selesai untuk kelompok melalui penempatan Andi (Ketua)
        $fileKelompok = UploadedFile::fake()->create('surat_kelompok_aptika.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
                'file_surat' => $fileKelompok,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verifikasi flash message menyebutkan distribusi ke seluruh 3 anggota kelompok
        $pesanSukses = session('success');
        $this->assertStringContainsString('3 anggota tim', $pesanSukses);
        $this->assertStringContainsString('Tim Aptika UGM', $pesanSukses);

        // Refresh seluruh entri penempatan anggota tim
        $this->penempatanAndi->refresh();
        $this->penempatanBudi->refresh();
        $this->penempatanCitra->refresh();

        // 1. Andi (Ketua) terisi
        $this->assertNotNull($this->penempatanAndi->file_surat_selesai);
        $this->assertNotNull($this->penempatanAndi->tgl_upload_surat_selesai);
        Storage::disk('local')->assertExists($this->penempatanAndi->file_surat_selesai);

        // 2. Budi (Anggota 1) OTOMATIS terisi dengan berkas yang sama
        $this->assertNotNull($this->penempatanBudi->file_surat_selesai);
        $this->assertEquals($this->penempatanAndi->file_surat_selesai, $this->penempatanBudi->file_surat_selesai);
        $this->assertNotNull($this->penempatanBudi->tgl_upload_surat_selesai);

        // 3. Citra (Anggota 2) OTOMATIS terisi dengan berkas yang sama
        $this->assertNotNull($this->penempatanCitra->file_surat_selesai);
        $this->assertEquals($this->penempatanAndi->file_surat_selesai, $this->penempatanCitra->file_surat_selesai);
        $this->assertNotNull($this->penempatanCitra->tgl_upload_surat_selesai);
    }

    public function test_all_team_members_can_download_surat_selesai_from_their_own_dashboards(): void
    {
        // Upload surat untuk kelompok
        $fileKelompok = UploadedFile::fake()->create('surat_kelompok.pdf', 300, 'application/pdf');
        $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
            'file_surat' => $fileKelompok,
        ]);

        // 1. Andi (Ketua) membuka dashboard & unduh
        $andiDash = $this->actingAs($this->andi)->get(route('peserta.dashboard'));
        $andiDash->assertOk();
        $andiDash->assertSee('Surat Keterangan Selesai Magang (Dokumen Resmi)');
        $andiDash->assertSee('Unduh Surat Selesai (PDF)');

        $andiDownload = $this->actingAs($this->andi)->get(route('peserta.selesai-magang.unduh'));
        $andiDownload->assertOk();
        $andiDownload->assertHeader('Content-Type', 'application/pdf');

        // 2. Budi (Anggota 1) membuka dashboard & unduh
        $budiDash = $this->actingAs($this->budi)->get(route('peserta.dashboard'));
        $budiDash->assertOk();
        $budiDash->assertSee('Surat Keterangan Selesai Magang (Dokumen Resmi)');
        $budiDash->assertSee('Unduh Surat Selesai (PDF)');

        $budiDownload = $this->actingAs($this->budi)->get(route('peserta.selesai-magang.unduh'));
        $budiDownload->assertOk();
        $budiDownload->assertHeader('Content-Type', 'application/pdf');

        // 3. Citra (Anggota 2) membuka dashboard & unduh
        $citraDash = $this->actingAs($this->citra)->get(route('peserta.dashboard'));
        $citraDash->assertOk();
        $citraDash->assertSee('Surat Keterangan Selesai Magang (Dokumen Resmi)');
        $citraDash->assertSee('Unduh Surat Selesai (PDF)');

        $citraDownload = $this->actingAs($this->citra)->get(route('peserta.selesai-magang.unduh'));
        $citraDownload->assertOk();
        $citraDownload->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unrelated_participant_cannot_download_other_participant_surat_idor_safe(): void
    {
        // Upload surat untuk Andi (Kelompok)
        $fileKelompok = UploadedFile::fake()->create('surat_kelompok.pdf', 300, 'application/pdf');
        $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
            'file_surat' => $fileKelompok,
        ]);

        // Peserta Individu (Dewi Sartika) belum diunggah suratnya
        $dewiDownload = $this->actingAs($this->pesertaIndividu)->get(route('peserta.selesai-magang.unduh'));
        $dewiDownload->assertNotFound();
    }
}
