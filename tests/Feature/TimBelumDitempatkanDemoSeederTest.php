<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\Pendaftaran;
use App\Models\User;
use Database\Seeders\BidangJurusanSeeder;
use Database\Seeders\BidangSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\PeriodeKuotaSeeder;
use Database\Seeders\TimBelumDitempatkanDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimBelumDitempatkanDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_mengisi_antrean_tim_belum_ditempatkan_secara_idempoten_dan_tampil_di_admin(): void
    {
        $this->seed([
            BidangSeeder::class,
            JurusanSeeder::class,
            BidangJurusanSeeder::class,
            PeriodeKuotaSeeder::class,
            JenisDokumenSeeder::class,
        ]);

        // Jalankan 2 kali untuk memastikan idempoten (tanpa duplikasi)
        $this->seed(TimBelumDitempatkanDemoSeeder::class);
        $this->seed(TimBelumDitempatkanDemoSeeder::class);

        $antreanTim = Pendaftaran::with('kelompokMagang.anggota')
            ->where('tipe', 'kelompok')
            ->where('status', StatusPendaftaranEnum::Diterima)
            ->doesntHave('penempatan')
            ->get();

        $this->assertCount(3, $antreanTim);

        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        // Verifikasi tampil di halaman Admin Penempatan (Tab Tim -> Belum Ditempatkan)
        $indexRes = $this->actingAs($admin)->get(route('admin.penempatan.index', [
            'tipe' => 'kelompok',
            'tab' => 'belum',
        ]));

        $indexRes->assertOk();
        $indexRes->assertSee('Tim Integrasi Data SPBE UGM');
        $indexRes->assertSee('Tim Monitoring Infrastruktur Jaringan SMKN 2 Depok');
        $indexRes->assertSee('Tim Produksi Konten &amp; Publikasi Digital UNY', false);

        // Verifikasi halaman Detail antrean tiap tim dapat dibuka beserta biodata & pembimbing bidangnya
        $firstTim = $antreanTim->first();
        $this->assertNotNull($firstTim);
        $indexRes->assertSee(route('admin.penempatan.antrean.show', $firstTim->id), false);

        $detailRes = $this->actingAs($admin)->get(route('admin.penempatan.antrean.show', $firstTim));
        $detailRes->assertOk();
        $detailRes->assertSee($firstTim->user->nama);
        $detailRes->assertSee($firstTim->kelompokMagang->nama_kelompok);
        $detailRes->assertSee('Anggota Tim');
        $detailRes->assertSee(route('admin.penempatan.tetapkan'), false);

        // Pastikan seluruh anggota tim demo juga memiliki akun User aktif & profil biodata yang tampil di detail antrean
        foreach ($firstTim->kelompokMagang->anggota as $anggota) {
            $this->assertNotNull($anggota->user_id);
            $this->assertNotNull($anggota->user);
            $this->assertTrue((bool) $anggota->user->status_aktif);
            $detailRes->assertSee($anggota->nama);
            $detailRes->assertSee($anggota->email);
        }
    }
}
