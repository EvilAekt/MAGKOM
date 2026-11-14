<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\PermissionService;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PermissionService $permissionService): void
    {
        $permissionService->provisionDefaultModules(overrideRoles: false);
    }
}
