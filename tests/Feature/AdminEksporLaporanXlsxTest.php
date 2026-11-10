<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class AdminEksporLaporanXlsxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulanMagang;

    protected SlotKhusus $slotKhusus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Layanan E-Government',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'it',
            'aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Magang Sleman',
            'email' => 'admin@magkom.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'SuperAdmin Kadis',
            'email' => 'superadmin@magkom.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Budi Santoso, S.Kom',
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'bidang_id' => $this->bidang->id,
            'must_change_password' => false,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => Carbon::parse('2026-11-01'),
            'tgl_buka' => Carbon::parse('2026-10-01'),
            'tgl_tutup' => Carbon::parse('2026-10-25'),
            'aktif' => true,
        ]);

        $this->slotKhusus = SlotKhusus::create([
            'nama_kemitraan' => 'MoU Universitas Negeri Yogyakarta',
            'instansi' => 'UNY',
            'jumlah' => 5,
            'terisi' => 0,
            'bulan_mulai' => Carbon::parse('2026-11-01'),
            'durasi_bulan' => 3,
            'dibuat_oleh' => $this->admin->id,
            'aktif' => true,
        ]);
    }

    public function test_ekspor_xlsx_pendaftaran_mengembalikan_content_type_dan_zip_struktur_valid(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        // Simpan sementara konten ke file dan buka sebagai zip
        $tempZip = tempnam(sys_get_temp_dir(), 'test_zip_');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($tempZip, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('xl/styles.xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));

        $stylesXml = $zip->getFromName('xl/styles.xml');
        $this->assertStringContainsString('FF1E3A8A', $stylesXml); // Sleman Blue fill
        $this->assertStringContainsString('FFD1D5DB', $stylesXml); // All borders

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('Tanggal Diajukan', $sheetXml);
        $this->assertStringContainsString('customWidth="1"', $sheetXml);

        $zip->close();
        @unlink($tempZip);
    }

    public function test_ekspor_xlsx_penempatan_memuat_jurusan_jalur_khusus_dan_border_styling(): void
    {
        $pesertaKhusus = User::factory()->create([
            'nama' => 'Jarwo Kemitraan',
            'email' => 'jarwo@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        PesertaProfil::create([
            'user_id' => $pesertaKhusus->id,
            'nim_nisn' => '24.03.2255',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '08123456789',
        ]);

        Penempatan::create([
            'pendaftaran_id' => null,
            'kuota_id' => null,
            'slot_khusus_id' => $this->slotKhusus->id,
            'peserta_id' => $pesertaKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::parse('2026-11-01'),
            'tgl_selesai' => Carbon::parse('2027-01-31'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan', ['format' => 'xlsx']));

        $response->assertOk();

        $tempZip = tempnam(sys_get_temp_dir(), 'test_pen_');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($tempZip, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        // Pastikan nama jurusan peserta jalur khusus termuat
        $this->assertStringContainsString('Teknik Informatika', $sheetXml);
        $this->assertStringContainsString('Jarwo Kemitraan', $sheetXml);
        $this->assertStringContainsString('Jalur Khusus', $sheetXml);
        $this->assertStringContainsString('Aktif', $sheetXml);

        $zip->close();
        @unlink($tempZip);
    }

    public function test_ekspor_xlsx_mencegah_formula_injection(): void
    {
        $pesertaHacker = User::factory()->create([
            'nama' => '=cmd|hacker',
            'email' => 'hacker@example.com',
            'role' => RoleEnum::Peserta,
        ]);

        PesertaProfil::create([
            'user_id' => $pesertaHacker->id,
            'nim_nisn' => '+628999',
            'institusi' => '@GoogleFormula',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '-08123',
        ]);

        Penempatan::create([
            'pendaftaran_id' => null,
            'kuota_id' => null,
            'slot_khusus_id' => $this->slotKhusus->id,
            'peserta_id' => $pesertaHacker->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::parse('2026-11-01'),
            'tgl_selesai' => Carbon::parse('2027-01-31'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan', ['format' => 'xlsx']));
        $tempZip = tempnam(sys_get_temp_dir(), 'test_hack_');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($tempZip, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        $this->assertStringContainsString("'=cmd|hacker", $sheetXml);
        $this->assertStringContainsString("'+628999", $sheetXml);
        $this->assertStringContainsString("'@GoogleFormula", $sheetXml);

        $zip->close();
        @unlink($tempZip);
    }

    public function test_admin_dapat_mengekspor_rekap_surat_penempatan_format_xlsx(): void
    {
        $peserta = User::factory()->create([
            'nama' => 'Bakti Idaman',
            'email' => 'bakti@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '24.03.2345',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '08123456789',
        ]);

        $pen = Penempatan::create([
            'pendaftaran_id' => null,
            'kuota_id' => null,
            'slot_khusus_id' => $this->slotKhusus->id,
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::parse('2026-11-01'),
            'tgl_selesai' => Carbon::parse('2027-01-31'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('rekap-surat-diterima-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        $tempZip = tempnam(sys_get_temp_dir(), 'test_rekap_xlsx_');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($tempZip, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        $expectedNoSurat = '500.12/'.str_pad((string) $pen->id, 4, '0', STR_PAD_LEFT).'/DISKOMINFO/'.date('Y');

        $this->assertStringContainsString('No Surat Diterima', $sheetXml);
        $this->assertStringContainsString('Bakti Idaman', $sheetXml);
        $this->assertStringContainsString($expectedNoSurat, $sheetXml);
        $this->assertStringContainsString('Teknik Informatika', $sheetXml);

        $stylesXml = $zip->getFromName('xl/styles.xml');
        $this->assertStringContainsString('FF1E3A8A', $stylesXml);

        $zip->close();
        @unlink($tempZip);
    }

    public function test_ekspor_penempatan_mematuhi_filter_pencarian_dan_bidang(): void
    {
        $bidangLain = Bidang::create([
            'nama_bidang' => 'Informasi dan Komunikasi Publik',
            'kode_bidang' => 'IKP',
            'aktif' => true,
        ]);

        $peserta1 = User::factory()->create(['nama' => 'Peserta Satu Egov', 'role' => RoleEnum::Peserta]);
        $peserta2 = User::factory()->create(['nama' => 'Peserta Dua Ikp', 'role' => RoleEnum::Peserta]);

        Penempatan::create([
            'peserta_id' => $peserta1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addMonths(3),
        ]);

        Penempatan::create([
            'peserta_id' => $peserta2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidangLain->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addMonths(3),
        ]);

        // Filter berdasarkan q=Satu
        $resFilterQ = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', [
            'format' => 'csv',
            'q' => 'Satu',
        ]));
        $resFilterQ->assertOk();
        $contentQ = $resFilterQ->streamedContent();
        $this->assertStringContainsString('Peserta Satu Egov', $contentQ);
        $this->assertStringNotContainsString('Peserta Dua Ikp', $contentQ);

        // Filter berdasarkan bidang_id IKP
        $resFilterBidang = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima', [
            'format' => 'csv',
            'bidang_id' => $bidangLain->id,
        ]));
        $resFilterBidang->assertOk();
        $contentBidang = $resFilterBidang->streamedContent();
        $this->assertStringNotContainsString('Peserta Satu Egov', $contentBidang);
        $this->assertStringContainsString('Peserta Dua Ikp', $contentBidang);
    }
}
