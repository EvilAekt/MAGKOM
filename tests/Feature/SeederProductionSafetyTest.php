<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\AcceptedPesertaSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DummyDuaPuluhPesertaSeeder;
use Database\Seeders\ProductionRealisticDataSeeder;
use Database\Seeders\PurgeAndSeedCleanGmailAccountsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\TestingUserSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class SeederProductionSafetyTest extends TestCase
{
    use RefreshDatabase;

    private string $originalEnv;

    private ?string $originalSuperAdminPassword;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnv = (string) $this->app['env'];
        $this->originalSuperAdminPassword = $_ENV['SEED_SUPERADMIN_PASSWORD']
            ?? $_SERVER['SEED_SUPERADMIN_PASSWORD']
            ?? (getenv('SEED_SUPERADMIN_PASSWORD') !== false ? (string) getenv('SEED_SUPERADMIN_PASSWORD') : null);
    }

    protected function tearDown(): void
    {
        $this->app['env'] = $this->originalEnv;
        config(['app.env' => $this->originalEnv]);

        $this->setSuperAdminPasswordEnv($this->originalSuperAdminPassword);

        parent::tearDown();
    }

    private function setSuperAdminPasswordEnv(?string $password): void
    {
        if ($password !== null && $password !== '') {
            $_ENV['SEED_SUPERADMIN_PASSWORD'] = $password;
            $_SERVER['SEED_SUPERADMIN_PASSWORD'] = $password;
            putenv("SEED_SUPERADMIN_PASSWORD={$password}");
        } else {
            unset($_ENV['SEED_SUPERADMIN_PASSWORD'], $_SERVER['SEED_SUPERADMIN_PASSWORD']);
            putenv('SEED_SUPERADMIN_PASSWORD');
        }
    }

    public function test_production_database_seeder_creates_only_master_data_and_single_superadmin(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        $this->setSuperAdminPasswordEnv('ProductionSuperSecret123!#');

        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true]);

        $this->assertSame(1, User::count());

        $superAdmin = User::where('email', 'superadmin@magkom.go.id')->first();
        $this->assertNotNull($superAdmin);
        $this->assertSame(RoleEnum::SuperAdmin, $superAdmin->role);
        $this->assertTrue($superAdmin->status_aktif);
        $this->assertTrue((bool) $superAdmin->must_change_password);
        $this->assertTrue(Hash::check('ProductionSuperSecret123!#', $superAdmin->password));
        $this->assertFalse(Hash::check('Password123!', $superAdmin->password));
        $this->assertFalse(Hash::check('SuperAdminMagKom123!', $superAdmin->password));

        $this->assertNull(User::where('email', 'admin@magkom.go.id')->first());
        $this->assertNull(User::where('email', 'pembimbing@magkom.go.id')->first());
        $this->assertNull(User::where('email', 'peserta@magkom.go.id')->first());
        $this->assertNull(User::where('email', 'pendaftar@magkom.go.id')->first());
        $this->assertSame(0, User::where('email', 'like', '%@gmail.com')->count());
    }

    public function test_production_superadmin_seeder_does_not_overwrite_existing_credentials(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        $this->setSuperAdminPasswordEnv('InitialProductionPassword123!#');

        $this->artisan('db:seed', ['--class' => SuperAdminSeeder::class, '--force' => true]);

        $superAdmin = User::where('email', 'superadmin@magkom.go.id')->firstOrFail();
        $customPassword = Hash::make('AdminCustomizedPass999!#');
        $superAdmin->password = $customPassword;
        $superAdmin->must_change_password = false;
        $superAdmin->save();

        $this->setSuperAdminPasswordEnv('AttemptedOverwritePassword456!#');
        $this->artisan('db:seed', ['--class' => SuperAdminSeeder::class, '--force' => true]);

        $superAdmin->refresh();
        $this->assertSame($customPassword, $superAdmin->password);
        $this->assertFalse((bool) $superAdmin->must_change_password);
    }

    public function test_production_seeding_fails_if_seed_superadmin_password_is_missing(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        $this->setSuperAdminPasswordEnv(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('SEED_SUPERADMIN_PASSWORD must be explicitly provided in production environment.');

        $this->artisan('db:seed', ['--class' => SuperAdminSeeder::class, '--force' => true]);
    }

    public function test_production_blocks_testing_and_dummy_seeders(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);

        $blockedSeeders = [
            TestingUserSeeder::class,
            UserSeeder::class,
            DummyDuaPuluhPesertaSeeder::class,
            AcceptedPesertaSeeder::class,
            PurgeAndSeedCleanGmailAccountsSeeder::class,
            ProductionRealisticDataSeeder::class,
        ];

        foreach ($blockedSeeders as $seederClass) {
            try {
                (new $seederClass)->run();
                $this->fail("Expected RuntimeException was not thrown for {$seederClass} in production environment.");
            } catch (RuntimeException $e) {
                $this->assertStringContainsString('Cannot run', $e->getMessage());
                $this->assertStringContainsString('in production environment', $e->getMessage());
            }
        }
    }

    public function test_testing_environment_allows_test_seeders(): void
    {
        $this->app['env'] = 'testing';
        config(['app.env' => 'testing']);

        $this->seed(DatabaseSeeder::class);

        $this->assertNotNull(User::where('email', 'superadmin@magkom.go.id')->first());
        $this->assertNotNull(User::where('email', 'admin@magkom.go.id')->first());
        $this->assertNotNull(User::where('email', 'pembimbing@magkom.go.id')->first());
        $this->assertNotNull(User::where('email', 'peserta@magkom.go.id')->first());
    }
}
