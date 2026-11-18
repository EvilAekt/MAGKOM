<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Pengaturan;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Queries\Admin\Pendaftaran\PendaftaranIndexQuery;
use Carbon\CarbonInterface;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSlaWorkingDaysTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 's1',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->admin = new User;
        $this->admin->nama = 'Admin Verifikator';
        $this->admin->email = 'admin.sla@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();
    }

    protected function createPendaftar(string $nama, string $email, CarbonInterface $diajukanAt): Pendaftaran
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'Password123!';
        $user->role = RoleEnum::Peserta;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->save();

        PesertaProfil::create([
            'user_id' => $user->id,
            'nim_nisn' => (string) rand(10000000, 99999999),
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'nama_kontak_darurat' => 'Wali',
            'no_kontak_darurat' => '089876543210',
            'hubungan_kontak_darurat' => 'Orang Tua',
        ]);

        return Pendaftaran::create([
            'user_id' => $user->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'mandiri',
            'durasi_bulan' => 3,
            'diajukan_at' => $diajukanAt,
            'created_at' => $diajukanAt,
        ]);
    }

    public function test_sla_watchdog_filters_pendaftaran_sesuai_threshold_dinamis(): void
    {
        Pengaturan::setNilai('sla_verifikasi_hari', '5');
        Pengaturan::clearCache();

        // 1. Pendaftar Breach (diajukan 6 hari lalu)
        $pBreach = $this->createPendaftar('Peserta Breach', 'breach@test.com', now()->subDays(6));

        // 2. Pendaftar Mendekati (diajukan 4 hari lalu)
        $pMendekati = $this->createPendaftar('Peserta Mendekati', 'mendekati@test.com', now()->subDays(4));

        // 3. Pendaftar Aman (diajukan 1 hari lalu)
        $pAman = $this->createPendaftar('Peserta Aman', 'aman@test.com', now()->subDays(1));

        // Filter: breach
        $resBreach = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['sla' => 'breach']));
        $resBreach->assertOk();
        $resBreach->assertSee($pBreach->user->nama);
        $resBreach->assertDontSee($pMendekati->user->nama);
        $resBreach->assertDontSee($pAman->user->nama);

        // Filter: mendekati
        $resMendekati = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['sla' => 'mendekati']));
        $resMendekati->assertOk();
        $resMendekati->assertSee($pMendekati->user->nama);
        $resMendekati->assertDontSee($pBreach->user->nama);
        $resMendekati->assertDontSee($pAman->user->nama);

        // Filter: aman
        $resAman = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['sla' => 'aman']));
        $resAman->assertOk();
        $resAman->assertSee($pAman->user->nama);
        $resAman->assertDontSee($pBreach->user->nama);
        $resAman->assertDontSee($pMendekati->user->nama);
    }

    public function test_queue_stats_menghitung_metrik_sla_dengan_benar(): void
    {
        Pengaturan::setNilai('sla_verifikasi_hari', '5');
        Pengaturan::clearCache();

        $this->createPendaftar('Peserta A', 'a@test.com', now()->subDays(6));
        $this->createPendaftar('Peserta B', 'b@test.com', now()->subDays(4));
        $this->createPendaftar('Peserta C', 'c@test.com', now()->subDays(1));

        $query = app(PendaftaranIndexQuery::class);
        $stats = $query->getQueueStats();

        $this->assertSame(1, $stats['sla_breach']);
        $this->assertSame(1, $stats['sla_mendekati']);
        $this->assertSame(2, $stats['sla_warning']); // Mendekati + breach (total yang >= warning)
    }
}
