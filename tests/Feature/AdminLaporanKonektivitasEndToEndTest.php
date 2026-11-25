<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
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

class AdminLaporanKonektivitasEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    private function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $u = new User;
        $u->nama = $nama;
        $u->email = $email;
        $u->password = 'Password123!';
        $u->role = $role;
        $u->status_aktif = true;
        $u->email_verified_at = now();
        if ($role === RoleEnum::Pembimbing && isset($this->bidang)) {
            $u->bidang_id = $this->bidang->id;
        }
        $u->save();

        return $u;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika (EGOV)',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'jenjang' => JenjangEnum::S1,
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(15)->toDateString(),
            'kuota_total' => 20,
            'kuota_terisi' => 2,
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 2,
            'aktif' => true,
        ]);

        $this->admin = $this->createUser('Admin Verifikator', 'admin.laporan@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing = $this->createUser('Pembimbing Kominfo', 'pembimbing.laporan@slemankab.go.id', RoleEnum::Pembimbing);
    }

    public function test_admin_can_access_laporan_hub_and_see_kpi_cards_and_analytics_widgets(): void
    {
        $peserta1 = $this->createUser('Peserta Satu', 'p1@laporan.test', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta1->id,
            'nim_nisn' => '1122334455',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-01-01',
            'alamat' => 'Sleman',
            'no_hp' => '081200000001',
        ]);

        Pendaftaran::create([
            'user_id' => $peserta1->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        Penempatan::create([
            'peserta_id' => $peserta1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertOk();
        $response->assertSee('Pusat Rekapitulasi Data Magang');
        $response->assertSee('Total Pengajuan');
        $response->assertSee('Peserta Diterima');
        $response->assertSee('Penempatan Aktif');
        $response->assertSee('Magang Selesai');
        $response->assertSee('Tingkat Keterisian Kuota per Jurusan');
        $response->assertSee('Proporsi Jenjang Calon Peserta');

        $metrics = $response->viewData('metricCards');
        $this->assertEquals(1, $metrics['total_pendaftaran']);
        $this->assertEquals(1, $metrics['total_diterima']);
        $this->assertEquals(1, $metrics['penempatan_aktif']);
    }

    public function test_admin_can_filter_pendaftaran_by_status_perlu_perbaikan(): void
    {
        $peserta = $this->createUser('Peserta Perlu Perbaikan', 'revisi@laporan.test', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '7788990011',
            'institusi' => 'UNY',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-02-02',
            'alamat' => 'Sleman',
            'no_hp' => '081200000002',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::PerluPerbaikan,
            'catatan' => 'Surat pengantar belum bertanda tangan basah.',
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'tab' => 'pendaftaran',
            'status' => 'perlu_perbaikan',
        ]));

        $response->assertOk();
        $response->assertSee('Peserta Perlu Perbaikan');
        $response->assertSee('Perlu Perbaikan');

        $pendaftaranData = $response->viewData('pendaftaranData');
        $this->assertTrue($pendaftaranData->contains('id', $pendaftaran->id));
    }

    public function test_admin_can_filter_penempatan_tab_with_all_filters(): void
    {
        $peserta = $this->createUser('Peserta Penempatan Tab', 'penempatan.tab@laporan.test', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '4455667788',
            'institusi' => 'UIN Sunan Kalijaga',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-03-03',
            'alamat' => 'Sleman',
            'no_hp' => '081200000003',
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'tab' => 'penempatan',
            'status' => 'aktif',
            'jalur' => 'umum',
        ]));

        $response->assertOk();
        $response->assertSee('Peserta Penempatan Tab');

        $penempatanData = $response->viewData('penempatanData');
        $this->assertTrue($penempatanData->contains('id', $penempatan->id));
    }

    public function test_admin_can_export_pendaftaran_in_xlsx_csv_and_pdf_formats_with_audit_trail(): void
    {
        $peserta = $this->createUser('Peserta Ekspor Pendaftaran', 'ekspor.pdt@laporan.test', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '9988776655',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-04-04',
            'alamat' => 'Sleman',
            'no_hp' => '081200000004',
        ]);

        Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        // 1. Ekspor CSV
        $responseCsv = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
        ]));
        $responseCsv->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $responseCsv->headers->get('content-type'));

        // 2. Ekspor XLSX
        $responseXlsx = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'xlsx',
        ]));
        $responseXlsx->assertOk();
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $responseXlsx->headers->get('content-type'));

        // 3. Ekspor PDF
        $responsePdf = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'pdf',
        ]));
        $responsePdf->assertOk();
        $this->assertEquals('application/pdf', $responsePdf->headers->get('content-type'));

        // 4. Verifikasi Audit Log
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'pendaftaran',
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::AksesData->value,
        ]);
    }

    public function test_admin_can_export_penempatan_filtered_by_nim_nisn_and_audit_trail_recorded(): void
    {
        $peserta = $this->createUser('Peserta Ekspor Penempatan', 'ekspor.pen@laporan.test', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '3322110099',
            'institusi' => 'Politeknik Negeri Sleman',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-05-05',
            'alamat' => 'Sleman',
            'no_hp' => '081200000005',
        ]);

        Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan', [
            'format' => 'csv',
            'q' => '3322110099',
        ]));

        $response->assertOk();
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'user_id' => $this->admin->id,
            'tipe' => TipeAuditEnum::AksesData->value,
        ]);
    }

    public function test_spreadsheet_export_sanitizes_dangerous_formula_injection(): void
    {
        $pesertaBahaya = $this->createUser('=CMD|calc!A0', 'hacker@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $pesertaBahaya->id,
            'nim_nisn' => '+62812345678',
            'institusi' => '@SUM(1+1)',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-06-06',
            'alamat' => 'Sleman',
            'no_hp' => '081200000006',
        ]);

        Pendaftaran::create([
            'user_id' => $pesertaBahaya->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', [
            'format' => 'csv',
        ]));

        $response->assertOk();
        $content = $response->streamedContent();

        // Formula diawali tanda kutip tunggal (') agar dinetralkan
        $this->assertStringContainsString("'=CMD|calc!A0", $content);
        $this->assertStringContainsString("'+62812345678", $content);
        $this->assertStringContainsString("'@SUM(1+1)", $content);
    }
}
