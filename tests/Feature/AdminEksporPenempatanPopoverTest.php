<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pengaturan;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEksporPenempatanPopoverTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        Pengaturan::setNilai('maks_peserta_aktif_per_pembimbing', '6');

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
    }

    public function test_halaman_admin_penempatan_menampilkan_popover_ekspor_dengan_3_format(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));

        $response->assertOk();
        $response->assertSee('id="btnToggleExportPopover"', false);
        $response->assertSee('id="admExportPopover"', false);
        $response->assertSee('Ekspor Rekap Cepat');
        $response->assertSee('Microsoft Excel');
        $response->assertSee('.xlsx');
        $response->assertSee('CSV Tabular');
        $response->assertSee('.csv');
        $response->assertSee('Dokumen Resmi (.pdf)');
        $response->assertSee('.pdf');
        $response->assertSee('id="btnDoExportDownload"', false);
    }

    public function test_ekspor_rekap_surat_diterima_format_xlsx_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-surat-diterima-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
    }

    public function test_ekspor_rekap_surat_diterima_format_csv_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'csv']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-surat-diterima-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.csv', (string) $response->headers->get('content-disposition'));
    }

    public function test_ekspor_rekap_surat_diterima_format_pdf_berhasil_dan_tercatat_di_audit_log(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'pdf']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-surat-diterima-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('content-disposition'));

        // Pastikan audit log tercatat
        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->admin->id,
            'entitas' => 'penempatan',
        ]);

        $log = AuditLog::where('user_id', $this->admin->id)
            ->where('entitas', 'penempatan')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('pdf', json_encode($log->detail));
    }
}
