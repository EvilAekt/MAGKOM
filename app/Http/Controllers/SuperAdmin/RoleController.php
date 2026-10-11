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

        // Matriks hak akses 4 role sistem sesuai PRD Bab 3.1 & 3.3
        $matriksAkses = [
            [
                'modul' => 'Portal Publik & Registrasi Calon Peserta',
                'deskripsi' => 'Akses informasi lowongan, jadwal pendaftaran, dan pembuatan akun baru',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => true,
                'peserta' => true,
            ],
            [
                'modul' => 'Pemberkasan & Pendaftaran Magang Mandiri',
                'deskripsi' => 'Pengisian formulir pendaftaran, unggah CV/transkrip, dan surat pengantar',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => true,
            ],
            [
                'modul' => 'Verifikasi Berkas & Seleksi Administratif',
                'deskripsi' => 'Pemeriksaan validitas berkas, keputusan terima, tolak, revisi, atau antrean',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Manajemen Kuota Bulanan & Jurusan',
                'deskripsi' => 'Pengelolaan alokasi kursi per bidang/jurusan dan penyesuaian dinamis',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Penetapan Penempatan & Pembimbing Lapangan',
                'deskripsi' => 'Alokasi peserta lolos ke bidang dinas dan penetapan mentor pembimbing',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Logbook Harian & Pengelolaan Tugas Proyek',
                'deskripsi' => 'Pengisian aktivitas kerja harian peserta dan pemberian tugas oleh mentor',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => true,
                'peserta' => true,
            ],
            [
                'modul' => 'Penilaian Kinerja & Pengesahan Sertifikat',
                'deskripsi' => 'Evaluasi nilai berdasarkan rubrik resmi, persetujuan admin, & penerbitan sertifikat',
                'superadmin' => true,
                'admin' => true,
                'pembimbing' => true,
                'peserta' => false,
            ],
            [
                'modul' => 'Manajemen Staf Internal & Akun Pegawai',
                'deskripsi' => 'Pembuatan akun admin/pembimbing, reset kata sandi, dan status aktif',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Slot Khusus Kemitraan & Override Kuota',
                'deskripsi' => 'Penguncian kursi jalur kemitraan kampus mitra tanpa mengurangi kuota umum',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Konfigurasi Parameter Sistem & Rubrik Bobot',
                'deskripsi' => 'Pengaturan batas beban pembimbing (BR-13), batas nilai kelulusan, dan master data',
                'superadmin' => true,
                'admin' => false,
                'pembimbing' => false,
                'peserta' => false,
            ],
            [
                'modul' => 'Jejak Audit Trail & Log Keamanan Sistem',
                'deskripsi' => 'Pemantauan rekaman mutasi append-only, filter multi-kolom, & ekspor CSV compliance',
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
