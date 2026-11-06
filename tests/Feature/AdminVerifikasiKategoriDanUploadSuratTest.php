<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminVerifikasiKategoriDanUploadSuratTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pesertaIndividu;

    protected User $pesertaKetuaKelompok;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Pendaftaran $pendaftaranIndividu;

    protected Pendaftaran $pendaftaranKelompok;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->pesertaIndividu = User::factory()->create([
            'nama' => 'Budi Peserta Individu',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->pesertaKetuaKelompok = User::factory()->create([
            'nama' => 'Siti Ketua Kelompok',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-11-30',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->pendaftaranIndividu = Pendaftaran::create([
            'user_id' => $this->pesertaIndividu->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'tipe' => 'mandiri',
            'kelompok_magang_id' => null,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $this->pesertaKetuaKelompok->id,
            'nama_kelompok' => 'Tim Riset Alpha',
        ]);
        $kelompok->anggota()->create([
            'nama' => 'Anggota Dua',
            'nim_nisn' => '12345678',
        ]);

        $this->pendaftaranKelompok = Pendaftaran::create([
            'user_id' => $this->pesertaKetuaKelompok->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);
    }

    public function test_admin_dapat_melihat_switcher_kategori_dan_badge_di_antrean_pendaftaran(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'));

        $response->assertOk();
        $response->assertSee('Semua Kategori');
        $response->assertSee('Individu');
        $response->assertSee('Kelompok');
        $response->assertSee('Budi Peserta Individu');
        $response->assertSee('Siti Ketua Kelompok');
        $response->assertSee('Tim: Tim Riset Alpha');
    }

    public function test_filter_kategori_hanya_menampilkan_pendaftaran_individu(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['tipe' => 'individu']));

        $response->assertOk();
        $response->assertSee('Budi Peserta Individu');
        $response->assertDontSee('Siti Ketua Kelompok');
        $response->assertDontSee('Tim: Tim Riset Alpha');
    }

    public function test_filter_kategori_hanya_menampilkan_pendaftaran_kelompok(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['tipe' => 'kelompok']));

        $response->assertOk();
        $response->assertSee('Siti Ketua Kelompok');
        $response->assertSee('Tim: Tim Riset Alpha');
        $response->assertDontSee('Budi Peserta Individu');
    }

    public function test_admin_menerima_pendaftaran_dengan_mengunggah_file_surat_keterangan_diterima(): void
    {
        $file = UploadedFile::fake()->create('surat_keputusan_dinas.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(
            route('admin.pendaftaran.putuskan', $this->pendaftaranIndividu),
            [
                'keputusan' => 'terima',
                'catatan' => 'Berkas pendaftaran memenuhi persyaratan.',
                'file_surat_diterima' => $file,
            ]
        );

        $response->assertRedirect(route('admin.pendaftaran.show', $this->pendaftaranIndividu));

        $this->pendaftaranIndividu->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $this->pendaftaranIndividu->status);
        $this->assertNotNull($this->pendaftaranIndividu->file_surat_diterima);
        Storage::disk('local')->assertExists($this->pendaftaranIndividu->file_surat_diterima);

        // Admin unduh surat
        $resDownloadAdmin = $this->actingAs($this->admin)->get(route('admin.pendaftaran.surat-diterima', $this->pendaftaranIndividu));
        $resDownloadAdmin->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $resDownloadAdmin->headers->get('content-type'));

        // Peserta unduh surat miliknya
        $resDownloadPeserta = $this->actingAs($this->pesertaIndividu)->get(route('peserta.pendaftaran.surat-diterima', $this->pendaftaranIndividu));
        $resDownloadPeserta->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $resDownloadPeserta->headers->get('content-type'));
    }

    public function test_validasi_menolak_file_surat_keterangan_diterima_yang_bukan_pdf(): void
    {
        $invalidFile = UploadedFile::fake()->create('surat.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->admin)->post(
            route('admin.pendaftaran.putuskan', $this->pendaftaranIndividu),
            [
                'keputusan' => 'terima',
                'catatan' => 'Berkas pendaftaran memenuhi persyaratan.',
                'file_surat_diterima' => $invalidFile,
            ]
        );

        $response->assertSessionHasErrors(['file_surat_diterima']);
        $this->pendaftaranIndividu->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $this->pendaftaranIndividu->status);
    }
}
