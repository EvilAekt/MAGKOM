<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
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

class PembimbingLaporanKinerjaViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta1;

    protected User $peserta2;

    protected Penempatan $penempatan1;

    protected Penempatan $penempatan2;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

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

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
            'deskripsi' => 'Bidang Aptika',
            'is_active' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Budi Santoso, S.Kom',
            'pembimbing@slemankab.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta1 = $this->createUser(
            'Indra Kurniawan',
            'indra@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta1->id,
            'institusi' => 'UPN Veteran Yogyakarta',
            'nim_nisn' => '123220088',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567891',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        $this->peserta2 = $this->createUser(
            'Joko Susilo',
            'joko@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta2->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '22/492110/TK/54110',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567892',
            'tanggal_mulai' => Carbon::today()->subDays(5),
            'tanggal_selesai' => Carbon::today()->addDays(60),
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(50),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(5),
            'tgl_selesai' => Carbon::today()->addDays(60),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);
    }

    public function test_laporan_kinerja_displays_data_penempatan_peserta_panel(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);

        // Memastikan judul panel adalah 'Data Penempatan Peserta' sesuai Gambar 2
        $response->assertSee('Data Penempatan Peserta');

        // Memastikan avatar box inisial dan nama mentee muncul
        $response->assertSee('mentee-avatar-square');
        $response->assertSee('Indra Kurniawan');
        $response->assertSee('Joko Susilo');
        $response->assertSee('UPN Veteran Yogyakarta');
        $response->assertSee('Universitas Gadjah Mada');
    }

    public function test_table_strictly_displays_six_columns_and_omits_nilai_akhir(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);

        // 6 Kolom yang wajib ada
        $response->assertSee('Nama Mentee');
        $response->assertSee('Institusi &amp; Jurusan', false);
        $response->assertSee('Bidang Magang');
        $response->assertSee('Tugas Selesai');
        $response->assertSee('Logbook');
        $response->assertSee('Status');

        // Kolom Nilai Akhir dan metrik nilai harus ditiadakan
        $response->assertDontSee('<th>Nilai Akhir</th>', false);
        $response->assertDontSee('Rata-rata Nilai Akhir');
    }

    public function test_laporan_kinerja_displays_pagination_bar_and_supports_per_page(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index', ['per_page' => 10]));

        $response->assertStatus(200);

        // Memastikan komponen pagination bar Gentelella hadir
        $response->assertSee('adm-pagination-wrapper');
        $response->assertSee('Penempatan Aktif');
        $response->assertSee('Menampilkan:');
    }

    public function test_laporan_kinerja_displays_empty_state_when_no_mentees(): void
    {
        $pembimbingBaru = $this->createUser(
            'Pembimbing Baru, S.T',
            'baru@slemankab.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $response = $this->actingAs($pembimbingBaru)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Data Penempatan');
        $response->assertSee('Belum ada catatan rekapitulasi peserta bimbingan yang terdaftar.');
    }

    public function test_laporan_peserta_displays_four_tile_stats_gentelella(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);

        // 4 Tile Stats selaras Gambar 2
        $response->assertSee('TOTAL MENTEE');
        $response->assertSee('PESERTA AKTIF');
        $response->assertSee('PERLU PERHATIAN');
        $response->assertSee('ALUMNI SELESAI');
        $response->assertSee('Seluruh riwayat penempatan');
        $response->assertSee('Sedang menjalankan magang');
        $response->assertSee('Tugas lewat tenggat / belum tuntas');
        $response->assertSee('Magang tuntas &amp; lulus', false);
    }

    public function test_laporan_peserta_topbar_and_sidebar_labels_and_omits_old_f09_header(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);

        // Topbar Title harus menampilkan 'Laporan Peserta'
        $response->assertSee('<h1 class="topbar-title">Laporan Peserta</h1>', false);

        // Sidebar harus menampilkan 'Laporan Peserta'
        $response->assertSee('Laporan Peserta');
        $response->assertDontSee('Laporan Kinerja ASN');

        // Teks lama F-09 dan deskripsi panjang harus ditiadakan
        $response->assertDontSee('Laporan Kinerja Pembimbing Lapangan ASN (F-09, F-10)');
        $response->assertDontSee('Rekapitulasi portofolio bimbingan, jam mentoring kedinasan');
    }

    public function test_laporan_peserta_export_popover_and_endpoints(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);

        // Hero Banner Card MagKom (Gambar 2 Parity)
        $response->assertSee('classroom-banner');
        $response->assertSee('laporan-hero-banner');
        $response->assertSee('banner-title');
        $response->assertSee('btn-export-rekap-banner');
        $response->assertSee('adm-export-popover-right');
        $response->assertSee('adm-pointer-right');

        // Komponen Popover Ekspor Rekap Cepat (Gambar 5)
        $response->assertSee('Ekspor Rekap');
        $response->assertSee('Ekspor Rekap Cepat');
        $response->assertSee('Microsoft Excel');
        $response->assertSee('.xlsx');
        $response->assertSee('Rekomendasi');
        $response->assertSee('CSV Tabular');
        $response->assertSee('.csv');
        $response->assertSee('Cepat');
        $response->assertSee('Dokumen Resmi (.pdf)');
        $response->assertSee('Legal Sah');

        // Test Endpoint Export CSV
        $csvResponse = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.export', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $csvResponse->headers->get('Content-Type'));

        // Test Endpoint Export XLSX
        $xlsxResponse = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.export', ['format' => 'xlsx']));
        $xlsxResponse->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', (string) $xlsxResponse->headers->get('Content-Type'));

        // Test Endpoint Export PDF (Redirect ke Cetak LPJ)
        $pdfResponse = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.export', ['format' => 'pdf']));
        $pdfResponse->assertRedirect(route('pembimbing.laporan-kinerja.cetak', ['format' => 'pdf']));
    }

    public function test_laporan_peserta_filter_pencarian_q_menyaring_berdasarkan_nama_kampus_dan_nim(): void
    {
        // 1. Verifikasi markup input pencarian dan label filter nama peserta
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertStatus(200);
        $response->assertSee('Pencarian Peserta');
        $response->assertSee('id="inputSearchLaporan"', false);
        $response->assertSee('placeholder="Cari nama, kampus, NIM..."', false);
        $response->assertSee('Filter Nama Peserta');

        // 2. Pencarian berdasarkan Nama Peserta (Indra)
        $searchNama = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index', ['q' => 'Indra']));
        $searchNama->assertStatus(200);
        $searchNama->assertViewHas('rekapMentees', fn ($m) => $m->count() === 1 && $m->first()['penempatan']->id === $this->penempatan1->id);
        $searchNama->assertSee('value="Indra"', false);

        // 3. Pencarian berdasarkan Kampus (Gadjah Mada)
        $searchKampus = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index', ['q' => 'Gadjah Mada']));
        $searchKampus->assertStatus(200);
        $searchKampus->assertViewHas('rekapMentees', fn ($m) => $m->count() === 1 && $m->first()['penempatan']->id === $this->penempatan2->id);

        // 4. Pencarian berdasarkan NIM (123220088)
        $searchNim = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index', ['q' => '123220088']));
        $searchNim->assertStatus(200);
        $searchNim->assertViewHas('rekapMentees', fn ($m) => $m->count() === 1 && $m->first()['penempatan']->id === $this->penempatan1->id);

        // 5. Tombol Reset muncul saat parameter q terisi
        $searchNama->assertSee('btn-filter-reset');
        $searchNama->assertSee('Reset');

        // 5. Tombol Reset muncul saat parameter q terisi
        $searchNama->assertSee('btn-filter-reset');
        $searchNama->assertSee('Reset');
    }
}
