<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingFocusedMenteeLogbookViewTest extends TestCase
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

    protected Logbook $logbook1;

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
            'nama_bidang' => 'Aplikasi Informatika (APTIKA)',
            'deskripsi' => 'Pengembangan Software dan Sistem Informasi',
            'kuota' => 10,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika & Ilmu Komputer',
            'kategori' => 'universitas',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser('Dr. Ir. Budi Santoso', 'budi.santoso@sleman.go.id', RoleEnum::Pembimbing, $this->bidang->id);
        $this->pembimbingLain = $this->createUser('Agus Prasetyo, M.Kom', 'agus.prasetyo@sleman.go.id', RoleEnum::Pembimbing, $this->bidang->id);

        $this->peserta1 = $this->createUser('Naufal Pratama', 'naufal@student.ac.id', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $this->peserta1->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '20/456789/TK/50001',
            'no_hp' => '081234567891',
        ]);

        $this->peserta2 = $this->createUser('Siti Rahmawati', 'siti@student.ac.id', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $this->peserta2->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Negeri Yogyakarta',
            'nim_nisn' => '20/456790/TK/50002',
            'no_hp' => '081234567892',
        ]);

        $this->pesertaLain = $this->createUser('Peserta Pembimbing Lain', 'lain@student.ac.id', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $this->pesertaLain->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Institut Teknologi Bandung',
            'nim_nisn' => '20/456799/TK/50099',
            'no_hp' => '081234567899',
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::now()->subDays(10),
            'tgl_selesai' => Carbon::now()->addDays(50),
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::now()->subDays(5),
            'tgl_selesai' => Carbon::now()->addDays(55),
        ]);

        $this->penempatanLain = Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'pembimbing_id' => $this->pembimbingLain->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::now()->subDays(5),
            'tgl_selesai' => Carbon::now()->addDays(55),
        ]);

        $this->logbook1 = Logbook::create([
            'penempatan_id' => $this->penempatan1->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Implementasi modul sinkronisasi data logbook dan integrasi API.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);
    }

    public function test_halaman_awal_logbook_menampilkan_seluruh_kartu_mentee_tanpa_switcher_tabel(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index'));

        $response->assertOk();
        // 1. Header default muncul dan switcher tabel tidak ditampilkan
        $response->assertSee('Daftar Logbook &amp; Catatan Pembimbing', false);
        $response->assertDontSee('id="btnViewTable"', false);
        $response->assertDontSee('id="btnViewCard"', false);

        // 2. Kartu filter nama peserta terlihat pada direktori awal
        $response->assertSee('id="logbookFilterCard"', false);
        $response->assertDontSee('id="logbookFilterCard" class="logbook-filter-card d-none"', false);

        // 3. Header identitas mentee dobel tidak ditampilkan pada panel
        $response->assertDontSee('id="menteeUnifiedHeader"', false);

        // 4. Semua kartu mentee bimbingan muncul di area konten
        $response->assertSee('Naufal Pratama');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('Siti Rahmawati');
        $response->assertSee('Universitas Negeri Yogyakarta');

        // 5. Mentee pembimbing lain tidak boleh muncul (Anti-IDOR)
        $response->assertDontSee('Peserta Pembimbing Lain');

        // 6. Header kartu mentee berfungsi sebagai tautan masuk ke rincian logbook
        $response->assertSee('class="mentee-card-header mentee-card-link"', false);
        $response->assertSee('masuk='.$this->penempatan1->id, false);
        $response->assertSee('<div id="logs-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer" hidden>', false);

        // 7. Input pencarian dan badge chips statistik terlihat pada kartu mentee
        $response->assertSee('name="q"', false);
        $response->assertSee('placeholder="Cari nama, kampus, NIM..."', false);
        $response->assertSee('class="mentee-stats-badges"', false);
        $response->assertSee('1 Logbook');
    }

    public function test_pencarian_q_pada_logbook_menyaring_peserta_berdasarkan_nama_atau_kampus(): void
    {
        // 1. Cari berdasarkan nama "Naufal"
        $responseNama = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'q' => 'Naufal',
        ]));
        $responseNama->assertOk();
        $responseNama->assertSee('Naufal Pratama');
        $responseNama->assertSee('data-mentee-id="'.$this->penempatan1->id.'"', false);
        $responseNama->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);
        $responseNama->assertSee('class="btn-filter-reset"', false);

        // 2. Cari berdasarkan kampus "Yogyakarta"
        $responseKampus = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'q' => 'Yogyakarta',
        ]));
        $responseKampus->assertOk();
        $responseKampus->assertSee('Siti Rahmawati');
        $responseKampus->assertSee('data-mentee-id="'.$this->penempatan2->id.'"', false);
        $responseKampus->assertDontSee('data-mentee-id="'.$this->penempatan1->id.'"', false);
        $responseKampus->assertSee('class="btn-filter-reset"', false);
    }

    public function test_filter_penempatan_id_hanya_menampilkan_peserta_terfilter_seperti_search(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'penempatan_id' => $this->penempatan1->id,
        ]));

        $response->assertOk();
        // 1. Kartu filter tetap terlihat saat dalam mode filter aktif
        $response->assertSee('id="logbookFilterCard" class="logbook-filter-card"', false);
        $response->assertDontSee('id="logbookFilterCard" class="logbook-filter-card d-none"', false);

        // 2. Judul default panel tetap muncul
        $response->assertSee('Daftar Logbook &amp; Catatan Pembimbing', false);
        $response->assertDontSee('id="defaultTitleText" class="default-panel-title d-none"', false);

        // 3. Kartu mentee terpilih terlihat
        $response->assertSee('Naufal Pratama');
        $response->assertSee('Universitas Gadjah Mada');

        // 4. Mentee lain tidak muncul dalam daftar kartu yang terfilter
        $response->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);

        // 5. Tombol reset muncul di bar filter
        $response->assertSee('class="btn-filter-reset"', false);

        // 6. Seperti search filter: kartu tetap berupa kartu daftar, entri logbook belum langsung terbuka
        $response->assertSee('<div id="logs-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer" hidden>', false);
        $response->assertSee('class="mentee-card-header mentee-card-link"', false);
        $response->assertDontSee('class="mentee-chevron-back-btn"', false);
    }

    public function test_klik_masuk_penempatan_menampilkan_mode_detail_dan_logbook_langsung_terbuka(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'masuk' => $this->penempatan1->id,
        ]));

        $response->assertOk();
        // 1. Kartu mentee terpilih terlihat
        $response->assertSee('Naufal Pratama');
        $response->assertSee('Universitas Gadjah Mada');

        // 2. Drawer logbook langsung terbuka penuh tanpa accordion/hidden
        $response->assertSee('<div id="logs-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer">', false);
        $response->assertDontSee('<div id="logs-mentee-'.$this->penempatan1->id.'" class="mentee-tasks-drawer" hidden>', false);
        // 3. Tombol panah kembali (<) terlihat di kanan header kartu
        $response->assertSee('class="mentee-chevron-back-btn"', false);
        $response->assertDontSee('data-mentee-id="'.$this->penempatan2->id.'"', false);

        // 4. Modal pop-up lama tidak ada di DOM, digantikan form inline editor
        $response->assertDontSee('id="modal-catatan-logbook"', false);
        $response->assertSee('inline-note-editor');
        $response->assertSee('logbook-note-field-filled');
        $response->assertSee('logbook-note-floating-label');
        $response->assertSee('logbook-note-clean-textarea');
        $response->assertSee('btn-toggle-inline-note');
        $response->assertSee('data-action="toggle-inline-note"', false);
        $response->assertSee('data-action="cancel-inline-note"', false);
        $response->assertSee('Simpan Catatan');

        // 5. Filter Card disembunyikan dengan d-none saat sudah masuk ke detail mentee
        $response->assertSee('id="logbookFilterCard" class="logbook-filter-card d-none"', false);
    }

    public function test_akses_penempatan_id_mentee_pembimbing_lain_tidak_bisa_diakses_anti_idor(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'penempatan_id' => $this->penempatanLain->id,
        ]));

        $response->assertOk();
        // Tidak membocorkan nama mentee pembimbing lain
        $response->assertDontSee('Peserta Pembimbing Lain');
        // Tetap pada header default
        $response->assertSee('Daftar Logbook &amp; Catatan Pembimbing', false);
    }

    public function test_halaman_tugas_tidak_menampilkan_switcher_tabel_secara_visual(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index'));

        $response->assertOk();
        // Switcher tabel disembunyikan dari visual pengguna
        $response->assertSee('view-switcher-only" style="display: none;"', false);
    }

    public function test_pembimbing_bisa_mengosongkan_atau_menghapus_catatan_logbook_tersimpan_sebagai_null(): void
    {
        // 1. Set catatan awal
        $this->logbook1->update([
            'catatan_pembimbing' => 'Catatan bimbingan awal yang ingin dihapus pembimbing.',
        ]);

        // 2. Akses halaman: textarea tidak memiliki atribut required
        $viewResponse = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'masuk' => $this->penempatan1->id,
        ]));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Catatan bimbingan awal yang ingin dihapus pembimbing.');
        $viewResponse->assertDontSee('required'."\n".'                                                                  class="logbook-note-clean-textarea"', false);

        // 3. Post review dengan string kosong: harus berhasil dan tersimpan sebagai null
        $postResponse = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.logbook.review', $this->logbook1->id), [
                'catatan_pembimbing' => '',
            ]);

        $postResponse->assertSessionHasNoErrors();
        $postResponse->assertRedirect();
        $this->assertNull($this->logbook1->fresh()->catatan_pembimbing);

        // 4. Pastikan jika status 'perlu_revisi', catatan kosong tetap ditolak (AC-09-4)
        $revisiResponse = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.logbook.review', $this->logbook1->id), [
                'status' => 'perlu_revisi',
                'catatan_pembimbing' => '',
            ]);
        $revisiResponse->assertSessionHasErrors('catatan_pembimbing');
    }

    public function test_paginasi_logbook_menampilkan_10_entri_per_halaman_dan_navigasi_halaman(): void
    {
        // Buat total 15 entri logbook untuk penempatan 1 (logbook1 sudah ada 1, tambah 14)
        for ($i = 2; $i <= 15; $i++) {
            Logbook::create([
                'penempatan_id' => $this->penempatan1->id,
                'minggu_ke' => (int) ceil($i / 5),
                'tanggal' => Carbon::today()->subDays($i),
                'kegiatan' => "Kegiatan bimbingan logbook ke-{$i} oleh mentee.",
                'status' => StatusLogbookEnum::Terkirim,
            ]);
        }

        // 1. Halaman 1 harus menampilkan ringkasan 1 sampai 10 dari 15 entri
        $responseHalaman1 = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'masuk' => $this->penempatan1->id,
        ]));

        $responseHalaman1->assertOk();
        $responseHalaman1->assertSee('id="logbookPaginationWrapper"', false);
        $responseHalaman1->assertSee('Menampilkan');
        $responseHalaman1->assertSee('<strong>1</strong>', false);
        $responseHalaman1->assertSee('<strong>10</strong>', false);
        $responseHalaman1->assertSee('<strong>15</strong>', false);
        $responseHalaman1->assertSee('Berikutnya &raquo;', false);

        // Filter Card harus tetap tersembunyi
        $responseHalaman1->assertSee('id="logbookFilterCard" class="logbook-filter-card d-none"', false);

        // 2. Akses Halaman 2: menampilkan 11 sampai 15 dari 15 entri
        $responseHalaman2 = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'masuk' => $this->penempatan1->id,
            'page' => 2,
        ]));

        $responseHalaman2->assertOk();
        $responseHalaman2->assertSee('id="logbookPaginationWrapper"', false);
        $responseHalaman2->assertSee('<strong>11</strong>', false);
        $responseHalaman2->assertSee('<strong>15</strong>', false);
        $responseHalaman2->assertSee('&laquo; Sebelumnya', false);
        $responseHalaman2->assertSee('id="logbookFilterCard" class="logbook-filter-card d-none"', false);
    }
}
