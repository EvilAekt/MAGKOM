<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
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
            [
                'nama' => 'Pendaftar Calon Peserta',
                'email' => 'pendaftar@magkom.go.id',
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

            if ($user->role === RoleEnum::Peserta && ! $user->profil) {
                $jurusanSmk = Jurusan::where('kategori', 'smk')->first();
                PesertaProfil::create([
                    'user_id' => $user->id,
                    'nim_nisn' => $user->email === 'pendaftar@magkom.go.id' ? '20242002' : '20241001',
                    'institusi' => $user->email === 'pendaftar@magkom.go.id' ? 'Universitas Gadjah Mada' : 'SMK Negeri 1 Sleman',
                    'jurusan_id' => $jurusanSmk?->id,
                    'jenjang' => $user->email === 'pendaftar@magkom.go.id' ? JenjangEnum::S1 : JenjangEnum::Smk,
                    'no_hp' => '081234567890',
                ]);
            }

            // Pendaftar baru yang belum diverifikasi oleh admin
            if ($user->email === 'pendaftar@magkom.go.id') {
                $bulanMagang = BulanMagang::first();
                $kuota = Kuota::where('jurusan_id', $user->profil?->jurusan_id)->first() ?? Kuota::first();

                if ($bulanMagang && $kuota) {
                    $pendaftaran = Pendaftaran::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'bulan_magang_id' => $bulanMagang->id,
                        ],
                        [
                            'kuota_id' => $kuota->id,
                            'durasi_bulan' => 3,
                            'status' => StatusPendaftaranEnum::Diajukan,
                            'diajukan_at' => now()->subDay(),
                        ]
                    );

                    $jenisDokumenList = JenisDokumen::where('aktif', true)->get();
                    foreach ($jenisDokumenList as $jd) {
                        Dokumen::firstOrCreate(
                            [
                                'pendaftaran_id' => $pendaftaran->id,
                                'jenis_dokumen_id' => $jd->id,
                            ],
                            [
                                'path' => 'dokumen/pendaftaran_'.$jd->kode.'.pdf',
                                'nama_asli' => 'Dokumen_'.$jd->nama.'.pdf',
                                'mime' => 'application/pdf',
                                'ukuran' => 153600,
                                'hash' => md5($jd->kode.'_'.$pendaftaran->id),
                            ]
                        );
                    }
                }
            }
        }
    }
}
