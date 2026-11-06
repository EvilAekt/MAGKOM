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
        // Header fokus terpadu default disembunyikan
        $response->assertSee('class="mentee-unified-header d-none"', false);

        // Komponen Filter Nama Peserta ter-render dan terlihat di direktori awal
        $response->assertSee('Filter Nama Peserta');
        $response->assertSee('id="selectPenempatan"', false);
        $response->assertSee('Semua Peserta Bimbingan');
        $response->assertSee('class="btn-filter-apply"', false);
        $response->assertDontSee('class="btn-filter-reset"', false);
        $response->assertDontSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);

        // Mentee pembimbing lain tidak boleh ada di dropdown (Anti-IDOR)
        $response->assertDontSee('Peserta Pembimbing Lain');
    }

    public function test_akses_dengan_penempatan_id_menampilkan_mode_fokus_dan_tombol_kembali(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
            'penempatan_id' => $this->penempatan1->id,
        ]));

        $response->assertOk();
        // Container berada dalam mode fokus
        $response->assertSee('mentee-stream-container is-focused');
        // Identitas mentee muncul di header terpadu sisi kiri
        $response->assertSee('focusedMenteeName');
        $response->assertSee('Naufal Pratama');
        $response->assertSee('SMK Negeri 2 Depok Sleman');
        $response->assertSee('focusedMenteeAvatar');
        // Teks kaku 'Tugas & Progres:' telah dihilangkan
        $response->assertDontSee('Tugas &amp; Progres: Naufal Pratama');
        $response->assertDontSee('Tugas & Progres: Naufal Pratama');

        // Komponen Filter Nama Peserta disembunyikan saat dalam mode fokus
        $response->assertSee('id="tugasFilterCard" class="tugas-filter-card d-none"', false);
        $response->assertSee('value="'.$this->penempatan1->id.'" selected', false);
        $response->assertSee('class="btn-filter-reset"', false);

        // Tombol kembali sisi kiri muncul
        $response->assertSee('btnBackToMentees');
        $response->assertSee('Semua Mentee');

        // Tugas milik mentee terpilih terlihat
        $response->assertSee('Pembuatan Wireframe UI Portal Magang');

        // Chip statistik duplikat dihilangkan dari kartu
        $response->assertDontSee('mentee-stats-badges');

        // Badge kategori di baris tugas dihapus agar tampilan bersih
        $response->assertDontSee('task-row-category');

        // Kolom kompleksitas dihapus dari tabel penugasan
        $response->assertDontSee('<th>Kompleksitas</th>', false);
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
        // Kartu mentee 1 berstatus aktif fokus
        $responseFocus->assertSee('data-mentee-id="'.$this->penempatan1->id.'"', false);
        // Drawer mentee 2 tertutup dengan hidden
        $responseFocus->assertSee('id="tasks-mentee-'.$this->penempatan2->id.'" class="mentee-tasks-drawer" hidden', false);
        // Tabel tersinkronisasi dengan mode fokus
        $responseFocus->assertSee('classroom-table-container is-focused', false);
        $responseFocus->assertSee('task-table table-focused-mentee', false);
    }
}
