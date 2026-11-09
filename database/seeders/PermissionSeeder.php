<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulBawaan = [
            [
                'nama_modul' => 'Portal Publik & Registrasi Calon Peserta',
                'slug' => 'publik.portal-registrasi',
                'kategori' => 'Publik',
                'deskripsi' => 'Akses informasi formasi, alur pendaftaran, dan registrasi akun calon peserta magang.',
                'roles' => ['superadmin', 'admin', 'pembimbing', 'peserta'],
                'urutan' => 1,
            ],
            [
                'nama_modul' => 'Pemberkasan & Pendaftaran Magang Mandiri',
                'slug' => 'pendaftaran.mandiri',
                'kategori' => 'Pendaftaran',
                'deskripsi' => 'Pengunggahan berkas persyaratan, pemilihan formasi bulan magang, dan pengajuan berkas pendaftaran.',
                'roles' => ['superadmin', 'peserta'],
                'urutan' => 2,
            ],
            [
                'nama_modul' => 'Verifikasi Berkas & Seleksi Administratif',
                'slug' => 'seleksi.verifikasi-berkas',
                'kategori' => 'Seleksi',
                'deskripsi' => 'Pemeriksaan validitas berkas, persetujuan/penolakan pendaftaran, dan verifikasi administrasi.',
                'roles' => ['superadmin', 'admin'],
                'urutan' => 3,
            ],
            [
                'nama_modul' => 'Manajemen Kuota Bulanan & Jurusan',
                'slug' => 'kuota.manajemen',
                'kategori' => 'Kuota',
                'deskripsi' => 'Pengaturan batas kuota umum, alokasi per bidang dinas, serta status buka/tutup bulan magang.',
                'roles' => ['superadmin', 'admin'],
                'urutan' => 4,
            ],
            [
                'nama_modul' => 'Penetapan Penempatan & Pembimbing Lapangan',
                'slug' => 'penempatan.pembimbing',
                'kategori' => 'Penempatan',
                'deskripsi' => 'Penugasan peserta yang lolos seleksi ke bidang dan pembimbing lapangan terkait.',
                'roles' => ['superadmin', 'admin'],
                'urutan' => 5,
            ],
            [
                'nama_modul' => 'Logbook Harian & Pengelolaan Tugas Proyek',
                'slug' => 'bimbingan.logbook-tugas',
                'kategori' => 'Bimbingan',
                'deskripsi' => 'Pengisian dan review logbook kegiatan harian serta penugasan modul proyek bimbingan.',
                'roles' => ['superadmin', 'pembimbing', 'peserta'],
                'urutan' => 6,
            ],
            [
                'nama_modul' => 'Penilaian Kinerja & Pengesahan Sertifikat',
                'slug' => 'penilaian.kinerja-sertifikat',
                'kategori' => 'Penilaian',
                'deskripsi' => 'Pemberian nilai rubrik bimbingan, rekapitulasi evaluasi akhir, dan pengesahan kelulusan.',
                'roles' => ['superadmin', 'admin', 'pembimbing'],
                'urutan' => 7,
            ],
            [
                'nama_modul' => 'Manajemen Staf Internal & Akun Pegawai',
                'slug' => 'staf.manajemen',
                'kategori' => 'Tata Kelola',
                'deskripsi' => 'Pengelolaan akun Admin Dinas dan Pembimbing Lapangan serta reset kredensial.',
                'roles' => ['superadmin'],
                'urutan' => 8,
            ],
            [
                'nama_modul' => 'Slot Khusus Kemitraan & Override Kuota',
                'slug' => 'kuota.slot-khusus',
                'kategori' => 'Kuota',
                'deskripsi' => 'Alokasi kuota jalur kemitraan kampus/sekolah dan diskresi kuota kepesertaan khusus.',
                'roles' => ['superadmin'],
                'urutan' => 9,
            ],
            [
                'nama_modul' => 'Konfigurasi Parameter Sistem & Rubrik Bobot',
                'slug' => 'konfigurasi.parameter',
                'kategori' => 'Tata Kelola',
                'deskripsi' => 'Pengaturan parameter global aplikasi, bobot nilai rubrik, dan batas waktu pengisian.',
                'roles' => ['superadmin'],
                'urutan' => 10,
            ],
            [
                'nama_modul' => 'Jejak Audit Trail & Log Keamanan Sistem',
                'slug' => 'keamanan.audit-trail',
                'kategori' => 'Keamanan',
                'deskripsi' => 'Pemantauan rekaman audit trail (append-only) seluruh aktivitas mutasi data dan sesi otentikasi.',
                'roles' => ['superadmin'],
                'urutan' => 11,
            ],
        ];

        DB::transaction(function () use ($modulBawaan) {
            foreach ($modulBawaan as $item) {
                /** @var Permission $permission */
                $permission = Permission::updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'nama_modul' => $item['nama_modul'],
                        'kategori' => $item['kategori'],
                        'deskripsi' => $item['deskripsi'],
                        'is_system' => true,
                        'urutan' => $item['urutan'],
                    ]
                );

                // Sinkronkan relasi role
                foreach ($item['roles'] as $role) {
                    RolePermission::firstOrCreate([
                        'role' => $role,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        });
    }
}
