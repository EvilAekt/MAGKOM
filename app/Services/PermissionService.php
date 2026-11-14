<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Definisi master 13 modul sistem definitif MagKom (PRD v1.2).
     *
     * @return array<int, array{nama_modul: string, slug: string, kategori: string, deskripsi: string, roles: array<int, string>, urutan: int}>
     */
    public function getDefaultSystemModules(): array
    {
        return [
            [
                'nama_modul' => 'Portal Publik & Registrasi Calon Peserta',
                'slug' => 'publik.portal-registrasi',
                'kategori' => 'Publik',
                'deskripsi' => 'Akses informasi formasi, alur pendaftaran, dan registrasi akun calon peserta magang.',
                'roles' => ['superadmin', 'admin', 'pembimbing', 'peserta'],
                'urutan' => 1,
            ],
            [
                'nama_modul' => 'Pemberkasan & Pendaftaran Magang (Mandiri & Kelompok)',
                'slug' => 'pendaftaran.magang',
                'kategori' => 'Pendaftaran',
                'deskripsi' => 'Pengunggahan berkas persyaratan, pemilihan formasi bulan magang, dan pengajuan magang mandiri atau rombongan kelompok.',
                'roles' => ['superadmin', 'peserta'],
                'urutan' => 2,
            ],
            [
                'nama_modul' => 'Verifikasi Berkas & Seleksi Administratif',
                'slug' => 'seleksi.verifikasi-berkas',
                'kategori' => 'Seleksi',
                'deskripsi' => 'Pemeriksaan validitas berkas, keputusan penerimaan/penolakan, penerbitan surat konfirmasi, dan antrean waiting list.',
                'roles' => ['superadmin', 'admin'],
                'urutan' => 3,
            ],
            [
                'nama_modul' => 'Manajemen Kuota Bulanan & Bidang Dinas',
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
                'deskripsi' => 'Penugasan peserta yang lolos seleksi ke unit kerja bidang dan pembimbing lapangan ASN terkait.',
                'roles' => ['superadmin', 'admin'],
                'urutan' => 5,
            ],
            [
                'nama_modul' => 'Logbook Mingguan & Manajemen Penugasan Proyek',
                'slug' => 'bimbingan.logbook-tugas',
                'kategori' => 'Bimbingan',
                'deskripsi' => 'Pengisian dan verifikasi logbook kegiatan mingguan, penugasan proyek bimbingan, serta pemantauan agenda bimbingan.',
                'roles' => ['superadmin', 'pembimbing', 'peserta'],
                'urutan' => 6,
            ],
            [
                'nama_modul' => 'Rekapitulasi Portofolio Bimbingan & Laporan Kinerja',
                'slug' => 'laporan.kinerja-portofolio',
                'kategori' => 'Pelaporan',
                'deskripsi' => 'Rekapitulasi capaian tugas proyek bimbingan, portofolio mentee, serta ekspor laporan kinerja mentoring ASN.',
                'roles' => ['superadmin', 'admin', 'pembimbing'],
                'urutan' => 7,
            ],
            [
                'nama_modul' => 'Manajemen Staf Internal & Akun Pegawai',
                'slug' => 'staf.manajemen',
                'kategori' => 'Tata Kelola',
                'deskripsi' => 'Pengelolaan akun Admin Dinas dan Pembimbing Lapangan serta reset kredensial akun pegawai.',
                'roles' => ['superadmin'],
                'urutan' => 8,
            ],
            [
                'nama_modul' => 'Slot Khusus Kemitraan & Override Kuota',
                'slug' => 'kuota.slot-khusus',
                'kategori' => 'Kuota',
                'deskripsi' => 'Alokasi kuota jalur kemitraan kampus/SMK dan diskresi pendaftaran peserta jalur khusus dinas.',
                'roles' => ['superadmin'],
                'urutan' => 9,
            ],
            [
                'nama_modul' => 'Konfigurasi Kebijakan & Parameter Sistem',
                'slug' => 'konfigurasi.parameter',
                'kategori' => 'Tata Kelola',
                'deskripsi' => 'Pengaturan parameter operasional aplikasi, kuota bulanan, rasio pembimbing, SLA verifikasi, dan retensi audit.',
                'roles' => ['superadmin'],
                'urutan' => 10,
            ],
            [
                'nama_modul' => 'Pusat Cadangan & Pemulihan Basis Data',
                'slug' => 'sistem.cadangan-database',
                'kategori' => 'Tata Kelola',
                'deskripsi' => 'Pembuatan snapshot basis data terisolasi, kompresi Gzip on-the-fly, verifikasi checksum forensik SHA-256, dan pemulihan data.',
                'roles' => ['superadmin'],
                'urutan' => 11,
            ],
            [
                'nama_modul' => 'Telemetri Kesehatan & Diagnostik Sistem',
                'slug' => 'sistem.telemetri-kesehatan',
                'kategori' => 'Keamanan',
                'deskripsi' => 'Pemantauan latensi real-time HTTP & SSE stream, utilisasi storage, status database MySQL, dan runtime environment PHP 8.3.',
                'roles' => ['superadmin'],
                'urutan' => 12,
            ],
            [
                'nama_modul' => 'Jejak Audit Trail & Log Keamanan Sistem',
                'slug' => 'keamanan.audit-trail',
                'kategori' => 'Keamanan',
                'deskripsi' => 'Pemantauan rekaman audit trail (append-only) seluruh aktivitas mutasi data dan sesi otentikasi.',
                'roles' => ['superadmin'],
                'urutan' => 13,
            ],
        ];
    }

    /**
     * Memastikan ke-13 modul sistem bawaan selalu terdaftar di database.
     * Bersifat idempotent: Hanya menambah yang belum ada, TIDAK merusak konfigurasi wewenang yang sudah diubah.
     */
    public function ensureSystemPermissionsExist(): void
    {
        $systemCount = Permission::where('is_system', true)->count();
        if ($systemCount >= 13) {
            return;
        }

        $this->provisionDefaultModules(overrideRoles: false);
    }

    /**
     * Provisioning modul bawaan sistem secara transaksional dan idempotent.
     *
     * @param  bool  $overrideRoles  Jika true, seluruh role wewenang pada modul sistem direset ke default PRD.
     */
    public function provisionDefaultModules(bool $overrideRoles = false): void
    {
        $modules = $this->getDefaultSystemModules();

        DB::transaction(function () use ($modules, $overrideRoles): void {
            // 1. Bersihkan modul zombie / usang jika ada
            $obsoleteSlugs = [
                'penilaian.kinerja-sertifikat',
            ];
            $obsoletePermissions = Permission::whereIn('slug', $obsoleteSlugs)->get();
            foreach ($obsoletePermissions as $obsolete) {
                RolePermission::where('permission_id', $obsolete->id)->delete();
                $obsolete->delete();
            }

            // 2. Transisi pendaftaran.mandiri -> pendaftaran.magang jika ada
            $mandiriPerm = Permission::where('slug', 'pendaftaran.mandiri')->first();
            if ($mandiriPerm) {
                $mandiriPerm->update(['slug' => 'pendaftaran.magang']);
            }

            // 3. Provisioning 13 modul sistem
            foreach ($modules as $item) {
                /** @var Permission $permission */
                $permission = Permission::firstOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'nama_modul' => $item['nama_modul'],
                        'kategori' => $item['kategori'],
                        'deskripsi' => $item['deskripsi'],
                        'is_system' => true,
                        'urutan' => $item['urutan'],
                    ]
                );

                // Pastikan flag is_system tetap true untuk modul sistem
                if (! $permission->is_system) {
                    $permission->update(['is_system' => true]);
                }

                if ($overrideRoles) {
                    // Reset wewenang ke konfigurasi bawaan PRD
                    RolePermission::where('permission_id', $permission->id)->delete();
                    foreach ($item['roles'] as $role) {
                        RolePermission::create([
                            'role' => $role,
                            'permission_id' => $permission->id,
                        ]);
                    }
                } else {
                    // Hanya tambahkan role mapping jika modul ini belum memiliki wewenang sama sekali
                    if ($permission->rolePermissions()->count() === 0) {
                        foreach ($item['roles'] as $role) {
                            RolePermission::firstOrCreate([
                                'role' => $role,
                                'permission_id' => $permission->id,
                            ]);
                        }
                    }
                }
            }

            $this->clearPermissionsCache();
        });
    }

    /**
     * Membersihkan cache hak akses untuk seluruh 4 role.
     */
    public function clearPermissionsCache(): void
    {
        $roles = [
            RoleEnum::SuperAdmin->value,
            RoleEnum::Admin->value,
            RoleEnum::Pembimbing->value,
            RoleEnum::Peserta->value,
        ];

        foreach ($roles as $role) {
            Cache::forget("role_perms_{$role}");
        }
    }
}
