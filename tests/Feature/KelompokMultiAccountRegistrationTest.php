<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Admin\Penempatan\AssignPesertaAction;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KelompokMultiAccountRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrasi_kelompok_membuat_akun_mandiri_untuk_ketua_dan_seluruh_anggota(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika UGM',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $response = $this->post(route('register'), [
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Riset Smart City',
            'username' => 'Andi Pratama',
            'email' => 'andi.ketua@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'no_telp' => '081234567890',
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => 'UGM-12345',
            'jenjang' => 'mahasiswa',
            'jurusan_id' => $jurusan->id,
            'anggota' => [
                [
                    'nama' => 'Budi Santoso',
                    'nim_nisn' => 'UGM-12346',
                    'no_hp' => '081234567891',
                    'email' => 'budi.anggota@example.com',
                    'password' => 'BudiPass123!',
                    'password_confirmation' => 'BudiPass123!',
                ],
                [
                    'nama' => 'Citra Dewi',
                    'nim_nisn' => 'UGM-12347',
                    'no_hp' => '081234567892',
                    'email' => 'citra.anggota@example.com',
                    'password' => 'CitraPass123!',
                    'password_confirmation' => 'CitraPass123!',
                ],
            ],
        ]);

        $response->assertRedirect(route('peserta.dashboard'));

        // 1. Verifikasi akun ketua
        $this->assertDatabaseHas('users', [
            'email' => 'andi.ketua@example.com',
            'role' => RoleEnum::Peserta->value,
        ]);
        $ketua = User::where('email', 'andi.ketua@example.com')->first();
        $this->assertTrue(Hash::check('Password123!', $ketua->password));

        // 2. Verifikasi akun Budi
        $this->assertDatabaseHas('users', [
            'email' => 'budi.anggota@example.com',
            'nama' => 'Budi Santoso',
            'role' => RoleEnum::Peserta->value,
        ]);
        $budi = User::where('email', 'budi.anggota@example.com')->first();
        $this->assertTrue(Hash::check('BudiPass123!', $budi->password));

        // 3. Verifikasi akun Citra
        $this->assertDatabaseHas('users', [
            'email' => 'citra.anggota@example.com',
            'nama' => 'Citra Dewi',
            'role' => RoleEnum::Peserta->value,
        ]);
        $citra = User::where('email', 'citra.anggota@example.com')->first();
        $this->assertTrue(Hash::check('CitraPass123!', $citra->password));

        // 4. Verifikasi kelompok magang & anggota terhubung
        $kelompok = $ketua->kelompokMagang;
        $this->assertNotNull($kelompok);
        $this->assertEquals('Tim Riset Smart City', $kelompok->nama_kelompok);
        $this->assertCount(2, $kelompok->anggota);

        $anggotaBudi = $kelompok->anggota()->where('email', 'budi.anggota@example.com')->first();
        $this->assertEquals($budi->id, $anggotaBudi->user_id);

        $anggotaCitra = $kelompok->anggota()->where('email', 'citra.anggota@example.com')->first();
        $this->assertEquals($citra->id, $anggotaCitra->user_id);
    }

    public function test_seluruh_anggota_kelompok_mendapat_penempatan_dan_dapat_login_mandiri_ke_dashboard(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::Admin]);
        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing]);
        $bidang = Bidang::create([
            'nama_bidang' => 'Layanan E-Government',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);
        $bulan = BulanMagang::create([
            'nama_bulan' => 'Januari 2027',
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_buka' => now()->subMonth()->startOfMonth()->toDateString(),
            'tgl_tutup' => now()->subMonth()->endOfMonth()->toDateString(),
            'aktif' => true,
            'kuota_default' => 20,
        ]);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        $kuota = Kuota::create([
            'jurusan_id' => $jurusan->id,
            'bulan_magang_id' => $bulan->id,
            'jumlah' => 10,
            'terisi' => 0,
        ]);

        // Ketua mendaftar
        $ketua = User::factory()->create([
            'email' => 'ketua.tim@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => RoleEnum::Peserta,
        ]);

        $anggota1 = User::factory()->create([
            'email' => 'anggota1@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => RoleEnum::Peserta,
        ]);

        $anggota2 = User::factory()->create([
            'email' => 'anggota2@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => RoleEnum::Peserta,
        ]);

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Dev MagKom',
        ]);

        $kelompok->anggota()->create([
            'user_id' => $anggota1->id,
            'nama' => $anggota1->nama,
            'email' => $anggota1->email,
        ]);

        $kelompok->anggota()->create([
            'user_id' => $anggota2->id,
            'nama' => $anggota2->nama,
            'email' => $anggota2->email,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'bulan_magang_id' => $bulan->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        // Jalankan penempatan
        $assignAction = app(AssignPesertaAction::class);
        $assignAction->execute($pendaftaran, $admin, $pembimbing, $bidang);

        // Verifikasi penempatan ketua
        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $ketua->id,
            'pembimbing_id' => $pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        // Verifikasi penempatan anggota 1
        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $anggota1->id,
            'pembimbing_id' => $pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        // Verifikasi penempatan anggota 2
        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $anggota2->id,
            'pembimbing_id' => $pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        // Login mandiri anggota 1 dan akses dashboard
        $responseAnggota1 = $this->post(route('login'), [
            'email' => 'anggota1@example.com',
            'password' => 'Secret123!',
        ]);
        $responseAnggota1->assertRedirect(route('peserta.dashboard'));

        $this->actingAs($anggota1);
        $dashboardResponse = $this->get(route('peserta.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertViewHas('fase', 'aktif');
        $dashboardResponse->assertSee($pembimbing->nama);
    }
}
