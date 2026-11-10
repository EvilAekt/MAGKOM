<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAdministrasiExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected Pendaftaran $pendaftaran;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika (EGOV)',
            'deskripsi' => 'Bidang Layanan EGOV',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 1,
            'aktif' => true,
        ]);

        $this->admin = new User;
        $this->admin->nama = 'Admin Magang';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing EGOV';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Calon Mahasiswa Sleman';
        $this->peserta->email = 'calon@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '220001001',
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234567890',
        ]);

        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 1,
            'created_at' => now()->subDay(),
        ]);

        $this->penempatan = Penempatan::create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_selesai' => now()->addMonth()->endOfMonth()->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'no_surat' => '005/KOMINFO/2026',
        ]);
    }

    public function test_halaman_admin_pendaftaran_menampilkan_popover_ekspor_dengan_3_format(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'));

        $response->assertOk();
        $response->assertSee('id="btnToggleExportPopoverPendaftaran"', false);
        $response->assertSee('id="admExportPopoverPendaftaran"', false);
        $response->assertSee('adm-export-popover-right');
        $response->assertSee('adm-pointer-right');
        $response->assertSee('Ekspor Rekap Cepat');
        $response->assertSee('Microsoft Excel');
        $response->assertSee('.xlsx');
        $response->assertSee('CSV Tabular');
        $response->assertSee('.csv');
        $response->assertSee('Dokumen Resmi (.pdf)');
        $response->assertSee('.pdf');
        $response->assertSee('triggerPendaftaranExport');
    }

    public function test_halaman_admin_penempatan_menampilkan_popover_anti_clipping(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));

        $response->assertOk();
        $response->assertSee('id="admExportPopover"', false);
        $response->assertSee('adm-export-popover-right');
        $response->assertSee('adm-pointer-right');
    }

    public function test_ekspor_laporan_pendaftaran_format_xlsx(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('Content-Disposition'));

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->admin->id,
            'entitas' => 'pendaftaran',
        ]);
    }

    public function test_ekspor_laporan_pendaftaran_format_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'csv']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.csv', (string) $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $content);
        $this->assertStringContainsString('Calon Mahasiswa Sleman', $content);
    }

    public function test_ekspor_laporan_pendaftaran_format_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'pdf']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_ekspor_laporan_pendaftaran_dengan_filter_keyword_q(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'q' => 'Gadjah Mada',
            'tipe' => 'mandiri',
        ]));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Universitas Gadjah Mada', $content);
    }

    public function test_ekspor_laporan_penempatan_format_xlsx(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_ekspor_laporan_penempatan_format_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan', ['format' => 'csv']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.csv', (string) $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $content);
        $this->assertStringContainsString('Calon Mahasiswa Sleman', $content);
    }

    public function test_ekspor_laporan_penempatan_format_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan', ['format' => 'pdf']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_ekspor_penempatan_surat_diterima_semua_format(): void
    {
        // XLSX
        $responseXlsx = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'xlsx']));
        $responseXlsx->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $responseXlsx->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', (string) $responseXlsx->headers->get('Content-Disposition'));

        // CSV
        $responseCsv = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'csv']));
        $responseCsv->assertOk();
        $this->assertStringContainsString('text/csv', (string) $responseCsv->headers->get('Content-Type'));
        $this->assertStringContainsString('.csv', (string) $responseCsv->headers->get('Content-Disposition'));

        // PDF
        $responsePdf = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'pdf']));
        $responsePdf->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $responsePdf->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', (string) $responsePdf->headers->get('Content-Disposition'));
    }

    public function test_ekspor_pendaftaran_menerapkan_filter_tipe_bulan_jurusan_status_dan_kombinasi(): void
    {
        // Setup data kedua dengan atribut berbeda
        $jurusan2 = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulan2 = BulanMagang::create([
            'bulan_mulai' => now()->addMonths(2)->startOfMonth(),
            'tgl_buka' => now()->toDateString(),
            'tgl_tutup' => now()->addDays(30)->toDateString(),
            'aktif' => true,
        ]);

        $kuota2 = Kuota::create([
            'bulan_magang_id' => $bulan2->id,
            'jurusan_id' => $jurusan2->id,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
        ]);

        $peserta2 = new User([
            'nama' => 'Bambang Sudarsono',
            'email' => 'bambang@uny.ac.id',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $peserta2->role = RoleEnum::Peserta;
        $peserta2->email_verified_at = now();
        $peserta2->save();

        PesertaProfil::create([
            'user_id' => $peserta2->id,
            'nim_nisn' => '220002002',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $jurusan2->id,
            'no_hp' => '089876543210',
        ]);

        $pendaftaran2 = Pendaftaran::create([
            'user_id' => $peserta2->id,
            'bulan_magang_id' => $bulan2->id,
            'kuota_id' => $kuota2->id,
            'tipe' => 'kelompok',
            'status' => StatusPendaftaranEnum::Diajukan,
            'durasi_bulan' => 2,
            'created_at' => now(),
        ]);

        // 1. Uji Filter Tipe: mandiri
        $respTipeMandiri = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'tipe' => 'mandiri',
        ]));
        $respTipeMandiri->assertOk();
        $contentMandiri = $respTipeMandiri->streamedContent();
        $this->assertStringContainsString('Calon Mahasiswa Sleman', $contentMandiri);
        $this->assertStringNotContainsString('Bambang Sudarsono', $contentMandiri);

        // 2. Uji Filter Tipe: kelompok
        $respTipeKelompok = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'tipe' => 'kelompok',
        ]));
        $respTipeKelompok->assertOk();
        $contentKelompok = $respTipeKelompok->streamedContent();
        $this->assertStringContainsString('Bambang Sudarsono', $contentKelompok);
        $this->assertStringNotContainsString('Calon Mahasiswa Sleman', $contentKelompok);

        // 3. Uji Filter Bulan Magang
        $respBulan = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'bulan_magang_id' => $this->bulan->id,
        ]));
        $respBulan->assertOk();
        $contentBulan = $respBulan->streamedContent();
        $this->assertStringContainsString('Calon Mahasiswa Sleman', $contentBulan);
        $this->assertStringNotContainsString('Bambang Sudarsono', $contentBulan);

        // 4. Uji Filter Jurusan
        $respJurusan = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'jurusan_id' => $jurusan2->id,
        ]));
        $respJurusan->assertOk();
        $contentJurusan = $respJurusan->streamedContent();
        $this->assertStringContainsString('Bambang Sudarsono', $contentJurusan);
        $this->assertStringNotContainsString('Calon Mahasiswa Sleman', $contentJurusan);

        // 5. Uji Filter Status
        $respStatus = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'status' => StatusPendaftaranEnum::Diterima->value,
        ]));
        $respStatus->assertOk();
        $contentStatus = $respStatus->streamedContent();
        $this->assertStringContainsString('Calon Mahasiswa Sleman', $contentStatus);
        $this->assertStringNotContainsString('Bambang Sudarsono', $contentStatus);

        // 6. Uji Kombinasi Seluruh Filter (q + tipe + bulan + jurusan + status)
        $respKombinasi = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
            'q' => 'Bambang',
            'tipe' => 'kelompok',
            'bulan_magang_id' => $bulan2->id,
            'jurusan_id' => $jurusan2->id,
            'status' => StatusPendaftaranEnum::Diajukan->value,
        ]));
        $respKombinasi->assertOk();
        $contentKombinasi = $respKombinasi->streamedContent();
        $this->assertStringContainsString('Bambang Sudarsono', $contentKombinasi);
        $this->assertStringNotContainsString('Calon Mahasiswa Sleman', $contentKombinasi);

        // 7. Uji Konten PDF dan XLSX dengan kombinasi filter
        $respPdfFiltered = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'pdf',
            'q' => 'Bambang',
            'status' => StatusPendaftaranEnum::Diajukan->value,
        ]));
        $respPdfFiltered->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $respPdfFiltered->headers->get('Content-Type'));

        $respXlsxFiltered = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'xlsx',
            'q' => 'Bambang',
            'status' => StatusPendaftaranEnum::Diajukan->value,
        ]));
        $respXlsxFiltered->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $respXlsxFiltered->headers->get('Content-Type'));
    }
}
