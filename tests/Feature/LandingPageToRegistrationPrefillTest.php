<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingPageToRegistrationPrefillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['captcha.strict_testing' => false]);
    }

    public function test_halaman_register_menampilkan_kartu_ringkasan_kuota_dan_hidden_inputs_saat_dibuka_dari_landing_page(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'deskripsi' => 'Pengembangan Software',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kategori' => 'smk',
            'aktif' => true,
        ]);
        $bidang->jurusan()->attach($jurusan->id);

        $bulan = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => CarbonImmutable::now()->addMonth()->startOfMonth(),
            'tgl_buka' => CarbonImmutable::now()->subDays(5),
            'tgl_tutup' => CarbonImmutable::now()->addDays(20),
            'kuota_total' => 10,
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
        ]);

        $url = route('register', [
            'bulan' => $bulan->id,
            'bidang' => $bidang->id,
            'jenjang' => 'smk',
            'jurusan' => $jurusan->id,
            'tipe' => 'mandiri',
            'jumlah' => 1,
        ]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee('Rekayasa Perangkat Lunak');
        $response->assertSee('Aplikasi Informatika');
        $response->assertSee('Ubah');
        $response->assertSee('name="redirect_bulan" value="'.$bulan->id.'"', false);
        $response->assertSee('name="redirect_bidang" value="'.$bidang->id.'"', false);
        $response->assertSee('name="redirect_jurusan" value="'.$jurusan->id.'"', false);
        $response->assertSee('name="redirect_tipe" value="mandiri"', false);
        $response->assertSee('name="redirect_jumlah" value="1"', false);
    }

    public function test_halaman_register_jalur_kelompok_menampilkan_bagian_tim_dan_hidden_params(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Infrastruktur TIK',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
            'kategori' => 'smk',
            'aktif' => true,
        ]);
        $bidang->jurusan()->attach($jurusan->id);

        $bulan = BulanMagang::create([
            'nama_bulan' => 'Desember 2026',
            'bulan_mulai' => CarbonImmutable::now()->addMonths(2)->startOfMonth(),
            'tgl_buka' => CarbonImmutable::now()->subDays(5),
            'tgl_tutup' => CarbonImmutable::now()->addDays(20),
            'kuota_total' => 10,
            'aktif' => true,
        ]);

        Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 6,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $url = route('register', [
            'bulan' => $bulan->id,
            'bidang' => $bidang->id,
            'jenjang' => 'smk',
            'jurusan' => $jurusan->id,
            'tipe' => 'kelompok',
            'jumlah' => 3,
        ]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee('Kelompok (3 Orang)');
        $response->assertSee('name="nama_kelompok"', false);
        $response->assertSee('name="redirect_tipe" value="kelompok"', false);
        $response->assertSee('name="redirect_jumlah" value="3"', false);
    }

    public function test_registrasi_berhasil_menyimpan_pilihan_kuota_ke_session_dan_membuat_profil(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulan = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => CarbonImmutable::now()->addMonth()->startOfMonth(),
            'tgl_buka' => CarbonImmutable::now()->subDays(5),
            'tgl_tutup' => CarbonImmutable::now()->addDays(20),
            'kuota_total' => 10,
            'aktif' => true,
        ]);

        $postData = [
            'nama' => 'Peserta Uji Landing',
            'email' => 'peserta.landing@example.com',
            'no_telp' => '081298765432',
            'password' => 'PasswordAman123',
            'password_confirmation' => 'PasswordAman123',
            'tipe_pendaftaran' => 'mandiri',
            'jenjang' => 'mahasiswa',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '21/123456/TK/54321',
            'persetujuan_kebijakan' => 1,
            'redirect_bulan' => $bulan->id,
            'redirect_jurusan' => $jurusan->id,
            'redirect_tipe' => 'mandiri',
            'redirect_jumlah' => 1,
        ];

        $response = $this->post(route('register'), $postData);

        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'peserta.landing@example.com',
            'role' => RoleEnum::Peserta->value,
        ]);

        $user = User::where('email', 'peserta.landing@example.com')->first();
        $this->assertNotNull($user->profil);
        $this->assertEquals($jurusan->id, $user->profil->jurusan_id);
        $this->assertEquals('s1', $user->profil->jenjang->value);

        // Session preferensi kuota tersimpan
        $preferensi = session('pendaftaran_preferensi');
        $this->assertIsArray($preferensi);
        $this->assertEquals($bulan->id, $preferensi['bulan_id']);
        $this->assertEquals($jurusan->id, $preferensi['jurusan_id']);
    }

    public function test_halaman_login_membawa_parameter_kuota_dan_menyimpannya_saat_login(): void
    {
        $user = User::create([
            'nama' => 'Peserta Lama',
            'email' => 'peserta.lama@example.com',
            'password' => Hash::make('PasswordAman123'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $response = $this->get(route('login', [
            'bulan' => 5,
            'bidang' => 2,
            'jurusan' => 10,
            'tipe' => 'mandiri',
            'jumlah' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Melanjutkan permohonan magang dari kuota pilihan Anda.');
        $response->assertSee('name="redirect_bulan" value="5"', false);

        $loginResponse = $this->post(route('login'), [
            'email' => 'peserta.lama@example.com',
            'password' => 'PasswordAman123',
            'redirect_bulan' => 5,
            'redirect_bidang' => 2,
            'redirect_jurusan' => 10,
            'redirect_tipe' => 'mandiri',
            'redirect_jumlah' => 1,
        ]);

        $loginResponse->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticatedAs($user);

        $preferensi = session('pendaftaran_preferensi');
        $this->assertIsArray($preferensi);
        $this->assertEquals(5, $preferensi['bulan_id']);
        $this->assertEquals(2, $preferensi['bidang_id']);
        $this->assertEquals(10, $preferensi['jurusan_id']);
    }

    public function test_halaman_pendaftaran_peserta_menggunakan_preselected_kuota_dari_session(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $user = new User;
        $user->nama = 'Peserta Aktif';
        $user->email = 'peserta.aktif@example.com';
        $user->password = Hash::make('PasswordAman123');
        $user->role = RoleEnum::Peserta;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->save();

        $profil = PesertaProfil::create([
            'user_id' => $user->id,
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Amikom',
            'nim_nisn' => '20.11.1234',
            'no_hp' => '081234567890',
            'foto' => 'berkas/foto.jpg',
            'nama_kontak_darurat' => 'Wali Peserta',
            'no_kontak_darurat' => '081987654321',
            'hubungan_kontak_darurat' => 'Orang Tua',
        ]);

        $bulan1 = BulanMagang::create([
            'nama_bulan' => 'Bulan Pertama',
            'bulan_mulai' => CarbonImmutable::now()->addMonth()->startOfMonth(),
            'tgl_buka' => CarbonImmutable::now()->subDays(5),
            'tgl_tutup' => CarbonImmutable::now()->addDays(20),
            'kuota_total' => 10,
            'aktif' => true,
        ]);

        $bulan2 = BulanMagang::create([
            'nama_bulan' => 'Bulan Kedua',
            'bulan_mulai' => CarbonImmutable::now()->addMonths(2)->startOfMonth(),
            'tgl_buka' => CarbonImmutable::now()->subDays(5),
            'tgl_tutup' => CarbonImmutable::now()->addDays(20),
            'kuota_total' => 10,
            'aktif' => true,
        ]);

        $kuota1 = Kuota::create([
            'bulan_magang_id' => $bulan1->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $kuota2 = Kuota::create([
            'bulan_magang_id' => $bulan2->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        // Login dengan session preferensi memilih bulan kedua
        $response = $this->actingAs($user->fresh())
            ->withSession([
                'pendaftaran_preferensi' => [
                    'bulan_id' => $bulan2->id,
                    'jurusan_id' => $jurusan->id,
                    'tipe' => 'mandiri',
                    'jumlah' => 1,
                ],
            ])
            ->get(route('peserta.pendaftaran.index'));

        $response->assertStatus(200);
        $response->assertViewHas('preselectedKuotaId', $kuota2->id);
        $response->assertSee('value="'.$kuota2->id.'"', false);
        $response->assertSee('data-bulan-id="'.$bulan2->id.'"', false);
        $response->assertSee('selected', false);
    }
}
