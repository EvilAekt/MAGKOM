<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed akun demo untuk semua role (SuperAdmin, Admin, Pembimbing, Peserta).
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');
        $bidang = Bidang::first();
        $jurusan = Jurusan::first();

        // 1. Super Admin
        $superAdmin = User::firstOrNew(['email' => 'superadmin@magkom.go.id']);
        $superAdmin->nama = 'Super Administrator';
        $superAdmin->password = $defaultPassword;
        $superAdmin->role = RoleEnum::SuperAdmin;
        $superAdmin->email_verified_at = now();
        $superAdmin->status_aktif = true;
        $superAdmin->must_change_password = false;
        $superAdmin->save();

        // 2. Admin Verifikator
        $admin = User::firstOrNew(['email' => 'admin@magkom.go.id']);
        $admin->nama = 'Admin Verifikator Magang';
        $admin->password = $defaultPassword;
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = now();
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->save();

        // 3. Pembimbing Lapangan
        $pembimbing = User::firstOrNew(['email' => 'pembimbing@magkom.go.id']);
        $pembimbing->nama = 'Budi Pembimbing Lapangan';
        $pembimbing->password = $defaultPassword;
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->bidang_id = $bidang?->id;
        $pembimbing->email_verified_at = now();
        $pembimbing->status_aktif = true;
        $pembimbing->must_change_password = false;
        $pembimbing->save();

        // 4. Peserta Magang
        $peserta = User::firstOrNew(['email' => 'peserta@magkom.go.id']);
        $peserta->nama = 'Ahmad Siswa Magang';
        $peserta->password = $defaultPassword;
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->must_change_password = false;
        $peserta->save();

        // Profil Peserta Lengkap
        if ($jurusan) {
            PesertaProfil::updateOrCreate(
                ['user_id' => $peserta->id],
                [
                    'nim_nisn' => '1029384756',
                    'institusi' => 'SMK Negeri 2 Depok Sleman',
                    'jurusan_id' => $jurusan->id,
                    'jenjang' => JenjangEnum::Smk,
                    'no_hp' => '081234567890',
                    'nama_kontak_darurat' => 'Siti Wali',
                    'no_kontak_darurat' => '089876543210',
                    'hubungan_kontak_darurat' => 'Orang Tua / Wali',
                ]
            );
        }
    }
}
