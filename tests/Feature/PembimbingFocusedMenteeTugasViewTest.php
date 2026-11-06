<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingFocusedMenteeTugasViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $pembimbingLain;

    protected User $peserta1;

    protected User $peserta2;

    protected User $pesertaLain;

    protected Penempatan $penempatan1;

    protected Penempatan $penempatan2;

    protected Penempatan $penempatanLain;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected Tugas $tugas1;

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

        $this->pembimbingLain = $this->createUser(
            'Pembimbing Lain, M.Kom',
            'pembimbing.lain@slemankab.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta1 = $this->createUser(
            'Naufal Pratama',
            'naufal@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta1->id,
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nim_nisn' => '12345678',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'no_hp' => '081234567891',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        $this->peserta2 = $this->createUser(
            'Dimas Pratama',
            'dimas@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta2->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '87654321',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567892',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        $this->pesertaLain = $this->createUser(
            'Peserta Pembimbing Lain',
            'peserta.lain@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->pesertaLain->id,
            'institusi' => 'Institusi Luar',
            'nim_nisn' => '99999999',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567899',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        $bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatanLain = Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'pembimbing_id' => $this->pembimbingLain->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Tugas untuk Peserta 1
        $this->tugas1 = Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul' => 'Pembuatan Wireframe UI Portal Magang',
            'deskripsi' => 'Desain arsitektur wireframe halaman antarmuka.',
            'kategori' => 'Desain & UI',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'status' => StatusTugasEnum::Belum,
            'deadline' => Carbon::today()->addDays(5),
            'waktu_mulai' => Carbon::now(),
        ]);

        // Tugas untuk Peserta 2
        Tugas::create([
            'penempatan_id' => $this->penempatan2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul' => 'Implementasi API Autentikasi',
            'deskripsi' => 'Setup controller dan unit test auth.',
            'kategori' => 'Backend API',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'status' => StatusTugasEnum::Selesai,
            'deadline' => Carbon::today()->addDays(7),
            'waktu_mulai' => Carbon::now(),
        ]);
    }

    public function test_halaman_awal_menampilkan_seluruh_kartu_mentee_dan_header_default(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index'));

        $response->assertOk();
        $response->assertSee('Daftar Penugasan & Progres Mentee');
        $response->assertSee('Naufal Pratama');
        $response->assertSee('Dimas Pratama');
        // Header fokus terpadu dobel tidak ditampilkan pada panel
        $response->assertDontSee('class="mentee-unified-header"', false);

        // Komponen Filter Nama Peserta ter-render dan terlihat di direktori awal
        $response->assertSee('Filter Nama Peserta');
        $response->assertSee('id="selectPenempatan"', false);
        $response->assertSee('Semua Peserta Bimbingan');
        $response->assertSee('class="btn-filter-apply"', false);
        $response->assertDontSee('class="btn-filter-reset"', false);
        $response->assertDontSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);

        // Mentee pembimbing lain tidak boleh ada di dropdown (Anti-IDOR)
        $response->assertDontSee('Peserta Pembimbing Lain');

        // Header kartu mentee berfungsi sebagai tautan masuk ke detail peserta
        $response->assertSee('class="mentee-card-header mentee-card-link"', false);
        $response->assertSee('masuk='.$this->penempatan1->id, false);
        $response->assertSee('<div id="tasks-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer" hidden>', false);

        // Tombol Buat Tugas Baru memiliki satu ikon (tidak ada plus ganda + +)
        $response->assertDontSee('+ + Buat Tugas Baru', false);
        $response->assertSee('<span>Buat Tugas Baru</span>', false);

        // Input pencarian teks q tersedia di form filter
        $response->assertSee('name="q"', false);
        $response->assertSee('placeholder="Cari nama, kampus, NIM..."', false);
    }

    public function test_pencarian_q_pada_tugas_menyaring_peserta_berdasarkan_nama_atau_kampus(): void
    {
        // 1. Cari berdasarkan nama "Naufal"
        $responseNama = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'q' => 'Naufal',
        ]));
        $responseNama->assertOk();
        $responseNama->assertSee('Naufal Pratama');
        $responseNama->assertSee('data-mentee-id="'.$this->penempatan1->id.'"', false);
        $responseNama->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);
        $responseNama->assertSee('class="btn-filter-reset"', false);

        // 2. Cari berdasarkan kampus "Gadjah Mada"
        $responseKampus = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'q' => 'Gadjah Mada',
        ]));
        $responseKampus->assertOk();
        $responseKampus->assertSee('Dimas Pratama');
        $responseKampus->assertSee('data-mentee-id="'.$this->penempatan2->id.'"', false);
        $responseKampus->assertDontSee('data-mentee-id="'.$this->penempatan1->id.'"', false);
        $responseKampus->assertSee('class="btn-filter-reset"', false);
    }

    public function test_filter_penempatan_id_hanya_menampilkan_peserta_terfilter_seperti_search(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'penempatan_id' => $this->penempatan1->id,
        ]));

        $response->assertOk();
        // Identitas mentee terpilih terlihat pada kartu
        $response->assertSee('Naufal Pratama');
        $response->assertSee('SMK Negeri 2 Depok Sleman');

        // Seperti search filter: kartu tetap berupa kartu daftar, tugas belum langsung terbuka
        $response->assertSee('<div id="tasks-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer" hidden>', false);
        $response->assertSee('class="mentee-card-header mentee-card-link"', false);
        $response->assertDontSee('class="mentee-chevron-back-btn"', false);

        // Komponen Filter Nama Peserta tetap terlihat saat dalam mode filter aktif
        $response->assertSee('id="tugasFilterCard" class="tugas-filter-card"', false);
        $response->assertDontSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);
        $response->assertSee('Daftar Penugasan &amp; Progres Mentee', false);
        $response->assertSee('value="'.$this->penempatan1->id.'" selected', false);
        $response->assertSee('class="btn-filter-reset"', false);

        // Mentee lain tidak muncul dalam daftar kartu yang terfilter
        $response->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);
    }

    public function test_klik_masuk_penempatan_menampilkan_mode_detail_dan_tugas_langsung_terbuka(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'masuk' => $this->penempatan1->id,
        ]));

        $response->assertOk();
        // Identitas mentee terpilih terlihat pada kartu
        $response->assertSee('Naufal Pratama');
        $response->assertSee('SMK Negeri 2 Depok Sleman');

        // Drawer tugas langsung terbuka penuh tanpa accordion/hidden
        $response->assertSee('<div id="tasks-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer">', false);
        $response->assertDontSee('<div id="tasks-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer" hidden>', false);
        // Tombol panah kembali (<) terlihat di kanan header kartu
        $response->assertSee('class="mentee-chevron-back-btn"', false);
        $response->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);

        // Filter Card harus disembunyikan dengan d-none saat masuk mode fokus mentee
        $response->assertSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);

        // Cek peserta tanpa tugas: pastikan paginasi mentee TIDAK bocor ke tampilan mentee kosong
        $pesertaTanpaTugas = $this->createUser('Zulfikar Ali', 'zulfikar@student.ac.id', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $pesertaTanpaTugas->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '22/499111/TK/55000',
            'no_hp' => '081234567895',
        ]);
        $penempatanKosong = Penempatan::create([
            'peserta_id' => $pesertaTanpaTugas->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $this->penempatan1->bulan_magang_id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $responseKosong = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'masuk' => $penempatanKosong->id,
        ]));
        $responseKosong->assertOk();
        $responseKosong->assertSee('Belum ada tugas untuk peserta ini.');
        $responseKosong->assertDontSee('id="menteePaginationWrapper"', false);
        $responseKosong->assertDontSee('id="tugasPaginationWrapper"', false);
    }

    public function test_halaman_edit_tugas_menggunakan_tampilan_workspace_modern(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.edit', $this->tugas1));

        $response->assertOk();
        $response->assertSee('<h1 class="topbar-title">Edit Tugas Mentee</h1>', false);
        $response->assertDontSee('<nav class="breadcrumb-trail"', false);
        $response->assertSee('tugas-workspace');
        $response->assertSee('Edit Tugas Mentee');
        $response->assertSee('Simpan Perubahan');
        $response->assertSee($this->tugas1->judul);
        $response->assertSee('tugas-locked-mentee-card');
        $response->assertSee('Naufal Pratama');

        // Form submit update berhasil
        $updateResponse = $this->actingAs($this->pembimbing)->put(route('pembimbing.tugas.update', $this->tugas1), [
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Judul Tugas Diperbarui via Classroom Edit',
            'deskripsi' => '<p>Instruksi pengerjaan diperbarui dengan format teks kaya.</p>',
            'kategori' => 'Pengembangan Web',
            'deadline' => Carbon::today()->addDays(10)->format('Y-m-d'),
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('tugas', [
            'id' => $this->tugas1->id,
            'judul' => 'Judul Tugas Diperbarui via Classroom Edit',
        ]);
    }

    public function test_akses_penempatan_id_milik_pembimbing_lain_tidak_bisa_diakses_anti_idor(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'penempatan_id' => $this->penempatanLain->id,
        ]));

        $response->assertOk();
        // Tidak membocorkan nama mentee pembimbing lain
        $response->assertDontSee('Peserta Pembimbing Lain');
        // Tetap pada header default
        $response->assertSee('Daftar Penugasan & Progres Mentee');
    }

    public function test_tampilan_tabel_tersinkronisasi_dengan_fokus_mentee(): void
    {
        // 1. Pada halaman awal (semua mentee), tabel ringkasan mentee dan tabel penugasan keduanya tersedia di DOM
        $responseAll = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index'));
        $responseAll->assertOk();
        $responseAll->assertSee('Judul Penugasan');
        $responseAll->assertSee('task-row-icon');
        $responseAll->assertDontSee('<th>Peserta Magang</th>', false);
        $responseAll->assertSee('id="classroomTableContainer"', false);
        $responseAll->assertSee('id="tableMenteesSummaryView"', false);
        $responseAll->assertSee('id="menteesSummaryTable"', false);
        $responseAll->assertSee('id="mainTugasTable"', false);
        $responseAll->assertSee('class="col-peserta"', false);
        $responseAll->assertSee('Total Tugas');
        $responseAll->assertSee('Status Progres');
        $responseAll->assertSee('Lihat Tugas');

        // 2. Pada mode fokus mentee (penempatan1), mentee 1 fokus dan drawernya terbuka
        $responseFocus = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'penempatan_id' => $this->penempatan1->id,
        ]));
        $responseFocus->assertOk();
        // Tugas mentee 1 tampil aktif di dalam drawer kartu/tabel
        $responseFocus->assertSee($this->tugas1->judul);
        // Kartu mentee 1 muncul
        $responseFocus->assertSee('data-mentee-id="'.$this->penempatan1->id.'"', false);
        // Kartu mentee 2 tidak ada dalam daftar stream yang terfilter
        $responseFocus->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);
    }

    public function test_paginasi_tugas_menampilkan_10_entri_per_halaman_dan_navigasi_halaman(): void
    {
        // Buat total 15 tugas untuk penempatan 1 (tugas1 sudah ada 1, tambah 14)
        for ($i = 2; $i <= 15; $i++) {
            Tugas::create([
                'penempatan_id' => $this->penempatan1->id,
                'pembimbing_id' => $this->pembimbing->id,
                'judul' => "Tugas Tambahan ke-{$i} untuk Mentee",
                'deskripsi' => "Deskripsi penugasan nomor {$i}.",
                'kategori' => 'Pengembangan Web',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'status' => StatusTugasEnum::Belum,
                'deadline' => Carbon::now()->addDays($i),
            ]);
        }

        // 1. Halaman 1 harus menampilkan ringkasan 1 sampai 10 dari 15 entri
        $responseHalaman1 = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'masuk' => $this->penempatan1->id,
        ]));

        $responseHalaman1->assertOk();
        $responseHalaman1->assertSee('id="tugasPaginationWrapper"', false);
        $responseHalaman1->assertSee('Menampilkan');
        $responseHalaman1->assertSee('<strong>1</strong>', false);
        $responseHalaman1->assertSee('<strong>10</strong>', false);
        $responseHalaman1->assertSee('<strong>15</strong>', false);
        $responseHalaman1->assertSee('Berikutnya &raquo;', false);

        // Filter Card harus tetap tersembunyi
        $responseHalaman1->assertSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);

        // 2. Akses Halaman 2: menampilkan 11 sampai 15 dari 15 entri
        $responseHalaman2 = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'masuk' => $this->penempatan1->id,
            'page' => 2,
        ]));

        $responseHalaman2->assertOk();
        $responseHalaman2->assertSee('id="tugasPaginationWrapper"', false);
        $responseHalaman2->assertSee('<strong>11</strong>', false);
        $responseHalaman2->assertSee('<strong>15</strong>', false);
        $responseHalaman2->assertSee('&laquo; Sebelumnya', false);
        $responseHalaman2->assertSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);
    }

    public function test_baris_tugas_tidak_lagi_menampilkan_tombol_detail_dan_memiliki_atribut_double_click(): void
    {
        // Akses halaman tugas dengan parameter masuk mentee penempatan1
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'masuk' => $this->penempatan1->id,
        ]));

        $response->assertOk();

        // 1. Tombol Detail tidak ada lagi di baris kartu tugas (.classroom-task-row)
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " classroom-task-row ")]//*[contains(concat(" ", normalize-space(@class), " "), " btn-tugas-action ")]');
        $this->assertEquals(0, $nodes->length);

        // 2. Baris kartu tugas memiliki atribut data-task-url untuk interaksi double-click
        $response->assertSee('data-task-url="'.route('pembimbing.tugas.show', $this->tugas1->id).'"', false);
        $response->assertSee('title="Klik dua kali (double click) untuk membuka detail tugas"', false);
        $response->assertSee('role="button"', false);
        $response->assertSee('tabindex="0"', false);
    }

    public function test_halaman_review_tugas_menampilkan_floating_card_catatan_umpan_balik_tanpa_placeholder_panjang(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $this->tugas1->id));

        $response->assertOk();

        // 1. Memiliki kontainer floating card box
        $response->assertSee('id="feedbackFieldBox"', false);
        $response->assertSee('class="review-feedback-field', false);

        // 2. Memiliki floating label di dalam card
        $response->assertSee('class="review-feedback-label"', false);
        $response->assertSee('Catatan Umpan Balik');
        $response->assertSee('id="feedbackReqBadge"', false);
        $response->assertSee('*(Wajib jika dikembalikan)');

        // 3. Textarea tidak memiliki placeholder panjang lama
        $response->assertDontSee('Tuliskan catatan apresiasi atau instruksi perbaikan untuk peserta...');
        $response->assertSee('class="review-feedback-textarea-clean"', false);
        $response->assertSee('id="feedbackInput"', false);
    }
}
