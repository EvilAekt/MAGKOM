<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestingUserSeeder extends Seeder
{
    /**
     * Seed akun testing untuk seluruh peran pengguna (Peserta, Pembimbing, Admin, SuperAdmin).
     * Seluruh akun diset siap pakai tanpa paksaan ganti password (must_change_password = false).
     */
    public function run(): void
    {
        $accounts = [
            [
                'nama' => 'Super Administrator',
                'email' => 'superadmin@magkom.go.id',
                'password' => 'Password123!',
                'role' => RoleEnum::SuperAdmin,
            ],
            [
                'nama' => 'Admin Magang Sleman',
                'email' => 'admin@magkom.go.id',
                'password' => 'Password123!',
                'role' => RoleEnum::Admin,
            ],
            [
                'nama' => 'Pembimbing Lapangan',
                'email' => 'pembimbing@magkom.go.id',
                'password' => 'Password123!',
                'role' => RoleEnum::Pembimbing,
            ],
            [
                'nama' => 'Peserta Magang',
                'email' => 'peserta@magkom.go.id',
                'password' => 'Password123!',
                'role' => RoleEnum::Peserta,
            ],
        ];

        foreach ($accounts as $acc) {
            $user = User::firstOrNew(['email' => $acc['email']]);
            $user->nama = $acc['nama'];
            $user->password = Hash::make($acc['password']);
            $user->role = $acc['role'];
            $user->email_verified_at = now();
            $user->status_aktif = true;
            $user->must_change_password = false;
            $user->save();
        }
    }
}
