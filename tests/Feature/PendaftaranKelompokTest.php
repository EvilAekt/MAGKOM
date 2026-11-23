<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\KelompokMagang;
use App\Models\Pengaturan;
use App\Models\User;
use Database\Seeders\AcceptedPesertaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaftaranKelompokTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrasi_mandiri_berhasil_seperti_biasa(): void
    {
        $response = $this->post('/register', [
            'username' => 'Peserta Mandiri Sleman',
            'email' => 'mandiri@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'mandiri',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'mandiri@magkom.test',
            'nama' => 'Peserta Mandiri Sleman',
            'role' => RoleEnum::Peserta->value,
        ]);

        $user = User::where('email', 'mandiri@magkom.test')->first();
        $this->assertNull($user->kelompokMagang);
        $this->assertEquals('Peserta Mandiri Sleman', $user->nama_tampil);
    }

    public function test_registrasi_kelompok_valid_menyimpan_nama_kelompok_dan_anggota(): void
    {
        $payload = [
            'username' => 'Ahmad Ketua Tim',
            'email' => 'ketua@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Inovasi Sleman',
            'anggota' => [
                [
                    'nama' => 'Budi Anggota Dua',
                    'nim_nisn' => '20240002',
                    'no_hp' => '081234567891',
                    'email' => 'budi@magkom.test',
                    'password' => 'BudiPass123!',
                    'password_confirmation' => 'BudiPass123!',
                ],
                [
                    'nama' => 'Citra Anggota Tiga',
                    'nim_nisn' => '20240003',
                    'no_hp' => '081234567892',
                    'email' => 'citra@magkom.test',
                    'password' => 'CitraPass123!',
                    'password_confirmation' => 'CitraPass123!',
                ],
            ],
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('peserta.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'ketua@magkom.test',
            'nama' => 'Ahmad Ketua Tim',
            'role' => RoleEnum::Peserta->value,
        ]);

        $this->assertDatabaseHas('kelompok_magang', [
            'nama_kelompok' => 'Tim Inovasi Sleman',
        ]);

        $kelompok = KelompokMagang::where('nama_kelompok', 'Tim Inovasi Sleman')->first();
        $this->assertNotNull($kelompok);
        $this->assertCount(2, $kelompok->anggota);

        $this->assertDatabaseHas('kelompok_anggota', [
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Budi Anggota Dua',
            'nim_nisn' => '20240002',
        ]);

        $this->assertDatabaseHas('kelompok_anggota', [
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Citra Anggota Tiga',
            'nim_nisn' => '20240003',
        ]);

        $ketuaUser = User::where('email', 'ketua@magkom.test')->first();
        $this->assertEquals('Tim Inovasi Sleman', $ketuaUser->nama_tampil);
    }

    public function test_registrasi_kelompok_gagal_jika_nama_kelompok_kosong(): void
    {
        $payload = [
            'username' => 'Ahmad Ketua Tim',
            'email' => 'ketua_fail@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => '', // kosong
            'anggota' => [
                [
                    'nama' => 'Budi Anggota Dua',
                    'nim_nisn' => '20240002',
                    'no_hp' => '081234567891',
                    'email' => 'budi@magkom.test',
                ],
            ],
        ];

        $response = $this->post('/register', $payload);
        $response->assertSessionHasErrors(['nama_kelompok']);
        $this->assertDatabaseMissing('users', ['email' => 'ketua_fail@magkom.test']);
    }

    public function test_dashboard_menampilkan_nama_kelompok_untuk_akun_kelompok(): void
    {
        $user = User::factory()->create([
            'nama' => 'Ketua Alpha',
            'email' => 'alpha@magkom.test',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $user->id,
            'nama_kelompok' => 'Tim Alpha Digital',
        ]);

        $kelompok->anggota()->create([
            'nama' => 'Rekan Alpha 1',
            'nim_nisn' => '123456',
            'no_hp' => '0811111111',
            'email' => 'rekan@magkom.test',
        ]);

        $response = $this->actingAs($user)->get(route('peserta.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Tim Alpha Digital');
    }

    public function test_registrasi_kelompok_gagal_jika_rekan_lebih_dari_tiga_orang(): void
    {
        $payload = [
            'username' => 'Ketua Lima Orang',
            'email' => 'lima@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Terlalu Banyak',
            'anggota' => [
                ['nama' => 'Rekan 1', 'nim_nisn' => '111', 'no_hp' => '0811', 'email' => 'r1@magkom.test'],
                ['nama' => 'Rekan 2', 'nim_nisn' => '222', 'no_hp' => '0822', 'email' => 'r2@magkom.test'],
                ['nama' => 'Rekan 3', 'nim_nisn' => '333', 'no_hp' => '0833', 'email' => 'r3@magkom.test'],
                ['nama' => 'Rekan 4', 'nim_nisn' => '444', 'no_hp' => '0844', 'email' => 'r4@magkom.test'], // Total 5 orang (1 ketua + 4 rekan), harus ditolak karena maks 4 orang
            ],
        ];

        $response = $this->post('/register', $payload);
        $response->assertSessionHasErrors(['anggota']);
        $this->assertDatabaseMissing('users', ['email' => 'lima@magkom.test']);
    }

    public function test_registrasi_kelompok_dengan_redirect_jumlah_wajib_cocok_jumlahnya(): void
    {
        // Pilihan kuota fix 3 orang (1 ketua + 2 rekan)
        $payloadSalah = [
            'username' => 'Ketua Kuota Tiga',
            'email' => 'kuotatiga@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'redirect_tipe' => 'kelompok',
            'redirect_jumlah' => '3',
            'nama_kelompok' => 'Tim Kuota Tiga',
            'anggota' => [
                ['nama' => 'Rekan 1', 'nim_nisn' => '111', 'no_hp' => '0811', 'email' => 'r1@magkom.test'],
                // hanya 1 rekan padahal kuota 3 orang (butuh 2 rekan)
            ],
        ];

        $response = $this->post('/register', $payloadSalah);
        $response->assertSessionHasErrors(['anggota']);

        $payloadBenar = [
            'username' => 'Ketua Kuota Tiga',
            'email' => 'kuotatiga_ok@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'redirect_tipe' => 'kelompok',
            'redirect_jumlah' => '3',
            'nama_kelompok' => 'Tim Kuota Tiga Sukses',
            'anggota' => [
                ['nama' => 'Rekan 1', 'nim_nisn' => '111', 'no_hp' => '0811', 'email' => 'r1@magkom.test', 'password' => 'PassRekan1!', 'password_confirmation' => 'PassRekan1!'],
                ['nama' => 'Rekan 2', 'nim_nisn' => '222', 'no_hp' => '0822', 'email' => 'r2@magkom.test', 'password' => 'PassRekan2!', 'password_confirmation' => 'PassRekan2!'],
            ],
        ];

        $responseBenar = $this->post('/register', $payloadBenar);
        $responseBenar->assertRedirect(route('peserta.dashboard'));
        $this->assertDatabaseHas('kelompok_magang', ['nama_kelompok' => 'Tim Kuota Tiga Sukses']);
    }

    public function test_registrasi_kelompok_membersihkan_kartu_anggota_kosong_dan_lolos_validasi(): void
    {
        // Rombongan 2 orang (1 ketua + 1 anggota), browser mengirimkan kartu kedua dan ketiga dengan string kosong
        $payload = [
            'username' => 'Ketua Tim Dua Orang',
            'email' => 'timdua@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'redirect_tipe' => 'kelompok',
            'redirect_jumlah' => '2',
            'nama_kelompok' => 'Tim Duo Solid',
            'anggota' => [
                ['nama' => 'Rekan Satu', 'nim_nisn' => '12345', 'no_hp' => '081234567891', 'email' => 'rekan1@magkom.test', 'password' => 'PassRekan1!', 'password_confirmation' => 'PassRekan1!'],
                ['nama' => '', 'nim_nisn' => '', 'no_hp' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''],
                ['nama' => '', 'nim_nisn' => '', 'no_hp' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''],
            ],
        ];

        $response = $this->post('/register', $payload);
        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect(route('peserta.dashboard'));

        $this->assertDatabaseHas('kelompok_magang', ['nama_kelompok' => 'Tim Duo Solid']);
        $kelompok = KelompokMagang::where('nama_kelompok', 'Tim Duo Solid')->first();
        $this->assertCount(1, $kelompok->anggota);
    }

    public function test_registrasi_mandiri_mengabaikan_redirect_tipe_kelompok_jika_memilih_mandiri(): void
    {
        $payload = [
            'username' => 'Budi Mandiri Saja',
            'email' => 'budimandiri@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'mandiri',
            'redirect_tipe' => 'kelompok', // bekas URL landing page kelompok
            'redirect_jumlah' => '2',
        ];

        $response = $this->post('/register', $payload);
        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'budimandiri@magkom.test']);
    }

    public function test_seeder_accepted_peserta_membuat_akun_pov_mandiri_dan_kelompok_diterima(): void
    {
        $this->seed(AcceptedPesertaSeeder::class);

        // 1. Uji Akun Mandiri Diterima
        $userMandiri = User::where('email', 'peserta@magkom.go.id')->first();
        $this->assertNotNull($userMandiri);
        $resMandiri = $this->actingAs($userMandiri)->get(route('peserta.dashboard'));
        $resMandiri->assertStatus(200);

        // 2. Uji Akun Kelompok Diterima
        $userKelompok = User::where('email', 'kelompok@magkom.go.id')->first();
        $this->assertNotNull($userKelompok);
        $this->assertNotNull($userKelompok->kelompokMagang);
        $this->assertEquals('Tim Inovasi Smart Sleman', $userKelompok->nama_tampil);
        $this->assertCount(2, $userKelompok->kelompokMagang->anggota);

        $resKelompok = $this->actingAs($userKelompok)->get(route('peserta.dashboard'));
        $resKelompok->assertStatus(200);
        $resKelompok->assertSee('Tim Inovasi Smart Sleman');
    }

    public function test_profil_peserta_menampilkan_roster_kelompok_dan_tetap_rapi_untuk_mandiri(): void
    {
        $this->seed(AcceptedPesertaSeeder::class);

        $userKelompok = User::where('email', 'kelompok@magkom.go.id')->firstOrFail();
        $resKelompok = $this->actingAs($userKelompok)->get(route('peserta.profil.show'));
        $resKelompok->assertStatus(200);
        $resKelompok->assertSee('Tim Kelompok: Tim Inovasi Smart Sleman');
        $resKelompok->assertSee('Citra Lestari');
        $resKelompok->assertSee('Bagas Wicaksono');
        $resKelompok->assertSee('Ketua Tim');

        $userMandiri = User::where('email', 'peserta@magkom.go.id')->firstOrFail();
        $resMandiri = $this->actingAs($userMandiri)->get(route('peserta.profil.show'));
        $resMandiri->assertStatus(200);
        $resMandiri->assertDontSee('Tim Kelompok:');
    }

    public function test_halaman_register_merender_batas_maksimal_sesuai_pengaturan_sistem(): void
    {
        Pengaturan::setNilai('maks_anggota_kelompok', '5');

        $response = $this->get(route('register', ['tipe' => 'kelompok']));
        $response->assertStatus(200);
        $response->assertSee('Kelompok (2–5 Orang)');
        $response->assertSee('data-max-rekan="4"', false);
        $response->assertSee('data-max-total="5"', false);
    }

    public function test_registrasi_kelompok_sukses_dengan_maksimal_anggota_dari_pengaturan_sistem(): void
    {
        Pengaturan::setNilai('maks_anggota_kelompok', '3');

        // Total 3 orang (1 ketua + 2 rekan) -> Sukses
        $payloadSukses = [
            'username' => 'Ketua Tim Tiga Orang',
            'email' => 'ketua3@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'redirect_tipe' => 'kelompok',
            'redirect_jumlah' => '3',
            'nama_kelompok' => 'Tim Trio Sukses',
            'anggota' => [
                ['nama' => 'Rekan 1', 'nim_nisn' => '111', 'no_hp' => '0811', 'email' => 'r1@magkom.test', 'password' => 'PassRekan1!', 'password_confirmation' => 'PassRekan1!'],
                ['nama' => 'Rekan 2', 'nim_nisn' => '222', 'no_hp' => '0822', 'email' => 'r2@magkom.test', 'password' => 'PassRekan2!', 'password_confirmation' => 'PassRekan2!'],
            ],
        ];

        $resSukses = $this->post('/register', $payloadSukses);
        $resSukses->assertSessionDoesntHaveErrors();
        $resSukses->assertRedirect(route('peserta.dashboard'));
        $this->assertDatabaseHas('kelompok_magang', ['nama_kelompok' => 'Tim Trio Sukses']);
    }

    public function test_registrasi_kelompok_ditolak_jika_melebihi_maksimal_anggota_dari_pengaturan_sistem(): void
    {
        Pengaturan::setNilai('maks_anggota_kelompok', '3');

        // Total 4 orang (1 ketua + 3 rekan) padahal batas 3 -> Ditolak
        $payloadGagal = [
            'username' => 'Ketua Tim Empat Orang',
            'email' => 'ketua4@magkom.test',
            'no_telp' => '081234567891',
            'password' => 'Password123!',
            'setuju_privasi' => '1',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Kuartet Ilegal',
            'anggota' => [
                ['nama' => 'Rekan 1', 'nim_nisn' => '111', 'no_hp' => '0811', 'email' => 'r1@magkom.test'],
                ['nama' => 'Rekan 2', 'nim_nisn' => '222', 'no_hp' => '0822', 'email' => 'r2@magkom.test'],
                ['nama' => 'Rekan 3', 'nim_nisn' => '333', 'no_hp' => '0833', 'email' => 'r3@magkom.test'],
            ],
        ];

        $resGagal = $this->post('/register', $payloadGagal);
        $resGagal->assertSessionHasErrors(['anggota']);
        $this->assertDatabaseMissing('users', ['email' => 'ketua4@magkom.test']);
    }

    public function test_halaman_register_mengunci_roster_anggota_jika_kuota_dan_jumlah_dipilih_di_awal(): void
    {
        $response = $this->get('/register?bulan=1&jurusan=1&tipe=kelompok&jumlah=3');
        $response->assertStatus(200);
        $response->assertSee('data-locked-count="true"', false);
        $response->assertDontSee('id="btn-add-anggota"', false);
        $response->assertDontSee('roster-locked-banner');
    }

    public function test_halaman_register_tetap_membuka_roster_jika_tanpa_kuota_terpilih(): void
    {
        $response = $this->get('/register?tipe=kelompok');
        $response->assertStatus(200);
        $response->assertSee('data-locked-count="false"', false);
        $response->assertSee('id="btn-add-anggota"', false);
    }
}
