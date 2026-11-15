<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Password awal diambil dari environment, BUKAN hardcoded (AC-11-8, D-07, BR-15).
     */
    public function run(): void
    {
        $nama = env('SEED_SUPERADMIN_NAME', 'Super Administrator');
        $email = env('SEED_SUPERADMIN_EMAIL', 'superadmin@magkom.go.id');
        $isProduction = app()->isProduction();

        if ($isProduction) {
            $password = env('SEED_SUPERADMIN_PASSWORD');
            if (empty($password)) {
                throw new RuntimeException(
                    'SEED_SUPERADMIN_PASSWORD must be explicitly provided in production environment.'
                );
            }
        } else {
            $password = env('SEED_SUPERADMIN_PASSWORD') ?: 'SuperAdminMagKom123!';
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($isProduction) {
                // In production, preserve existing SuperAdmin credentials
                return;
            }

            $existingUser->nama = $nama;
            $existingUser->password = Hash::make($password);
            $existingUser->role = RoleEnum::SuperAdmin;
            $existingUser->email_verified_at = now();
            $existingUser->status_aktif = true;
            $existingUser->must_change_password = true;
            $existingUser->save();

            return;
        }

        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role = RoleEnum::SuperAdmin;
        $user->email_verified_at = now();
        $user->status_aktif = true;
        $user->must_change_password = true; // Wajib ganti password saat login pertama (BR-15)
        $user->save();
    }
}
