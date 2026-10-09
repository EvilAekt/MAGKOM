<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            JurusanSeeder::class,
            RubrikSeeder::class,
            PengaturanSeeder::class,
            SuperAdminSeeder::class,
            PeriodeKuotaSeeder::class,
            JenisDokumenSeeder::class,
            TestingUserSeeder::class,
        ]);
    }
}
