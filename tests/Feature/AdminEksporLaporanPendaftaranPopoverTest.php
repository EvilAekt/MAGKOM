<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEksporLaporanPendaftaranPopoverTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected Pendaftaran $pendaftaran;

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
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now()->subDays(2),
        ]);
    }

    public function test_laporan_pendaftaran_menampilkan_popover_ekspor_dengan_3_format(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', ['tab' => 'pendaftaran']));

        $response->assertOk();
        $response->assertSee('id="btnToggleExportPopoverPendaftaran"', false);
        $response->assertSee('id="admExportPopoverPendaftaran"', false);
        $response->assertSee('Ekspor Rekap Cepat');
        $response->assertSee('Microsoft Excel');
        $response->assertSee('.xlsx');
        $response->assertSee('CSV Tabular');
        $response->assertSee('.csv');
        $response->assertSee('Dokumen Resmi (.pdf)');
        $response->assertSee('.pdf');
        $response->assertSee('id="btnDoExportDownloadPendaftaran"', false);
    }

    public function test_ekspor_laporan_pendaftaran_format_xlsx_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-pendaftaran-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
    }

    public function test_ekspor_laporan_pendaftaran_format_csv_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'csv']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-pendaftaran-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.csv', (string) $response->headers->get('content-disposition'));
    }

    public function test_ekspor_laporan_pendaftaran_format_pdf_berhasil_dan_tercatat_di_audit_log(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'pdf']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-pendaftaran-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('content-disposition'));

        // Pastikan audit log tercatat
        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->admin->id,
            'entitas' => 'pendaftaran',
        ]);

        $log = AuditLog::where('user_id', $this->admin->id)
            ->where('entitas', 'pendaftaran')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('pdf', json_encode($log->detail));
    }
}
