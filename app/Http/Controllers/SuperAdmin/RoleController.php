<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar role dan matriks hak akses sistem (SA-02 Role, PRD Bab 3.1 & 3.3).
     */
    public function index(Request $request): View
    {
        // Hitung pengguna per role
        $roleCounts = [
            RoleEnum::SuperAdmin->value => User::where('role', RoleEnum::SuperAdmin)->count(),
            RoleEnum::Admin->value => User::where('role', RoleEnum::Admin)->count(),
            RoleEnum::Pembimbing->value => User::where('role', RoleEnum::Pembimbing)->count(),
            RoleEnum::Peserta->value => User::where('role', RoleEnum::Peserta)->count(),
        ];

        // Matriks hak akses 4 role sistem
        $matriksAkses = [
            [
                'modul' => 'Portal Publik & Registrasi Calon Peserta',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => true,
                'peserta' => true,
            ],
            [
                'modul' => 'Pemberkasan & Pendaftaran Magang Mandiri',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => true,
            ],
            [
                'modul' => 'Verifikasi Berkas & Seleksi Administratif',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Manajemen Kuota Bulanan & Jurusan',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Penetapan Penempatan & Pembimbing Lapangan',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Logbook Harian & Pengelolaan Tugas Proyek',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => true,
                'peserta' => true,
            ],
            [
                'modul' => 'Penilaian Kinerja & Pengesahan Sertifikat',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => true,
                'peserta' => false,
            ],
            [
                'modul' => 'Manajemen Staf Internal & Akun Pegawai',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Slot Khusus Kemitraan & Override Kuota',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Konfigurasi Parameter Sistem & Rubrik Bobot',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Jejak Audit Trail & Log Keamanan Sistem',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
        ];

        return view('superadmin.role.index', [
            'roleCounts' => $roleCounts,
            'matriksAkses' => $matriksAkses,
        ]);
    }
}
