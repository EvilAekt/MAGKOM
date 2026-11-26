<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Services\PlacementService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TimAnggotaLoginBiodataDanPenempatanTest extends TestCase
{
    use RefreshDatabase;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Informasi dan Komunikasi Publik',
            'kode_bidang' => 'IKP',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Ilmu Komunikasi',
            'kategori' => 'mahasiswa',
            'bidang_id' => $this->bidang->id,
            'aktif' => true,
        ]);
        $this->jurusan->bidang()->syncWithoutDetaching([$this->bidang->id]);

        $this->bulan = BulanMagang::create([
            'nama_bulan' => 'Januari 2027',
            'bulan_mulai' => '2027-01-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-12-25',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing IKP',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
        ]);
    }

    public function test_anggota_tim_belum_bisa_login_sebelum_diterima_dan_aktif_setelah_diterima(): void
    {
        $registerResponse = $this->post(route('register'), [
            'username' => 'Keisha Ketua',
            'email' => 'keisha.ketua@magkom.test',
            'no_telp' => '081234000101',
            'password' => 'Magang2026!',
            'password_confirmation' => 'Magang2026!',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Publikasi UNY',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'Universitas Negeri Yogyakarta',
            'nim_nisn' => '22419141021',
            'anggota' => [
                [
                    'nama' => 'Kevin Anggota',
                    'nim_nisn' => '22419141022',
                    'no_hp' => '081234000102',
                    'email' => 'kevin.anggota@magkom.test',
                    'password' => 'Magang2026!',
                    'password_confirmation' => 'Magang2026!',
                ],
                [
                    'nama' => 'Nayla Anggota',
                    'nim_nisn' => '22419141023',
                    'no_hp' => '081234000103',
                    'email' => 'nayla.anggota@magkom.test',
                    'password' => 'Magang2026!',
                    'password_confirmation' => 'Magang2026!',
                ],
            ],
        ]);

        $registerResponse->assertRedirect(route('peserta.dashboard'));

        $ketua = User::where('email', 'keisha.ketua@magkom.test')->firstOrFail();
        $kevin = User::where('email', 'kevin.anggota@magkom.test')->firstOrFail();
        $nayla = User::where('email', 'nayla.anggota@magkom.test')->firstOrFail();

        $this->assertTrue((bool) $ketua->status_aktif);
        $this->assertFalse((bool) $kevin->status_aktif);
        $this->assertFalse((bool) $nayla->status_aktif);

        $kelompok = KelompokMagang::where('ketua_user_id', $ketua->id)->firstOrFail();
        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'kelompok_magang_id' => $kelompok->id,
            'tipe' => 'kelompok',
            'durasi_bulan' => 2,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // Logout ketua lalu coba login sebagai anggota sebelum diterima -> harus ditolak
        $this->post(route('logout'));

        $blockedLogin = $this->post(route('login'), [
            'email' => 'kevin.anggota@magkom.test',
            'password' => 'Magang2026!',
        ]);
        $blockedLogin->assertSessionHasErrors('email');
        $this->assertGuest();

        // Admin menerima berkas pendaftaran tim
        app(PlacementService::class)->terimaBerkas($pendaftaran, $this->admin, 'Berkas tim lengkap.');

        $kevin->refresh();
        $nayla->refresh();
        $this->assertTrue((bool) $kevin->status_aktif);
        $this->assertTrue((bool) $nayla->status_aktif);

        // Sekarang anggota berhasil login
        $allowedLogin = $this->post(route('login'), [
            'email' => 'kevin.anggota@magkom.test',
            'password' => 'Magang2026!',
        ]);
        $allowedLogin->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticatedAs($kevin);
    }

    public function test_anggota_dapat_mengisi_foto_dan_biodata_serta_tampil_di_penempatan_administrasi(): void
    {
        Storage::fake('public');

        $ketua = User::factory()->create([
            'nama' => 'Keisha Maharani',
            'email' => 'keisha@magkom.test',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        PesertaProfil::create([
            'user_id' => $ketua->id,
            'institusi' => 'Universitas Negeri Yogyakarta',
            'nim_nisn' => '22419141021',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234990301',
            'nama_pembimbing_sekolah' => 'Dra. Ratna Dewi, M.Si.',
            'alamat' => 'Jl. Colombo No. 1, Sleman',
            'nama_kontak_darurat' => 'Wali Keisha',
            'no_kontak_darurat' => '081900001122',
            'hubungan_kontak_darurat' => 'Orang Tua / Wali',
        ]);

        $anggota1 = User::factory()->create([
            'nama' => 'Kevin Adriansyah',
            'email' => 'kevin@magkom.test',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        PesertaProfil::create([
            'user_id' => $anggota1->id,
            'institusi' => 'Universitas Negeri Yogyakarta',
            'nim_nisn' => '22419141022',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234990302',
        ]);

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Konten Digital UNY',
        ]);
        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'user_id' => $anggota1->id,
            'nama' => 'Kevin Adriansyah',
            'nim_nisn' => '22419141022',
            'no_hp' => '081234990302',
            'email' => 'kevin@magkom.test',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'kelompok_magang_id' => $kelompok->id,
            'tipe' => 'kelompok',
            'durasi_bulan' => 2,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now()->subDays(3),
            'diputuskan_at' => now()->subDay(),
        ]);

        // 1. Anggota membuka halaman profil -> melihat perannya sebagai Anggota Tim, bukan Ketua
        $profilPage = $this->actingAs($anggota1)->get(route('peserta.profil.show'));
        $profilPage->assertOk();
        $profilPage->assertSee('Anggota Tim');
        $profilPage->assertSee('Keisha Maharani');
        $profilPage->assertSee('Kevin Adriansyah');

        // 2. Anggota mengisi foto & melengkapi biodata
        $saveRes = $this->actingAs($anggota1)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '22419141022',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jenjang' => JenjangEnum::S1->value,
            'jurusan_id' => $this->jurusan->id,
            'nama_pembimbing_sekolah' => 'Dr. Hendra Wijaya',
            'no_hp' => '081299887766',
            'alamat' => 'Jl. Gejayan No. 45, Depok, Sleman',
            'nama_kontak_darurat' => 'Budi Adriansyah',
            'no_kontak_darurat' => '081311223344',
            'hubungan_kontak_darurat' => 'Ayah',
            'foto' => UploadedFile::fake()->image('kevin.jpg', 300, 300),
            'persetujuan_wali' => '1',
        ]);
        $saveRes->assertRedirect(route('peserta.profil.show'));

        $anggota1->refresh();
        $this->assertNotNull($anggota1->profil?->foto);
        Storage::disk('public')->assertExists($anggota1->profil->foto);
        $this->assertDatabaseHas('kelompok_anggota', [
            'user_id' => $anggota1->id,
            'no_hp' => '081299887766',
        ]);

        // 3. Anggota dapat melihat detail pendaftaran tim (read-only), namun tetap dilarang mengakses tugas sebelum ditempatkan
        $this->actingAs($anggota1)->get(route('peserta.pendaftaran.detail', $pendaftaran))->assertOk();
        $this->actingAs($anggota1)->get(route('peserta.tugas.index'))->assertRedirect(route('peserta.dashboard'));

        // 4. Admin membuka Detail Antrean Penempatan -> melihat foto & biodata lengkap KETUA dan ANGGOTA
        $antreanRes = $this->actingAs($this->admin)->get(route('admin.penempatan.antrean.show', $pendaftaran));
        $antreanRes->assertOk();
        $antreanRes->assertSee('Tim Konten Digital UNY');
        $antreanRes->assertSee('Keisha Maharani');
        $antreanRes->assertSee('Kevin Adriansyah');
        $antreanRes->assertSee('Jl. Gejayan No. 45, Depok, Sleman');
        $antreanRes->assertSee('Dr. Hendra Wijaya');
        $antreanRes->assertSee('Budi Adriansyah');
        $antreanRes->assertSee($anggota1->profil->foto);

        // 5. SuperAdmin (read-only) juga bisa melihat biodata seluruh anggota tim tanpa form penetapan
        $saRes = $this->actingAs($this->superAdmin)->get(route('admin.penempatan.antrean.show', $pendaftaran));
        $saRes->assertOk();
        $saRes->assertSee('Kevin Adriansyah');
        $saRes->assertSee('Jl. Gejayan No. 45, Depok, Sleman');
        $saRes->assertDontSee(route('admin.penempatan.tetapkan'), false);

        // 6. Setelah pembimbing ditetapkan, Admin dapat melihat profil seluruh anggota di Detail Penempatan
        $penempatan = app(PlacementService::class)->tetapkanPenempatan($pendaftaran, $this->admin, $this->pembimbing, $this->bidang);
        $showRes = $this->actingAs($this->admin)->get(route('admin.penempatan.show', $penempatan));
        $showRes->assertOk();
        $showRes->assertSee('Profil &amp; Biodata Seluruh Anggota Tim', false);
        $showRes->assertSee('Kevin Adriansyah');
        $showRes->assertSee('Jl. Gejayan No. 45, Depok, Sleman');
    }
}
