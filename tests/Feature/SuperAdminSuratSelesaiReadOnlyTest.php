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
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminSuratSelesaiReadOnlyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pesertaIndividu;

    protected User $pesertaKelompok;

    protected Penempatan $penempatanIndividu;

    protected Penempatan $penempatanKelompok;

    protected KelompokMagang $kelompok;

    protected Pendaftaran $pendaftaranKelompok;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed(PengaturanSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin->value,
            'email' => 'superadmin@magkom.test',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin->value,
            'email' => 'admin@magkom.test',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'kode' => 'APTIKA',
            'kuota' => 20,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
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
            'terisi' => 2,
            'aktif' => true,
        ]);

        $pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $bidang->id,
            'email' => 'pembimbing@magkom.test',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        // Peserta Individu
        $this->pesertaIndividu = User::factory()->create([
            'role' => RoleEnum::Peserta->value,
            'nama' => 'Peserta Individu Selesai',
            'email' => 'individu@magkom.test',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        PesertaProfil::create([
            'user_id' => $this->pesertaIndividu->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '08123456789',
        ]);

        $pendaftaranIndividu = Pendaftaran::create([
            'user_id' => $this->pesertaIndividu->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'status' => 'diterima',
            'tipe' => 'individu',
            'durasi_bulan' => 3,
        ]);

        $this->penempatanIndividu = Penempatan::create([
            'peserta_id' => $this->pesertaIndividu->id,
            'pendaftaran_id' => $pendaftaranIndividu->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online->value,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->subDay()->toDateString(),
        ]);

        // Peserta Kelompok
        $this->pesertaKelompok = User::factory()->create([
            'role' => RoleEnum::Peserta->value,
            'nama' => 'Ketua Kelompok Alpha',
            'email' => 'ketua@magkom.test',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        PesertaProfil::create([
            'user_id' => $this->pesertaKelompok->id,
            'nim_nisn' => '9876543210',
            'institusi' => 'Institut Teknologi Bandung',
            'jurusan_id' => $jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '08987654321',
        ]);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Kelompok Alpha',
            'kode_kelompok' => 'ALPHA-01',
            'institusi' => 'Institut Teknologi Bandung',
            'ketua_user_id' => $this->pesertaKelompok->id,
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $this->kelompok->id,
            'user_id' => $this->pesertaKelompok->id,
            'nama' => $this->pesertaKelompok->nama,
            'email' => $this->pesertaKelompok->email,
            'role_kelompok' => 'ketua',
        ]);

        $this->pendaftaranKelompok = Pendaftaran::create([
            'user_id' => $this->pesertaKelompok->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'status' => 'diterima',
            'tipe' => 'kelompok',
            'durasi_bulan' => 3,
            'kelompok_magang_id' => $this->kelompok->id,
        ]);

        $this->penempatanKelompok = Penempatan::create([
            'peserta_id' => $this->pesertaKelompok->id,
            'pendaftaran_id' => $this->pendaftaranKelompok->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online->value,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->subDay()->toDateString(),
        ]);
    }

    /**
     * SuperAdmin dapat mengakses rute index surat selesai dan melihat badge Mode Lihat Saja (Read-Only).
     */
    public function test_superadmin_can_access_surat_selesai_index_and_sees_readonly_badge(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.surat-selesai.index'));

        $response->assertOk();
        $response->assertSee('Mode Lihat Saja (Read-Only)');
    }

    /**
     * Pada fokus masuk individu, SuperAdmin melihat panel pengawasan read-only dan tidak ada form upload.
     */
    public function test_superadmin_sees_readonly_notice_instead_of_upload_form_on_individual_focus(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.surat-selesai.index', [
                'tab' => 'individu',
                'masuk_individu' => $this->penempatanIndividu->id,
            ]));

        $response->assertOk();
        $response->assertSee('Mode Pengawasan SuperAdmin (Read-Only)');
        $response->assertSee('Akses Unggah Dibatasi');
        $response->assertDontSee('id="file_surat_ind"', false);
        $response->assertDontSee('Unggah Surat Resmi');
    }

    /**
     * Pada fokus masuk kelompok, SuperAdmin melihat panel pengawasan read-only dan tidak ada form upload tim.
     */
    public function test_superadmin_sees_readonly_notice_instead_of_upload_form_on_group_focus(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.surat-selesai.index', [
                'tab' => 'kelompok',
                'masuk_kelompok' => $this->pendaftaranKelompok->id,
            ]));

        $response->assertOk();
        $response->assertSee('Mode Pengawasan SuperAdmin (Read-Only)');
        $response->assertSee('Informasi Penerbitan Berkas Tim');
        $response->assertSee('Akses Unggah Dibatasi');
        $response->assertDontSee('id="file_surat_kel"', false);
        $response->assertDontSee('Pilih Berkas PDF Surat Selesai Tim');
    }

    /**
     * SuperAdmin dilarang mengunggah berkas surat selesai (HTTP 403 Forbidden).
     */
    public function test_superadmin_is_forbidden_from_uploading_surat_selesai_post_request(): void
    {
        $file = UploadedFile::fake()->create('surat_selesai.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
                'file_surat' => $file,
            ]);

        $response->assertForbidden();
    }

    /**
     * Admin operasional tetap dapat melihat formulir unggah dan mengunggah berkas dengan sukses.
     */
    public function test_admin_can_view_upload_form_and_successfully_upload_surat_selesai(): void
    {
        // 1. Admin melihat form di tampilan terfokus
        $viewResponse = $this->actingAs($this->admin)
            ->get(route('admin.surat-selesai.index', [
                'tab' => 'individu',
                'masuk_individu' => $this->penempatanIndividu->id,
            ]));

        $viewResponse->assertOk();
        $viewResponse->assertSee('Formulir Unggah Surat Selesai');
        $viewResponse->assertSee('id="file_surat_ind"', false);
        $viewResponse->assertSee('Unggah Surat Resmi');

        // 2. Admin mengunggah berkas
        $file = UploadedFile::fake()->create('surat_selesai.pdf', 500, 'application/pdf');

        $uploadResponse = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanIndividu), [
                'file_surat' => $file,
            ]);

        $uploadResponse->assertSessionHas('success');

        $this->penempatanIndividu->refresh();
        $this->assertNotNull($this->penempatanIndividu->file_surat_selesai);
    }
}
