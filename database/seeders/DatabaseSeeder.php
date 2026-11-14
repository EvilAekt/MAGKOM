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
            BidangSeeder::class,
            JurusanSeeder::class,
            BidangJurusanSeeder::class,
            PengaturanSeeder::class,
            SuperAdminSeeder::class,
            PermissionSeeder::class,
            PeriodeKuotaSeeder::class,
            JenisDokumenSeeder::class,
            UserSeeder::class,
            TestingUserSeeder::class,
        ]);
    }
}
