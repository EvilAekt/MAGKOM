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

class AdminEksporLaporanExcelTest extends TestCase
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
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
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

    public function test_ekspor_csv_pendaftaran_memuat_utf8_bom_dan_sep_directive(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pendaftaran'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Verifikasi UTF-8 BOM dan direktif sep=, untuk kompatibilitas Microsoft Excel
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $content);
        $this->assertStringContainsString('ID,"Nama Peserta",Email,NIM/NISN,Institusi,Jurusan', $content);
    }

    public function test_ekspor_csv_penempatan_jalur_khusus_memuat_nama_jurusan_dan_sep_directive(): void
    {
        // Buat peserta jalur khusus yang tidak punya kuota_id
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

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // 1. Verifikasi header Excel direktif
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $content);

        // 2. Verifikasi baris Jarwo memuat nama jurusan (tidak kosong ,,)
        $this->assertStringContainsString('"Universitas Negeri Yogyakarta","Teknik Informatika"', $content);
        $this->assertStringNotContainsString('"Universitas Negeri Yogyakarta",,', $content);

        // 3. Verifikasi label jalur dan status diformat rapi
        $this->assertStringContainsString('Jalur Khusus', $content);
        $this->assertStringContainsString('Aktif', $content);
    }

    public function test_ekspor_realisasi_kuota_dan_nilai_memuat_sep_directive(): void
    {
        // Ekspor Kuota
        $resKuota = $this->actingAs($this->admin)->get(route('admin.laporan.kuota'));
        $resKuota->assertOk();
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $resKuota->streamedContent());

        // Ekspor Nilai (SuperAdmin)
        $resNilai = $this->actingAs($this->superAdmin)->get(route('admin.laporan.nilai'));
        $resNilai->assertOk();
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $resNilai->streamedContent());
    }

    public function test_ekspor_rekap_surat_diterima_memuat_sep_directive(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.ekspor-surat-diterima'));
        $response->assertOk();
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $response->streamedContent());
    }

    public function test_sanitasi_formula_injection_tetap_berfungsi(): void
    {
        $pesertaHacker = User::factory()->create([
            'nama' => '=cmd|hacker',
            'email' => 'hacker@example.com',
            'role' => RoleEnum::Peserta,
        ]);

        PesertaProfil::create([
            'user_id' => $pesertaHacker->id,
            'nim_nisn' => '+628999',
            'institusi' => '@SpreadsheetFormula',
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

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan'));
        $content = $response->streamedContent();

        // Verifikasi karakter formula dinetralkan dengan awalan petik tunggal
        $this->assertStringContainsString("'=cmd|hacker", $content);
        $this->assertStringContainsString("'+628999", $content);
        $this->assertStringContainsString("'@SpreadsheetFormula", $content);
    }
}
