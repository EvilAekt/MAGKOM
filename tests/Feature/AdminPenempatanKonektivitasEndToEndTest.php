<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPenempatanKonektivitasEndToEndTest extends TestCase
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
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);

        $this->admin = $this->createUser('Admin Verifikator', 'admin.penempatan@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing = $this->createUser('Budi Santoso Pembimbing', 'budi.pembimbing@slemankab.go.id', RoleEnum::Pembimbing);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'jenjang' => JenjangEnum::S1,
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'nama_bulan' => 'Desember 2026',
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(10)->toDateString(),
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
    }

    public function test_admin_can_search_by_nim_nisn_in_tab_sudah_ditempatkan(): void
    {
        $peserta1 = $this->createUser('Peserta Satu', 'peserta1@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta1->id,
            'nim_nisn' => '123456789012',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-05-10',
            'alamat' => 'Sleman',
            'no_hp' => '081234567890',
        ]);

        $peserta2 = $this->createUser('Peserta Dua', 'peserta2@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta2->id,
            'nim_nisn' => '999988887777',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-06-12',
            'alamat' => 'Sleman',
            'no_hp' => '081234567891',
        ]);

        Penempatan::create([
            'peserta_id' => $peserta1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Penempatan::create([
            'peserta_id' => $peserta2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index', [
            'tab' => 'sudah',
            'q' => '123456789012',
        ]));

        $response->assertOk();
        $response->assertSee('123456789012');
        $response->assertDontSee('999988887777');
        $penempatanList = $response->viewData('penempatanList');
        $this->assertTrue($penempatanList->contains('peserta_id', $peserta1->id));
        $this->assertFalse($penempatanList->contains('peserta_id', $peserta2->id));
    }

    public function test_admin_can_search_by_nim_nisn_in_tab_belum_ditempatkan(): void
    {
        $pesertaAntrean1 = $this->createUser('Calon Peserta Antrean 1', 'antrean1@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $pesertaAntrean1->id,
            'nim_nisn' => '555544443333',
            'institusi' => 'Politeknik Negeri Yogyakarta',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2003-01-15',
            'alamat' => 'Sleman',
            'no_hp' => '081234567892',
        ]);

        $pendaftaran1 = Pendaftaran::create([
            'user_id' => $pesertaAntrean1->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $pesertaAntrean2 = $this->createUser('Calon Peserta Antrean 2', 'antrean2@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $pesertaAntrean2->id,
            'nim_nisn' => '777788889999',
            'institusi' => 'UIN Sunan Kalijaga',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2003-02-20',
            'alamat' => 'Sleman',
            'no_hp' => '081234567893',
        ]);

        $pendaftaran2 = Pendaftaran::create([
            'user_id' => $pesertaAntrean2->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index', [
            'tab' => 'belum',
            'q' => '555544443333',
        ]));

        $response->assertOk();
        $response->assertSee('555544443333');
        $response->assertDontSee('777788889999');
        $antrean = $response->viewData('antreanBelumDitempatkan');
        $this->assertTrue($antrean->contains('id', $pendaftaran1->id));
        $this->assertFalse($antrean->contains('id', $pendaftaran2->id));
    }

    public function test_admin_can_search_by_kelompok_magang_name_in_tab_belum_ditempatkan(): void
    {
        $ketua = $this->createUser('Ketua Tim Cyber', 'ketua.cyber@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $ketua->id,
            'nim_nisn' => '1122334455',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-01-01',
            'alamat' => 'Sleman',
            'no_hp' => '081234567894',
        ]);

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Cyber Security Sleman',
        ]);

        $pendaftaranKelompok = Pendaftaran::create([
            'user_id' => $ketua->id,
            'kelompok_magang_id' => $kelompok->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'kelompok',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index', [
            'tab' => 'belum',
            'q' => 'Cyber Security',
        ]));

        $response->assertOk();
        $response->assertSee('Ketua Tim Cyber');
    }

    public function test_admin_placing_peserta_creates_penempatan_and_logs_audit(): void
    {
        $peserta = $this->createUser('Randi Pratama', 'randi@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '1234512345',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-04-10',
            'alamat' => 'Sleman',
            'no_hp' => '081234567895',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.tetapkan'), [
            'pendaftaran_id' => $pendaftaran->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
        ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_placing_group_automatically_creates_placements_for_all_members(): void
    {
        $ketua = $this->createUser('Ketua Rombongan', 'ketua.rombongan@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $ketua->id,
            'nim_nisn' => '99001122',
            'institusi' => 'UNY',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-07-07',
            'alamat' => 'Sleman',
            'no_hp' => '081234567896',
        ]);

        $anggotaUser = $this->createUser('Anggota Rombongan Satu', 'anggota1.rombongan@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $anggotaUser->id,
            'nim_nisn' => '99001133',
            'institusi' => 'UNY',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-08-08',
            'alamat' => 'Sleman',
            'no_hp' => '081234567897',
        ]);

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Rombongan Multimedia',
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'user_id' => $anggotaUser->id,
            'nama' => 'Anggota Rombongan Satu',
            'email' => 'anggota1.rombongan@test.com',
            'nim_nisn' => '99001133',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'kelompok_magang_id' => $kelompok->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'kelompok',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.tetapkan'), [
            'pendaftaran_id' => $pendaftaran->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
        ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        // Ketua dan Anggota keduanya harus memiliki penempatan aktif
        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $ketua->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $anggotaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    public function test_admin_can_cancel_penempatan_before_start_and_it_releases_quota(): void
    {
        $peserta = $this->createUser('Peserta Batal', 'peserta.batal@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '888899990000',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'tgl_lahir' => '2002-09-09',
            'alamat' => 'Sleman',
            'no_hp' => '081234567898',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $initialTerisi = $this->kuota->fresh()->terisi;

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.batalkan', $penempatan), [
            'alasan' => 'Peserta mengundurkan diri karena diterima magang BUMN',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $penempatan->fresh()->status);
        $this->assertEquals(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->fresh()->status);
        $this->assertEquals($initialTerisi - 1, $this->kuota->fresh()->terisi);
    }
}
