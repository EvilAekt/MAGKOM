<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

class PembimbingLaporanKinerjaExportArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected Penempatan $penempatan;

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->role = $role;
        $user->bidang_id = $bidangId;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create(['nama_bidang' => 'Aptika Sleman & E-Gov']);
        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi (S-1)',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Budi Pembimbing, M.Kom.',
            'pembimbing.ekspor@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta = $this->createUser(
            'Ahmad Mentee — Daerah Istimewa Yogyakarta',
            'mentee.ekspor@magkom.go.id',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'institusi' => 'Universitas Amikom Yogyakarta',
            'nim_nisn' => '21.11.0001',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081299998888',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(50),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);
    }

    public function test_export_csv_returns_valid_bom_and_eleven_columns(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.laporan-kinerja.export', ['format' => 'csv']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.csv', (string) $response->headers->get('content-disposition'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Cek UTF-8 BOM
        $bom = chr(0xEF).chr(0xBB).chr(0xBF);
        $this->assertStringStartsWith($bom, $content);

        // Cek 11 kolom header CSV
        $this->assertStringContainsString('No,"Nama Mentee",NIM/NISN,Institusi,Jurusan,"Bidang Magang","Tugas Selesai","Total Tugas","Logbook Direview","Total Logbook",Status', $content);
        $this->assertStringContainsString('Ahmad Mentee', $content);
        $this->assertStringContainsString('21.11.0001', $content);
        $this->assertStringContainsString('Universitas Amikom', $content);
    }

    public function test_export_xlsx_returns_genuine_openxml_spreadsheet_with_nine_columns_and_gentelella_blue_styling(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.laporan-kinerja.export', ['format' => 'xlsx']));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        $tempZip = tempnam(sys_get_temp_dir(), 'test_laporan_xlsx_');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($tempZip, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('xl/styles.xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));

        // Cek Gentelella Royal Blue header styling (#0641CC -> FF0641CC)
        $stylesXml = $zip->getFromName('xl/styles.xml');
        $this->assertStringContainsString('FF0641CC', $stylesXml);

        // Cek data sheet (9 Kolom resmi dengan nilai X / Y untuk Tugas Selesai dan Logbook)
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('<t>Nama Mentee</t>', $sheetXml);
        $this->assertStringContainsString('<t>Tugas Selesai</t>', $sheetXml);
        $this->assertStringContainsString('<t>Logbook</t>', $sheetXml);
        $this->assertStringContainsString('<t>Status</t>', $sheetXml);

        // Kolom khusus format X / Y
        $this->assertStringContainsString('<t>0 / 0</t>', $sheetXml);

        // Karakter Unicode em-dash dan nama institusi
        $this->assertStringContainsString('Ahmad Mentee — Daerah Istimewa Yogyakarta', $sheetXml);
        $this->assertStringContainsString('Universitas Amikom Yogyakarta', $sheetXml);
        $this->assertStringContainsString('Aktif', $sheetXml);

        $zip->close();
        @unlink($tempZip);
    }

    public function test_export_with_format_pdf_redirects_to_cetak(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.laporan-kinerja.export', [
            'format' => 'pdf',
            'q' => 'Ahmad',
        ]));

        $response->assertRedirect(route('pembimbing.laporan-kinerja.cetak', [
            'format' => 'pdf',
            'q' => 'Ahmad',
        ]));
    }

    public function test_export_xlsx_sanitizes_formula_injection(): void
    {
        $hackerUser = $this->createUser('=cmd|hacker', 'hacker@magkom.go.id', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $hackerUser->id,
            'institusi' => '+Universitas Jahat',
            'nim_nisn' => '-12345',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081299998889',
        ]);

        Penempatan::create([
            'peserta_id' => $hackerUser->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(5),
            'tgl_selesai' => Carbon::today()->addDays(50),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);

        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.laporan-kinerja.export', ['format' => 'xlsx']));
        $response->assertOk();

        $tempZip = tempnam(sys_get_temp_dir(), 'test_laporan_formula_');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($tempZip, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        // Harus diawali tanda petik tunggal untuk netralisasi formula
        $this->assertStringContainsString("<t>'=cmd|hacker</t>", $sheetXml);
        $this->assertStringContainsString("<t>'+Universitas Jahat</t>", $sheetXml);
        $this->assertStringContainsString("<t>'-12345</t>", $sheetXml);

        $zip->close();
        @unlink($tempZip);
    }
}
