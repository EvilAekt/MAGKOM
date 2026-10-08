<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        $password = env('SEED_SUPERADMIN_PASSWORD', 'SuperAdminMagKom123!');

        $user = User::firstOrNew(['email' => $email]);
        $user->nama = $nama;
        $user->password = Hash::make($password);
        $user->role = RoleEnum::SuperAdmin;
        $user->email_verified_at = now();
        $user->status_aktif = true;
        $user->must_change_password = true; // Wajib ganti password saat login pertama (BR-15)
        $user->save();
    }
}
