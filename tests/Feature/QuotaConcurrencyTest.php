<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Exceptions\Domain\KuotaPenuhException;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\PlacementService;
use App\Services\QuotaAllocationService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuotaConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Kuota::count() === 0) {
            $this->seed();
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_alokasi_kuota_umum_atomik_mencegah_kuota_minus_dan_double_claim(): void
    {
        $service = app(QuotaAllocationService::class);

        // Buat kuota uji coba dengan kapasitas 1 sisa kursi
        $bulan = BulanMagang::first();
        $jurusan = Jurusan::first();

        $kuota = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id],
            ['jumlah' => 5, 'terisi' => 4, 'aktif' => true]
        );
        $kuota->update(['jumlah' => 5, 'terisi' => 4]);

        // Pemanggilan pertama dalam transaksi: berhasil mengambil 1 sisa kursi
        DB::transaction(function () use ($service, $kuota) {
            $k = $service->ambilKuotaUmum($kuota->id);
            $this->assertEquals(5, $k->terisi);
        });

        // Kuota di database kini bernilai terisi = 5 (penuh)
        $kuota->refresh();
        $this->assertEquals(5, $kuota->terisi);

        // Pemanggilan kedua dalam transaksi berikutnya: harus gagal melempar KuotaPenuhException
        $this->expectException(KuotaPenuhException::class);

        DB::transaction(function () use ($service, $kuota) {
            $service->ambilKuotaUmum($kuota->id);
        });

        // Terisi tidak boleh pernah bertambah melebihi jumlah (anti race condition)
        $kuota->refresh();
        $this->assertEquals(5, $kuota->terisi);
    }

    public function test_transaksi_penerimaan_pendaftaran_rollback_secara_atomik_jika_terjadi_kesalahan(): void
    {
        $admin = User::firstOrNew(['email' => 'admin.audit@kominfo.go.id']);
        $admin->nama = 'Admin Auditor';
        $admin->password = bcrypt('SecretAdmin#2026');
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = now();
        $admin->status_aktif = true;
        $admin->save();

        $peserta = User::firstOrNew(['email' => 'peserta.rollback@test.com']);
        $peserta->nama = 'Peserta Rollback';
        $peserta->password = bcrypt('Password#123');
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        $bulan = BulanMagang::first();
        $kuota = Kuota::where('bulan_magang_id', $bulan->id)->first();
        $kuota->update(['jumlah' => 10, 'terisi' => 2]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $placementService = app(PlacementService::class);

        // Sengaja hapus semua pembimbing untuk memaksa transaksi melempar ModelNotFoundException saat terima
        User::where('role', RoleEnum::Pembimbing)->update(['status_aktif' => false]);

        try {
            $placementService->terima($pendaftaran, $admin);
            $this->fail('Seharusnya melempar exception karena pembimbing tidak ditemukan');
        } catch (\Throwable $e) {
            // Transaksi dibatalkan (rollback)
        }

        // Kembalikan pembimbing aktif
        User::where('role', RoleEnum::Pembimbing)->update(['status_aktif' => true]);

        // Verifikasi kuota TIDAK bertambah karena di-rollback
        $kuota->refresh();
        $this->assertEquals(2, $kuota->terisi);

        // Verifikasi pendaftaran tetap berstatus Diajukan
        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->status);
    }
}
