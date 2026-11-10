<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
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

class AdminTablePaginationStandardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $peserta;

    protected User $pembimbing;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Bidang $bidang;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->admin = new User;
        $this->admin->nama = 'Admin Pengelola';
        $this->admin->email = 'admin.pagination@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->jurusan = new Jurusan;
        $this->jurusan->nama_jurusan = 'Teknik Informatika';
        $this->jurusan->kategori = 'it';
        $this->jurusan->aktif = true;
        $this->jurusan->save();

        $this->bidang = new Bidang;
        $this->bidang->nama_bidang = 'Aplikasi Informatika';
        $this->bidang->aktif = true;
        $this->bidang->save();

        $this->bulan = new BulanMagang;
        $this->bulan->bulan_mulai = now()->startOfMonth();
        $this->bulan->tgl_buka = now()->subDays(5);
        $this->bulan->tgl_tutup = now()->addDays(20);
        $this->bulan->aktif = true;
        $this->bulan->save();

        $this->kuota = new Kuota;
        $this->kuota->bulan_magang_id = $this->bulan->id;
        $this->kuota->jurusan_id = $this->jurusan->id;
        $this->kuota->jumlah = 20;
        $this->kuota->terisi = 0;
        $this->kuota->aktif = true;
        $this->kuota->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan';
        $this->pembimbing->email = 'pembimbing.pagination@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        // Buat 12 pendaftaran untuk menguji paginasi
        for ($i = 1; $i <= 12; $i++) {
            $user = new User;
            $user->nama = "Peserta Magang {$i}";
            $user->email = "peserta{$i}.pag@example.com";
            $user->password = 'Password123!';
            $user->role = RoleEnum::Peserta;
            $user->status_aktif = true;
            $user->email_verified_at = now();
            $user->save();

            $profil = new PesertaProfil;
            $profil->user_id = $user->id;
            $profil->institusi = "Universitas {$i}";
            $profil->jurusan_id = $this->jurusan->id;
            $profil->jenjang = JenjangEnum::S1;
            $profil->nim_nisn = "24000{$i}";
            $profil->no_hp = sprintf('0812345678%02d', $i);
            $profil->save();

            $p = new Pendaftaran;
            $p->user_id = $user->id;
            $p->bulan_magang_id = $this->bulan->id;
            $p->kuota_id = $this->kuota->id;
            $p->durasi_bulan = 3;
            $p->status = ($i <= 3) ? StatusPendaftaranEnum::WaitingList : StatusPendaftaranEnum::Diajukan;
            $p->save();

            if ($i <= 2) {
                $pen = new Penempatan;
                $pen->pendaftaran_id = $p->id;
                $pen->peserta_id = $user->id;
                $pen->pembimbing_id = $this->pembimbing->id;
                $pen->bidang_id = $this->bidang->id;
                $pen->kuota_id = $this->kuota->id;
                $pen->tgl_mulai = now();
                $pen->tgl_selesai = now()->addMonths(3);
                $pen->status = StatusPenempatanEnum::Aktif;
                $pen->jalur = JalurEnum::Umum;
                $pen->sumber = 'online';
                $pen->save();
            }
        }
    }

    public function test_admin_pendaftaran_index_defaults_to_5_per_page_and_accepts_custom_per_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'));

        $response->assertOk();
        $response->assertViewHas('pendaftaranList');
        $paginator = $response->viewData('pendaftaranList');
        $this->assertEquals(5, $paginator->perPage());

        $response->assertSee('adm-pagination-wrapper');
        $response->assertSee('Pendaftaran Terverifikasi');
        $response->assertSee('Lembar Halaman');
        $response->assertSee('title="Tampilkan 5 berkas pendaftaran per halaman"', false);
        $response->assertSee('title="Tampilkan 10 berkas pendaftaran per halaman"', false);

        // Uji query parameter custom ?per_page=10
        $response10 = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['per_page' => 10]));
        $response10->assertOk();
        $paginator10 = $response10->viewData('pendaftaranList');
        $this->assertEquals(10, $paginator10->perPage());
    }

    public function test_admin_penempatan_index_defaults_to_5_per_page_and_accepts_custom_per_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));

        $response->assertOk();
        $response->assertViewHas('penempatanList');
        $paginator = $response->viewData('penempatanList');
        $this->assertEquals(5, $paginator->perPage());

        $response->assertSee('adm-pagination-wrapper');
        $response->assertSee('Penempatan Aktif');
        $response->assertSee('Lembar Halaman');
        $response->assertSee('title="Tampilkan 5 peserta magang per halaman"', false);

        // Uji query parameter custom ?per_page=10
        $response10 = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['per_page' => 10]));
        $response10->assertOk();
        $paginator10 = $response10->viewData('penempatanList');
        $this->assertEquals(10, $paginator10->perPage());
    }

    public function test_admin_penempatan_belum_tab_defaults_to_5_per_page_and_accepts_custom_per_page(): void
    {
        $pesertaAntrean = new User;
        $pesertaAntrean->nama = 'Calon Antrean Paginasi';
        $pesertaAntrean->email = 'calon.paginasi@example.com';
        $pesertaAntrean->password = 'Password123!';
        $pesertaAntrean->role = RoleEnum::Peserta;
        $pesertaAntrean->status_aktif = true;
        $pesertaAntrean->email_verified_at = now();
        $pesertaAntrean->save();

        Pendaftaran::create([
            'user_id' => $pesertaAntrean->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-202611-0088',
            'asal_instansi' => 'Universitas Gadjah Mada',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diputuskan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tab' => 'belum']));

        $response->assertOk();
        $response->assertViewHas('antreanBelumDitempatkan');
        $paginator = $response->viewData('antreanBelumDitempatkan');
        $this->assertEquals(5, $paginator->perPage());

        $response->assertSee('adm-pagination-wrapper');
        $response->assertSee('Antrean Penempatan');
        $response->assertSee('Lembar Halaman');
        $response->assertSee('title="Tampilkan 5 peserta magang per halaman"', false);

        // Uji query parameter custom ?tab=belum&per_page=10
        $response10 = $this->actingAs($this->admin)->get(route('admin.penempatan.index', [
            'tab' => 'belum',
            'per_page' => 10,
        ]));
        $response10->assertOk();
        $paginator10 = $response10->viewData('antreanBelumDitempatkan');
        $this->assertEquals(10, $paginator10->perPage());
    }

    public function test_admin_laporan_index_pendaftaran_tab_defaults_to_5_per_page_and_accepts_custom_per_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', ['tab' => 'pendaftaran']));

        $response->assertOk();
        $response->assertViewHas('pendaftaranData');
        $paginator = $response->viewData('pendaftaranData');
        $this->assertEquals(5, $paginator->perPage());

        $response->assertSee('adm-pagination-wrapper');
        $response->assertSee('Rekapitulasi Berkas');
        $response->assertSee('Lembar Halaman');
        $response->assertSee('title="Tampilkan 5 data rekapitulasi per halaman"', false);

        // Uji query parameter custom ?tab=pendaftaran&per_page=10
        $response10 = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'tab' => 'pendaftaran',
            'per_page' => 10,
        ]));
        $response10->assertOk();
        $paginator10 = $response10->viewData('pendaftaranData');
        $this->assertEquals(10, $paginator10->perPage());
    }

    public function test_admin_pagination_falls_back_safely_when_invalid_per_page_provided(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['per_page' => 99999]));
        $response->assertOk();
        $paginator = $response->viewData('pendaftaranList');
        // Harus fallback aman ke default 5
        $this->assertEquals(5, $paginator->perPage());
    }
}
