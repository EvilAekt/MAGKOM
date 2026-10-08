<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Rubrik;
use Illuminate\Database\Seeder;

class RubrikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rubrikList = [
            ['kriteria' => 'Kualitas hasil kerja', 'bobot' => 30, 'aktif' => true],
            ['kriteria' => 'Ketepatan waktu dan penyelesaian tugas', 'bobot' => 20, 'aktif' => true],
            ['kriteria' => 'Inisiatif dan kemampuan belajar', 'bobot' => 20, 'aktif' => true],
            ['kriteria' => 'Komunikasi dan kerja sama', 'bobot' => 15, 'aktif' => true],
            ['kriteria' => 'Etika, disiplin, dan tanggung jawab', 'bobot' => 15, 'aktif' => true],
        ];

        foreach ($rubrikList as $rubrik) {
            Rubrik::updateOrCreate(
                ['kriteria' => $rubrik['kriteria']],
                $rubrik
            );
        }
    }
}
