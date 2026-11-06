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

        // 3. Header identitas mentee tersembunyi secara default
        $response->assertSee('id="menteeUnifiedHeader" class="mentee-unified-header d-none"', false);

        // 4. Semua kartu mentee bimbingan muncul di area konten
        $response->assertSee('Naufal Pratama');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('Siti Rahmawati');
        $response->assertSee('Universitas Negeri Yogyakarta');

        // 5. Mentee pembimbing lain tidak boleh muncul (Anti-IDOR)
        $response->assertDontSee('Peserta Pembimbing Lain');
    }

    public function test_akses_logbook_dengan_penempatan_id_menampilkan_mode_fokus_dan_tombol_semua_mentee(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index', [
            'penempatan_id' => $this->penempatan1->id,
        ]));

        $response->assertOk();
        // 1. Kartu filter disembunyikan saat dalam mode fokus
        $response->assertSee('id="logbookFilterCard" class="logbook-filter-card d-none"', false);

        // 2. Judul default panel disembunyikan
        $response->assertSee('id="defaultTitleText" class="default-panel-title d-none"', false);

        // 3. Header identitas mentee muncul selaras dengan modul Tugas
        $response->assertSee('id="menteeUnifiedHeader" class="mentee-unified-header "', false);
        $response->assertSee('focusedMenteeAvatar');
        $response->assertSee('focusedMenteeName');
        $response->assertSee('Naufal Pratama');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee('Semua Mentee');

        // 4. Entri logbook mentee 1 muncul
        $response->assertSee('Implementasi modul sinkronisasi data logbook dan integrasi API.');

        // 5. Status card aktif fokus
        $response->assertSee('is-active-focus is-expanded', false);
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
}
