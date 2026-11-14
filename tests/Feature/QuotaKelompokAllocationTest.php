<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Admin\Penempatan\CancelPenempatanAction;
use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Exceptions\Domain\KuotaPenuhException;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\PlacementService;
use App\Services\QuotaAllocationService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QuotaKelompokAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Kuota::count() === 0) {
            $this->seed();
        }

        $this->admin = User::firstOrCreate(
            ['email' => 'admin.kuota.kelompok@kominfo.go.id'],
            [
                'nama' => 'Admin Kuota Kelompok',
                'password' => bcrypt('AdminSecret#2026'),
                'role' => RoleEnum::Admin,
                'email_verified_at' => now(),
                'status_aktif' => true,
            ]
        );

        $this->pembimbing = User::firstOrCreate(
            ['email' => 'pembimbing.kuota.kelompok@kominfo.go.id'],
            [
                'nama' => 'Pembimbing Kuota Kelompok',
                'password' => bcrypt('PembimbingSecret#2026'),
                'role' => RoleEnum::Pembimbing,
                'email_verified_at' => now(),
                'status_aktif' => true,
            ]
        );

        $this->bulan = BulanMagang::first();
        $this->jurusan = Jurusan::first();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function createPeserta(string $email, string $nama): User
    {
        return User::create([
            'nama' => $nama,
            'email' => $email,
            'password' => bcrypt('Secret#123'),
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
        ]);
    }

    private function getOrCreateKuota(int $jumlah = 10, int $terisi = 0): Kuota
    {
        $kuota = Kuota::firstOrCreate(
            ['bulan_magang_id' => $this->bulan->id, 'jurusan_id' => $this->jurusan->id],
            ['jumlah' => $jumlah, 'terisi' => $terisi, 'aktif' => true]
        );
        $kuota->update(['jumlah' => $jumlah, 'terisi' => $terisi, 'aktif' => true]);

        return $kuota->fresh();
    }

    public function test_pendaftaran_individu_memotong_satu_kuota(): void
    {
        $kuota = $this->getOrCreateKuota(10, 0);

        $peserta = $this->createPeserta('individu@test.com', 'Peserta Individu');
        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'individu',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $service = app(PlacementService::class);
        $service->terimaBerkas($pendaftaran, $this->admin);

        $kuota->refresh();
        $this->assertEquals(1, $kuota->terisi);
    }

    public function test_pendaftaran_kelompok_ketua_dan_satu_anggota_memotong_dua_kuota(): void
    {
        $kuota = $this->getOrCreateKuota(10, 0);

        $ketua = $this->createPeserta('ketua2@test.com', 'Ketua Tim Dua');
        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Dua Personil',
        ]);
        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Anggota 1',
            'nim_nisn' => '123451',
            'no_hp' => '08123456781',
            'email' => 'anggota1@test.com',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $service = app(PlacementService::class);
        $service->terimaBerkas($pendaftaran, $this->admin);

        $kuota->refresh();
        $this->assertEquals(2, $kuota->terisi);
    }

    public function test_pendaftaran_kelompok_ketua_dan_tiga_anggota_memotong_empat_kuota(): void
    {
        $kuota = $this->getOrCreateKuota(10, 0);

        $ketua = $this->createPeserta('ketua4@test.com', 'Ketua Tim Empat');
        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Empat Personil',
        ]);
        for ($i = 1; $i <= 3; $i++) {
            KelompokAnggota::create([
                'kelompok_magang_id' => $kelompok->id,
                'nama' => "Anggota {$i}",
                'nim_nisn' => "12345{$i}",
                'no_hp' => "0812345678{$i}",
                'email' => "anggota{$i}@test.com",
            ]);
        }

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $service = app(PlacementService::class);
        $service->terimaBerkas($pendaftaran, $this->admin);

        $kuota->refresh();
        $this->assertEquals(4, $kuota->terisi);
    }

    public function test_penerimaan_kelompok_ditolak_jika_sisa_kuota_tidak_mencukupi_seluruh_anggota(): void
    {
        // Kapasitas 5, sudah terisi 3 (sisa 2). Kelompok butuh 3 (1 ketua + 2 anggota).
        $kuota = $this->getOrCreateKuota(5, 3);

        $ketua = $this->createPeserta('ketua.tolak@test.com', 'Ketua Kurang Kuota');
        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Tiga Butuh Tiga',
        ]);
        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Anggota A',
            'nim_nisn' => '99901',
            'no_hp' => '08123456789',
            'email' => 'a@test.com',
        ]);
        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Anggota B',
            'nim_nisn' => '99902',
            'no_hp' => '08123456780',
            'email' => 'b@test.com',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $service = app(PlacementService::class);

        $this->expectException(KuotaPenuhException::class);

        try {
            $service->terimaBerkas($pendaftaran, $this->admin);
        } finally {
            // Kuota terisi tidak boleh bertambah jika gagal/exception
            $kuota->refresh();
            $this->assertEquals(3, $kuota->terisi);

            $pendaftaran->refresh();
            $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->status);
        }
    }

    public function test_pembatalan_penempatan_kelompok_sebelum_mulai_mengembalikan_seluruh_kuota_tim(): void
    {
        $kuota = $this->getOrCreateKuota(10, 0);

        $ketua = $this->createPeserta('ketua.batal@test.com', 'Ketua Batal');
        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Batal Tiga',
        ]);
        for ($i = 1; $i <= 2; $i++) {
            KelompokAnggota::create([
                'kelompok_magang_id' => $kelompok->id,
                'nama' => "Anggota Batal {$i}",
                'nim_nisn' => "8880{$i}",
                'no_hp' => "0898765432{$i}",
                'email' => "batal{$i}@test.com",
            ]);
        }

        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $kuota->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $placementService = app(PlacementService::class);
        $p = $placementService->terimaBerkas($pendaftaran, $this->admin);

        $kuota->refresh();
        $this->assertEquals(3, $kuota->terisi);

        // Buat penempatan aktif dengan tgl_mulai di masa depan (2026-11-01 > now 2026-10-08)
        $penempatan = Penempatan::create([
            'pendaftaran_id' => $p->id,
            'peserta_id' => $ketua->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
        ]);

        $cancelAction = app(CancelPenempatanAction::class);
        $cancelAction->execute($penempatan, $this->admin, 'Permintaan pembatalan kelompok');

        // Kuota harus berkurang 3 kembali ke 0
        $kuota->refresh();
        $this->assertEquals(0, $kuota->terisi);

        $penempatan->refresh();
        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $penempatan->status);

        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->status);
    }

    public function test_pembatalan_penempatan_setelah_mulai_tidak_mengembalikan_kuota(): void
    {
        $kuota = $this->getOrCreateKuota(10, 3);

        $ketua = $this->createPeserta('ketua.hangus@test.com', 'Ketua Kuota Hangus');
        $penempatan = Penempatan::create([
            'peserta_id' => $ketua->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-10-01', // di masa lalu terhadap 2026-10-08
            'tgl_selesai' => '2026-12-31',
        ]);

        $cancelAction = app(CancelPenempatanAction::class);
        $cancelAction->execute($penempatan, $this->admin, 'Pengunduran diri setelah mulai');

        // Kuota tetap 3 (hangus)
        $kuota->refresh();
        $this->assertEquals(3, $kuota->terisi);

        // Akun peserta dinonaktifkan
        $ketua->refresh();
        $this->assertFalse($ketua->status_aktif);
    }

    public function test_pembatalan_tidak_dapat_dieksekusi_dua_kali_pada_penempatan_yang_sudah_batal(): void
    {
        $kuota = $this->getOrCreateKuota(10, 1);

        $peserta = $this->createPeserta('double.cancel@test.com', 'Peserta Double Cancel');
        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
        ]);

        $cancelAction = app(CancelPenempatanAction::class);
        $cancelAction->execute($penempatan, $this->admin, 'Batal pertama');

        $kuota->refresh();
        $this->assertEquals(0, $kuota->terisi);

        // Eksekusi kedua harus melempar \RuntimeException dan tidak mengurangi kuota menjadi negatif
        $this->expectException(\RuntimeException::class);
        $cancelAction->execute($penempatan, $this->admin, 'Batal kedua');
    }

    public function test_pelepasan_kuota_dengan_jumlah_nol_atau_negatif_melempar_invalid_argument_exception(): void
    {
        $kuota = $this->getOrCreateKuota(10, 2);
        $service = app(QuotaAllocationService::class);

        $this->expectException(\InvalidArgumentException::class);

        DB::transaction(function () use ($service, $kuota) {
            $service->lepasKuotaUmum($kuota->id, 0);
        });
    }

    public function test_pelepasan_kuota_melebihi_terisi_mencatat_log_warning_dan_menjaga_kuota_tidak_negatif(): void
    {
        $kuota = $this->getOrCreateKuota(10, 1);
        $service = app(QuotaAllocationService::class);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) use ($kuota) {
                return str_contains($message, (string) $kuota->id) && str_contains($message, 'Inkonsistensi akuntansi kuota');
            });

        DB::transaction(function () use ($service, $kuota) {
            $service->lepasKuotaUmum($kuota->id, 3);
        });

        $kuota->refresh();
        $this->assertEquals(0, $kuota->terisi);
    }
}
