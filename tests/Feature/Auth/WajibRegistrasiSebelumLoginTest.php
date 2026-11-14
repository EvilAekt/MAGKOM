<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider as SocialiteProviderContract;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class WajibRegistrasiSebelumLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function mockGoogleUser(string $id, string $email, string $name = 'Calon Mahasiswa'): void
    {
        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock(SocialiteProviderContract::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_user_google_belum_terdaftar_tidak_bisa_login_dan_dialihkan_ke_register(): void
    {
        $uniqueEmail = 'calon_peserta_baru@universitas.ac.id';
        $googleId = 'google_id_test_12345';

        $this->mockGoogleUser($googleId, $uniqueEmail, 'Budi Santoso');

        $response = $this->get(route('oauth.callback', 'google'));

        // Harus ditolak dan dialihkan ke formulir pendaftaran
        $response->assertRedirect(route('register'));
        $response->assertSessionHas('warning');
        $this->assertGuest();

        // Database users tidak boleh bertambah
        $this->assertDatabaseMissing('users', [
            'email' => $uniqueEmail,
        ]);

        // Data Google tersimpan di session untuk kemudahan calon peserta
        $oauthData = session('oauth_register_data');
        $this->assertIsArray($oauthData);
        $this->assertEquals($uniqueEmail, $oauthData['email']);
        $this->assertEquals('Budi Santoso', $oauthData['nama']);
        $this->assertEquals('google', $oauthData['provider']);
        $this->assertEquals($googleId, $oauthData['provider_id']);

        // Verifikasi audit log penolakan tercatat
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'users',
            'tipe' => 'aksi',
        ]);
    }

    public function test_user_google_yang_sudah_terdaftar_bisa_login_langsung(): void
    {
        $googleId = 'google_id_registered_999';
        $user = User::factory()->create([
            'email' => 'peserta.terdaftar@gmail.com',
            'nama' => 'Peserta Resmi',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'provider' => 'google',
            'provider_id' => $googleId,
            'email_verified_at' => now(),
        ]);

        $this->mockGoogleUser($googleId, $user->email, $user->nama);

        $response = $this->get(route('oauth.callback', 'google'));

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('peserta.pendaftaran.index'));
    }

    public function test_halaman_register_menampilkan_prefill_nama_dan_email_dari_oauth_session(): void
    {
        $response = $this->withSession([
            'oauth_register_data' => [
                'email' => 'auto.fill@kampus.ac.id',
                'nama' => 'Mahasiswa Cerdas',
                'provider' => 'google',
                'provider_id' => 'gid_123',
            ],
        ])->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('value="auto.fill@kampus.ac.id"', false);
        $response->assertSee('value="Mahasiswa Cerdas"', false);
        $response->assertSee('readonly', false);
    }

    public function test_registrasi_dengan_data_oauth_berhasil_membuat_user_dan_peserta_profil_lengkap(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'kode_bidang' => 'APTIKA',
            'deskripsi' => 'Bidang Aptika',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'bidang_id' => $bidang->id,
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $oauthEmail = 'calon.magang@ugm.ac.id';
        $oauthProviderId = 'google_id_new_888';

        $response = $this->withSession([
            'oauth_register_data' => [
                'email' => $oauthEmail,
                'nama' => 'Siti Aisyah',
                'provider' => 'google',
                'provider_id' => $oauthProviderId,
            ],
        ])->post(route('register'), [
            'username' => 'Siti Aisyah',
            'email' => $oauthEmail,
            'no_telp' => '081298765432',
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '20/456789/TK/51234',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();

        // Akun User harus memiliki data Google yang terhubung
        $user = User::where('email', $oauthEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('google', $user->provider);
        $this->assertEquals($oauthProviderId, $user->provider_id);
        $this->assertEquals(RoleEnum::Peserta, $user->role);

        // Profil Peserta WAJIB terbentuk dengan data akademik yang dipilih
        $profil = PesertaProfil::where('user_id', $user->id)->first();
        $this->assertNotNull($profil);
        $this->assertEquals(JenjangEnum::S1, $profil->jenjang);
        $this->assertEquals($jurusan->id, $profil->jurusan_id);
        $this->assertEquals('Universitas Gadjah Mada', $profil->institusi);
        $this->assertEquals('20/456789/TK/51234', $profil->nim_nisn);

        // Session OAuth register data harus dibersihkan setelah selesai
        $this->assertNull(session('oauth_register_data'));
    }

    public function test_registrasi_dengan_jenjang_mahasiswa_berhasil_disimpan_sebagai_s1(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Informasi dan Komunikasi Publik',
            'kode_bidang' => 'IKP',
            'deskripsi' => 'Bidang IKP',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'bidang_id' => $bidang->id,
            'nama_jurusan' => 'Ilmu Komunikasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $response = $this->post(route('register'), [
            'username' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@kampus.ac.id',
            'no_telp' => '081323456789',
            'password' => 'SandiKombinasi123',
            'password_confirmation' => 'SandiKombinasi123',
            'jenjang' => 'mahasiswa',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Indonesia',
            'nim_nisn' => '1906123456',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'ahmad.fauzi@kampus.ac.id')->first();
        $this->assertNotNull($user);

        $profil = PesertaProfil::where('user_id', $user->id)->first();
        $this->assertNotNull($profil);
        $this->assertEquals(JenjangEnum::S1, $profil->jenjang);
    }
}
