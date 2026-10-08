<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengaturanList = [
            ['kunci' => 'masa_tenggang_bulan', 'nilai' => '1'],
            ['kunci' => 'kuota_umum_maks_per_bulan', 'nilai' => '12'],
            ['kunci' => 'slot_khusus_default', 'nilai' => '3'],
            ['kunci' => 'maks_peserta_aktif_per_pembimbing', 'nilai' => null],
            ['kunci' => 'logbook_maks_mundur_hari', 'nilai' => '3'],
            ['kunci' => 'berkas_maks_mb', 'nilai' => '2'],
            ['kunci' => 'deadline_mendekati_hari', 'nilai' => '3'],
            ['kunci' => 'predikat_batas', 'nilai' => json_encode(['A' => 85, 'B' => 70, 'C' => 55, 'D' => 40])],
        ];

        foreach ($pengaturanList as $pengaturan) {
            Pengaturan::updateOrCreate(
                ['kunci' => $pengaturan['kunci']],
                $pengaturan
            );
        }
    }
}
