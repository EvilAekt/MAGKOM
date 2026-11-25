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

class AdminSuratSelesaiKonektivitasEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing;

    protected User $pesertaIndividu;

    protected User $ketuaKelompok;

    protected User $anggotaKelompok;

    protected Penempatan $penempatanIndividu;

    protected Penempatan $penempatanKetua;

    protected Penempatan $penempatanAnggota;

    protected KelompokMagang $kelompok;

    protected Pendaftaran $pendaftaranKelompok;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator Sleman',
            'email' => 'admin.surat@magkom.sleman.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Admin Sleman',
            'email' => 'superadmin.surat@magkom.sleman.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::create([
            'nama' => 'Magang November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status' => 'dibuka',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terpakai' => 3,
            'aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing Lapangan Aptika',
            'email' => 'pembimbing.aptika@magkom.sleman.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $bidang->id,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        // 1. Peserta Individu (Selesai Magang)
        $this->pesertaIndividu = User::factory()->create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@student.amikom.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        PesertaProfil::create([
            'user_id' => $this->pesertaIndividu->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Amikom Yogyakarta',
            'nim_nisn' => '21.11.3999',
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081299988877',
        ]);

        $pendaftaranIndividu = Pendaftaran::create([
            'user_id' => $this->pesertaIndividu->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'individu',
            'durasi_bulan' => 3,
            'status' => 'diterima',
            'tgl_mulai' => '2026-08-01',
            'tgl_selesai' => '2026-10-31',
        ]);

        $this->penempatanIndividu = Penempatan::create([
            'pendaftaran_id' => $pendaftaranIndividu->id,
            'peserta_id' => $this->pesertaIndividu->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Selesai,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => '2026-08-01',
            'tgl_selesai' => '2026-10-31',
            'tgl_ditetapkan_selesai' => '2026-10-31 16:00:00',
        ]);

        // 2. Rombongan Kelompok (2 Anggota: Ketua & Anggota)
        $this->ketuaKelompok = User::factory()->create([
            'nama' => 'Aditya Pratama',
            'email' => 'aditya@ugm.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        PesertaProfil::create([
            'user_id' => $this->ketuaKelompok->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '20/456789/TK/51234',
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081300011122',
        ]);

        $this->anggotaKelompok = User::factory()->create([
            'nama' => 'Nabila Azzahra',
            'email' => 'nabila@ugm.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        PesertaProfil::create([
            'user_id' => $this->anggotaKelompok->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '20/456790/TK/51235',
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081300011133',
        ]);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim SPBE Digital UGM',
            'ketua_user_id' => $this->ketuaKelompok->id,
            'kode_kelompok' => 'SPBE-UGM-01',
            'institusi' => 'Universitas Gadjah Mada',
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $this->kelompok->id,
            'user_id' => $this->anggotaKelompok->id,
            'nama' => $this->anggotaKelompok->nama,
            'email' => $this->anggotaKelompok->email,
        ]);

        $this->pendaftaranKelompok = Pendaftaran::create([
            'user_id' => $this->ketuaKelompok->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $this->kelompok->id,
            'tipe' => 'kelompok',
            'durasi_bulan' => 3,
            'status' => 'diterima',
            'tgl_mulai' => '2026-08-01',
            'tgl_selesai' => '2026-10-31',
        ]);

        $this->penempatanKetua = Penempatan::create([
            'pendaftaran_id' => $this->pendaftaranKelompok->id,
            'peserta_id' => $this->ketuaKelompok->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Selesai,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => '2026-08-01',
            'tgl_selesai' => '2026-10-31',
            'tgl_ditetapkan_selesai' => '2026-10-31 16:00:00',
        ]);

        $this->penempatanAnggota = Penempatan::create([
            'pendaftaran_id' => $this->pendaftaranKelompok->id,
            'peserta_id' => $this->anggotaKelompok->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Selesai,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => '2026-08-01',
            'tgl_selesai' => '2026-10-31',
            'tgl_ditetapkan_selesai' => '2026-10-31 16:00:00',
        ]);
    }

    public function test_admin_melihat_halaman_surat_selesai_dan_kedua_tab(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index'));

        $response->assertOk();
        $response->assertSee('Daftar Surat Selesai Magang');
        $response->assertSee('Peserta Individu');
        $response->assertSee('Rombongan Kelompok');
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('Universitas Amikom Yogyakarta');
        $response->assertSee('BELUM DIUNGGAH');

        // Buka Tab Kelompok
        $responseKelompok = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', ['tab' => 'kelompok']));
        $responseKelompok->assertOk();
        $responseKelompok->assertSee('Tim SPBE Digital UGM');
        $responseKelompok->assertSee('2 Anggota Terdaftar');
    }

    public function test_admin_dapat_mencari_dan_memfilter_peserta_individu(): void
    {
        // 1. Cari Keyword Amikom
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'individu',
            'q' => 'Amikom',
        ]));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Siti Nurhaliza');

        // 2. Filter status surat belum
        $responseFilter = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'individu',
            'status_surat' => 'belum',
        ]));
        $responseFilter->assertOk();
        $responseFilter->assertSee('Siti Nurhaliza');

        // 3. Filter status surat sudah (harus kosong karena belum ada berkas diunggah)
        $responseSudah = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'individu',
            'status_surat' => 'sudah',
        ]));
        $responseSudah->assertOk();
        $responseSudah->assertDontSee('Siti Nurhaliza');
    }

    public function test_admin_masuk_ke_mode_fokus_individu_dan_mengunggah_surat_resmi(): void
    {
        // 1. Masuk ke mode fokus Tier 2
        $responseFokus = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'individu',
            'masuk_individu' => $this->penempatanIndividu->id,
        ]));
        $responseFokus->assertOk();
        $responseFokus->assertSee('Unggah Surat Selesai: Siti Nurhaliza');
        $responseFokus->assertSee('Informasi Penempatan Magang');
        $responseFokus->assertSee('Formulir Unggah Surat Selesai');

        // 2. Unggah berkas PDF
        $file = UploadedFile::fake()->create('surat_selesai_siti.pdf', 600, 'application/pdf');

        $responseUpload = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
                'file_surat' => $file,
            ]);

        $responseUpload->assertRedirect();
        $responseUpload->assertSessionHas('success');

        $this->penempatanIndividu->refresh();
        $this->assertNotNull($this->penempatanIndividu->file_surat_selesai);
        $this->assertNotNull($this->penempatanIndividu->tgl_upload_surat_selesai);
        Storage::disk('local')->assertExists($this->penempatanIndividu->file_surat_selesai);

        // Verifikasi audit log tercatat
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'entitas_id' => $this->penempatanIndividu->id,
            'user_id' => $this->admin->id,
        ]);

        // Verifikasi peserta dapat mengunduh surat resmi
        $responseUnduh = $this->actingAs($this->pesertaIndividu)->get(route('peserta.selesai-magang.unduh'));
        $responseUnduh->assertOk();
        $this->assertEquals('application/pdf', $responseUnduh->headers->get('Content-Type'));
    }

    public function test_admin_masuk_ke_mode_fokus_kelompok_dan_otomatis_mendistribusikan_ke_semua_anggota(): void
    {
        // 1. Masuk ke mode fokus kelompok Tier 2
        $responseFokus = $this->actingAs($this->admin)->get(route('admin.surat-selesai.index', [
            'tab' => 'kelompok',
            'masuk_kelompok' => $this->pendaftaranKelompok->id,
        ]));
        $responseFokus->assertOk();
        $responseFokus->assertSee('Unggah Surat Selesai: Tim SPBE Digital UGM');
        $responseFokus->assertSee('Aditya Pratama');
        $responseFokus->assertSee('Nabila Azzahra');

        // 2. Unggah berkas tim via penempatan ketua
        $file = UploadedFile::fake()->create('surat_selesai_tim_ugm.pdf', 850, 'application/pdf');

        $responseUpload = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanKetua), [
                'file_surat' => $file,
            ]);

        $responseUpload->assertRedirect();
        $responseUpload->assertSessionHas('success');

        $this->penempatanKetua->refresh();
        $this->penempatanAnggota->refresh();

        // Keduanya menerima path file yang sama
        $this->assertNotNull($this->penempatanKetua->file_surat_selesai);
        $this->assertEquals($this->penempatanKetua->file_surat_selesai, $this->penempatanAnggota->file_surat_selesai);
        Storage::disk('local')->assertExists($this->penempatanKetua->file_surat_selesai);

        // Keduanya bisa mengunduh secara mandiri
        $this->actingAs($this->ketuaKelompok)->get(route('peserta.selesai-magang.unduh'))->assertOk();
        $this->actingAs($this->anggotaKelompok)->get(route('peserta.selesai-magang.unduh'))->assertOk();
    }

    public function test_penggantian_berkas_surat_menghapus_berkas_lama_yang_tidak_terpakai_orphan_cleanup(): void
    {
        // 1. Unggah berkas pertama
        $file1 = UploadedFile::fake()->create('surat_awal.pdf', 400, 'application/pdf');
        $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $file1,
        ]);
        $this->penempatanIndividu->refresh();
        $oldPath = $this->penempatanIndividu->file_surat_selesai;
        Storage::disk('local')->assertExists($oldPath);

        // 2. Unggah berkas revisi kedua
        $file2 = UploadedFile::fake()->create('surat_revisi.pdf', 500, 'application/pdf');
        $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $file2,
        ]);
        $this->penempatanIndividu->refresh();
        $newPath = $this->penempatanIndividu->file_surat_selesai;

        // Berkas lama harus terhapus fisik dari disk lokal
        $this->assertNotEquals($oldPath, $newPath);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($newPath);
    }

    public function test_validasi_menolak_berkas_non_pdf_dan_berkas_melebihi_2mb(): void
    {
        // 1. File gambar JPEG (bukan PDF)
        $fileGambar = UploadedFile::fake()->create('surat.jpg', 300, 'image/jpeg');
        $responseGambar = $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $fileGambar,
        ]);
        $responseGambar->assertSessionHasErrors(['file_surat']);

        // 2. File PDF melebihi 2MB (2500 KB)
        $fileBesar = UploadedFile::fake()->create('surat_jumbo.pdf', 2500, 'application/pdf');
        $responseBesar = $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $fileBesar,
        ]);
        $responseBesar->assertSessionHasErrors(['file_surat']);
    }

    public function test_penolakan_unggah_jika_penempatan_belum_berstatus_selesai(): void
    {
        $this->penempatanIndividu->update(['status' => StatusPenempatanEnum::Aktif]);

        $file = UploadedFile::fake()->create('surat.pdf', 300, 'application/pdf');
        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $file,
        ]);

        $response->assertSessionHasErrors(['file_surat']);
        $this->assertNull($this->penempatanIndividu->fresh()->file_surat_selesai);
    }

    public function test_superadmin_dilarang_mengunggah_surat_selesai_sec_12(): void
    {
        $file = UploadedFile::fake()->create('surat.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->superAdmin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $file,
        ]);

        $response->assertForbidden();
    }

    public function test_peserta_dan_pembimbing_dilarang_mengunggah_surat_selesai(): void
    {
        $file = UploadedFile::fake()->create('surat.pdf', 300, 'application/pdf');

        // Peserta mencoba upload
        $this->actingAs($this->pesertaIndividu)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), ['file_surat' => $file])
            ->assertForbidden();

        // Pembimbing mencoba upload
        $this->actingAs($this->pembimbing)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), ['file_surat' => $file])
            ->assertForbidden();
    }

    public function test_admin_dapat_mengunduh_salinan_surat_selesai(): void
    {
        $file = UploadedFile::fake()->create('surat_siti.pdf', 400, 'application/pdf');
        $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
            'file_surat' => $file,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.surat-selesai.unduh', $this->penempatanIndividu));
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}

