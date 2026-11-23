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

    protected Penempatan $penempatanAndi;

    protected Penempatan $penempatanBudi;

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
            'terisi' => 2,
            'aktif' => true,
        ]);

        // 2 Peserta dalam 1 kelompok magang: Andi (Ketua) dan Budi (Anggota)
        $this->andi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Andi Pratama', 'email' => 'andi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->andi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1001', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567891']);

        $this->budi = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Budi Santoso', 'email' => 'budi@magkom.test']);
        PesertaProfil::create(['user_id' => $this->budi->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '1002', 'jenjang' => JenjangEnum::S1, 'no_hp' => '081234567892']);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Aptika UGM',
            'ketua_user_id' => $this->andi->id,
            'kode_kelompok' => 'APTIKA-01',
            'institusi' => 'UGM',
        ]);

        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->budi->id, 'nama' => $this->budi->nama, 'email' => $this->budi->email]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->andi->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $this->kelompok->id,
            'durasi_bulan' => 3,
            'tipe_pendaftaran' => 'kelompok',
            'status' => 'diterima',
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        $pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $bidang->id,
            'email' => 'pembimbing@magkom.test',
        ]);

        // Penempatan Andi
        $this->penempatanAndi = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->andi->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-04-30',
        ]);

        // Penempatan Budi
        $this->penempatanBudi = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->budi->id,
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
        // Set penempatan Andi ke Aktif (belum selesai)
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

    public function test_admin_can_upload_surat_selesai_individually_for_each_team_member(): void
    {
        // 1. Admin upload surat untuk Andi (Ketua)
        $fileAndi = UploadedFile::fake()->create('surat_selesai_andi.pdf', 300, 'application/pdf');
        $respAndi = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
                'file_surat' => $fileAndi,
            ]);
        $respAndi->assertRedirect();
        $respAndi->assertSessionHas('success');

        $this->penempatanAndi->refresh();
        $this->assertNotNull($this->penempatanAndi->file_surat_selesai);
        $this->assertNotNull($this->penempatanAndi->tgl_upload_surat_selesai);
        Storage::disk('local')->assertExists($this->penempatanAndi->file_surat_selesai);

        // Surat Budi masih null (surat ketua tidak otomatis berlaku untuk anggota)
        $this->penempatanBudi->refresh();
        $this->assertNull($this->penempatanBudi->file_surat_selesai);

        // 2. Admin upload surat terpisah untuk Budi (Anggota)
        $fileBudi = UploadedFile::fake()->create('surat_selesai_budi.pdf', 400, 'application/pdf');
        $respBudi = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanBudi->id), [
                'file_surat' => $fileBudi,
            ]);
        $respBudi->assertRedirect();
        $respBudi->assertSessionHas('success');

        $this->penempatanBudi->refresh();
        $this->assertNotNull($this->penempatanBudi->file_surat_selesai);
        $this->assertNotEquals($this->penempatanAndi->file_surat_selesai, $this->penempatanBudi->file_surat_selesai);
        Storage::disk('local')->assertExists($this->penempatanBudi->file_surat_selesai);
    }

    public function test_each_peserta_can_only_view_and_download_their_own_surat_selesai(): void
    {
        // Upload surat untuk Andi
        $fileAndi = UploadedFile::fake()->create('surat_selesai_andi.pdf', 200, 'application/pdf');
        $this->actingAs($this->admin)->post(route('admin.penempatan.surat-selesai.upload', $this->penempatanAndi->id), [
            'file_surat' => $fileAndi,
        ]);
        $this->penempatanAndi->refresh();

        // 1. Andi membuka halaman Selesai Magang miliknya -> Terlihat siap diunduh
        $andiView = $this->actingAs($this->andi)->get(route('peserta.selesai-magang.index'));
        $andiView->assertOk();
        $andiView->assertSee('Dokumen Resmi Telah Terbit');
        $andiView->assertSee(route('peserta.selesai-magang.unduh'));

        // Andi mengunduh surat miliknya
        $andiDownload = $this->actingAs($this->andi)->get(route('peserta.selesai-magang.unduh'));
        $andiDownload->assertOk();

        // 2. Budi belum diunggah suratnya -> Halaman Budi menampilkan status proses penerbitan
        $budiView = $this->actingAs($this->budi)->get(route('peserta.selesai-magang.index'));
        $budiView->assertOk();
        $budiView->assertSee('Dokumen Sedang Dalam Proses Penerbitan');

        // Budi mencoba mengunduh surat -> 404 Not Found (Budi tidak bisa mengakses surat milik Andi)
        $budiDownload = $this->actingAs($this->budi)->get(route('peserta.selesai-magang.unduh'));
        $budiDownload->assertNotFound();
    }
}
