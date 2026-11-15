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
        ]);

        // SEC-001: Demo accounts tidak di-seed otomatis di local dev kecuali ada opt-in eksplisit (SEED_DEMO_ACCOUNTS=true)
        if (app()->environment('testing') || (app()->isLocal() && (bool) env('SEED_DEMO_ACCOUNTS', false))) {
            $this->call([
                UserSeeder::class,
                TestingUserSeeder::class,
            ]);
        }
    }
}
