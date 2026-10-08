<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\SlotKhusus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class PeriodeKuotaSeeder extends Seeder
{
    /**
     * Seed periode bulan magang dan alokasi kuota contoh.
     */
    public function run(): void
    {
        $superAdmin = User::where('role', RoleEnum::SuperAdmin)->first();

        // 1. Bulan Magang November 2026 (Status: Tersedia pada saat Oktober 2026)
        $bulanNov = BulanMagang::firstOrCreate(
            ['bulan_mulai' => '2026-11-01'],
            [
                'tgl_buka' => '2026-10-01',
                'tgl_tutup' => '2026-10-25',
                'override_status' => null,
                'aktif' => true,
            ]
        );

        // 2. Bulan Magang Desember 2026 (Status: Segera pada saat Oktober 2026)
        $bulanDes = BulanMagang::firstOrCreate(
            ['bulan_mulai' => '2026-12-01'],
            [
                'tgl_buka' => '2026-11-01',
                'tgl_tutup' => '2026-11-25',
                'override_status' => null,
                'aktif' => true,
            ]
        );

        // 3. Kuota per jurusan untuk November 2026
        $jurusanList = Jurusan::all();

        foreach ($jurusanList as $jurusan) {
            Kuota::firstOrCreate(
                [
                    'bulan_magang_id' => $bulanNov->id,
                    'jurusan_id' => $jurusan->id,
                ],
                [
                    'jumlah' => 5,
                    'terisi' => str_contains($jurusan->nama_jurusan, 'TKJ') ? 5 : 2, // TKJ dibuat penuh untuk menguji kondisi Waiting List
                    'aktif' => true,
                ]
            );

            // Kuota untuk Desember 2026
            Kuota::firstOrCreate(
                [
                    'bulan_magang_id' => $bulanDes->id,
                    'jurusan_id' => $jurusan->id,
                ],
                [
                    'jumlah' => 4,
                    'terisi' => 0,
                    'aktif' => true,
                ]
            );
        }

        // 4. Slot khusus internal SuperAdmin untuk November 2026
        if ($superAdmin) {
            SlotKhusus::firstOrCreate(
                ['bulan_mulai' => '2026-11-01'],
                [
                    'jumlah' => 3,
                    'terisi' => 1,
                    'aktif' => true,
                    'dibuat_oleh' => $superAdmin->id,
                ]
            );
        }
    }
}
