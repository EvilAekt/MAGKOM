<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutupWaitingListCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);
    }

    protected function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $user = new User();
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'Password123!';
        $user->role = $role;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    /** @test */
    public function it_automatically_closes_waiting_list_for_active_month_that_has_started(): void
    {
        // 1. Setup Admin
        $this->createUser('Super Admin', 'super@slemankab.go.id', RoleEnum::SuperAdmin);

        // 2. Bulan magang yang sudah lewat / mulai hari ini (misal bulan_mulai = 5 hari yang lalu)
        $bulanMulai = BulanMagang::create([
            'bulan_mulai' => now('Asia/Jakarta')->subDays(5)->startOfMonth()->toDateString(),
            'tgl_buka' => now('Asia/Jakarta')->subDays(25)->toDateString(),
            'tgl_tutup' => now('Asia/Jakarta')->subDays(10)->toDateString(),
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMulai->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terisi' => 5,
            'aktif' => true,
        ]);

        // 3. Calon peserta dengan status waiting_list
        $peserta = $this->createUser('Peserta WL', 'pesertawl@example.com', RoleEnum::Peserta);
        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulanMulai->id,
            'kuota_id' => $kuota->id,
            'status' => StatusPendaftaranEnum::WaitingList,
            'durasi_bulan' => 3,
            'diajukan_at' => now('Asia/Jakarta')->subDays(15),
        ]);

        // 4. Jalankan artisan command
        $this->artisan('magkom:tutup-waiting-list')
            ->expectsOutputToContain('antrean waiting list berhasil ditutup')
            ->assertSuccessful();

        // 5. Verifikasi status pendaftaran berubah menjadi ditolak dengan catatan AC-04-7
        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $pendaftaran->status);
        $this->assertStringContainsString('AC-04-7', (string) $pendaftaran->catatan);
    }

    /** @test */
    public function it_does_not_close_waiting_list_for_future_months(): void
    {
        // Bulan magang di masa depan (2 bulan lagi)
        $bulanDepan = BulanMagang::create([
            'bulan_mulai' => now('Asia/Jakarta')->addMonths(2)->startOfMonth()->toDateString(),
            'tgl_buka' => now('Asia/Jakarta')->subDays(5)->toDateString(),
            'tgl_tutup' => now('Asia/Jakarta')->addDays(15)->toDateString(),
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanDepan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terisi' => 5,
            'aktif' => true,
        ]);

        $peserta = $this->createUser('Peserta Masa Depan', 'pesertafuture@example.com', RoleEnum::Peserta);
        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulanDepan->id,
            'kuota_id' => $kuota->id,
            'status' => StatusPendaftaranEnum::WaitingList,
            'durasi_bulan' => 3,
            'diajukan_at' => now('Asia/Jakarta')->subDays(2),
        ]);

        $this->artisan('magkom:tutup-waiting-list')
            ->assertSuccessful();

        $pendaftaran->refresh();
        // Status tetap waiting_list karena bulan magang belum berjalan
        $this->assertEquals(StatusPendaftaranEnum::WaitingList, $pendaftaran->status);
    }
}
