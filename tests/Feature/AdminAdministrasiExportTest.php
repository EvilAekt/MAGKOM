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
}
