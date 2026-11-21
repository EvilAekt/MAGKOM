<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AkunDemoLengkapSeeder extends Seeder
{
    /**
     * Jalankan seeder akun demo sesuai spesifikasi:
     * 1 SuperAdmin: superadmin@gmail.com
     * 1 Admin Verifikator: admin@gmail.com
     * 1 Pembimbing Lapangan: pembimbing@gmail.com
     * 1 Peserta Magang Utama: peserta@gmail.com
     * 10 Akun Peserta Tambahan (peserta1@gmail.com s/d peserta10@gmail.com) dengan status acak/bervariasi
     * Password semua akun: Password123!
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException('Cannot run AkunDemoLengkapSeeder in production environment.');
        }

        $defaultPassword = Hash::make('Password123!');
        $now = Carbon::now('Asia/Jakarta');

        // Pastikan direktori penyimpanan ada
        Storage::disk('public')->makeDirectory('foto-staf');
        Storage::disk('public')->makeDirectory('profil-peserta');
        Storage::disk('private')->makeDirectory('dokumen');

        // Buat file PDF dummy valid untuk dokumen jika belum ada
        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n206\n%%EOF";
        if (! Storage::disk('private')->exists('dokumen/sample_berkas.pdf')) {
            Storage::disk('private')->put('dokumen/sample_berkas.pdf', $samplePdfContent);
        }

        DB::transaction(function () use ($defaultPassword, $now) {
            // =========================================================================
            // 1. DATA MASTER DASAR (BIDANG, BULAN, JURUSAN, KUOTA)
            // =========================================================================
            $bidangEgov = Bidang::firstOrCreate(
                ['nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)'],
                ['deskripsi' => 'Pengembangan SPBE dan integrasi aplikasi dinas.', 'aktif' => true]
            );
            $bidangIkp = Bidang::firstOrCreate(
                ['nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)'],
                ['deskripsi' => 'Publikasi multimedia, kehumasan, dan relasi media.', 'aktif' => true]
            );
            $bidangIti = Bidang::firstOrCreate(
                ['nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)'],
                ['deskripsi' => 'Jaringan, server, dan CSIRT Sleman.', 'aktif' => true]
            );
            $bidangSekretariat = Bidang::firstOrCreate(
                ['nama_bidang' => 'Sekretariat Dinas'],
                ['deskripsi' => 'Administrasi umum dan kepegawaian.', 'aktif' => true]
            );

            $bulanAktif = BulanMagang::where('aktif', true)->orderBy('id')->first();
            if (! $bulanAktif) {
                $bulanAktif = BulanMagang::create([
                    'nama_bulan' => $now->translatedFormat('F Y'),
                    'bulan_mulai' => $now->copy()->startOfMonth(),
                    'tgl_buka' => $now->copy()->subMonth()->startOfMonth(),
                    'tgl_tutup' => $now->copy()->subMonth()->endOfMonth(),
                    'aktif' => true,
                ]);
            }

            // Ambil semua jenis dokumen
            $jenisDokumenList = JenisDokumen::all();

            // =========================================================================
            // 2. TEPAT 1 SUPERADMIN (superadmin@gmail.com)
            // =========================================================================
            $fotoSuperadmin = 'foto-staf/superadmin.svg';
            $this->createAvatarAsset($fotoSuperadmin, 'Super Administrator', 'superadmin');

            $superAdmin = User::updateOrCreate(
                ['email' => 'superadmin@gmail.com'],
                [
                    'nama' => 'Super Administrator',
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::SuperAdmin,
                    'bidang_id' => null,
                    'foto' => $fotoSuperadmin,
                    'status_aktif' => true,
                    'must_change_password' => false,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                ]
            );

            // =========================================================================
            // 3. TEPAT 1 ADMIN VERIFIKATOR (admin@gmail.com)
            // =========================================================================
            $fotoAdmin = 'foto-staf/admin.svg';
            $this->createAvatarAsset($fotoAdmin, 'Admin Verifikator', 'admin');

            $admin = User::updateOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'nama' => 'Admin Verifikator Magang',
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::Admin,
                    'bidang_id' => null,
                    'foto' => $fotoAdmin,
                    'status_aktif' => true,
                    'must_change_password' => false,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                ]
            );

            // =========================================================================
            // 4. TEPAT 1 PEMBIMBING LAPANGAN (pembimbing@gmail.com)
            // =========================================================================
            $fotoPembimbing = 'foto-staf/pembimbing.svg';
            $this->createAvatarAsset($fotoPembimbing, 'Budi Pembimbing', 'pembimbing');

            $pembimbing = User::updateOrCreate(
                ['email' => 'pembimbing@gmail.com'],
                [
                    'nama' => 'Budi Santoso, S.Kom (Pembimbing)',
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::Pembimbing,
                    'bidang_id' => $bidangEgov->id,
                    'foto' => $fotoPembimbing,
                    'status_aktif' => true,
                    'must_change_password' => false,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                ]
            );

            // =========================================================================
            // 5. RELINK & BERSIHKAN ADMIN, PEMBIMBING, SUPERADMIN LAINNYA
            // Sesuai perintah: "akun admin 1 aja pembimbing 1 dan superadmin1"
            // =========================================================================
            $staffCoreIds = [$superAdmin->id, $admin->id, $pembimbing->id];
            $otherStaffIds = User::whereIn('role', [RoleEnum::SuperAdmin, RoleEnum::Admin, RoleEnum::Pembimbing])
                ->whereNotIn('id', $staffCoreIds)
                ->pluck('id')
                ->toArray();

            if (! empty($otherStaffIds)) {
                DB::table('audit_log')->whereIn('user_id', $otherStaffIds)->update(['user_id' => $superAdmin->id]);
                DB::table('slot_khusus')->whereIn('dibuat_oleh', $otherStaffIds)->update(['dibuat_oleh' => $superAdmin->id]);
                DB::table('users')->whereIn('dibuat_oleh', $otherStaffIds)->update(['dibuat_oleh' => $superAdmin->id]);
                DB::table('pendaftaran')->whereIn('verifikator_id', $otherStaffIds)->update(['verifikator_id' => $admin->id]);
                DB::table('penempatan')->whereIn('dibuat_oleh', $otherStaffIds)->update(['dibuat_oleh' => $admin->id]);
                DB::table('penempatan')->whereIn('pembimbing_id', $otherStaffIds)->update(['pembimbing_id' => $pembimbing->id]);
                DB::table('agenda_bimbingan')->whereIn('pembimbing_id', $otherStaffIds)->update(['pembimbing_id' => $pembimbing->id]);
                DB::table('tugas_template')->whereIn('pembimbing_id', $otherStaffIds)->update(['pembimbing_id' => $pembimbing->id]);

                // Bersihkan event realtime staf lama
                DB::table('realtime_events')->whereIn('user_id', $otherStaffIds)->delete();

                // Hapus staf lainnya agar HANYA ADA 1 untuk masing-masing peran
                User::whereIn('id', $otherStaffIds)->delete();
            }

            // Hapus akun non-gmail lama jika ada
            $allowedEmails = [
                'superadmin@gmail.com',
                'admin@gmail.com',
                'pembimbing@gmail.com',
                'peserta@gmail.com',
                'peserta1@gmail.com',
                'peserta2@gmail.com',
                'peserta3@gmail.com',
                'peserta4@gmail.com',
                'peserta5@gmail.com',
                'peserta6@gmail.com',
                'peserta7@gmail.com',
                'peserta8@gmail.com',
                'peserta9@gmail.com',
                'peserta10@gmail.com',
            ];

            $obsoleteUserIds = User::whereNotIn('email', $allowedEmails)->pluck('id')->toArray();
            if (! empty($obsoleteUserIds)) {
                DB::table('audit_log')->whereIn('user_id', $obsoleteUserIds)->update(['user_id' => $superAdmin->id]);
                DB::table('realtime_events')->whereIn('user_id', $obsoleteUserIds)->delete();

                $obsoletePenempatanIds = DB::table('penempatan')->whereIn('peserta_id', $obsoleteUserIds)->pluck('id')->toArray();
                if (! empty($obsoletePenempatanIds)) {
                    DB::table('agenda_bimbingan')->whereIn('penempatan_id', $obsoletePenempatanIds)->delete();
                    DB::table('dokumen_verifikasi')->whereIn('penempatan_id', $obsoletePenempatanIds)->delete();
                    DB::table('tugas')->whereIn('penempatan_id', $obsoletePenempatanIds)->delete();
                    DB::table('logbook')->whereIn('penempatan_id', $obsoletePenempatanIds)->delete();
                    DB::table('penempatan')->whereIn('id', $obsoletePenempatanIds)->delete();
                }

                $obsoletePendaftaranIds = DB::table('pendaftaran')->whereIn('user_id', $obsoleteUserIds)->pluck('id')->toArray();
                if (! empty($obsoletePendaftaranIds)) {
                    DB::table('dokumen')->whereIn('pendaftaran_id', $obsoletePendaftaranIds)->delete();
                    DB::table('pendaftaran')->whereIn('id', $obsoletePendaftaranIds)->delete();
                }

                DB::table('peserta_profil')->whereIn('user_id', $obsoleteUserIds)->delete();
                DB::table('kelompok_magang')->whereIn('ketua_user_id', $obsoleteUserIds)->delete();
                User::whereIn('id', $obsoleteUserIds)->delete();
            }

            // =========================================================================
            // 6. HELPER FUNGSI BUAT PESERTA & PROFIL
            // =========================================================================
            $getJurusanAndKuota = function (string $namaJurusan, JenjangEnum $jenjang) use ($bulanAktif) {
                $kategori = ($jenjang === JenjangEnum::Smk) ? 'smk' : 'mahasiswa';
                $jurusan = Jurusan::firstOrCreate(
                    ['nama_jurusan' => $namaJurusan],
                    ['kategori' => $kategori, 'aktif' => true]
                );

                $kuota = Kuota::firstOrCreate(
                    ['bulan_magang_id' => $bulanAktif->id, 'jurusan_id' => $jurusan->id],
                    ['jumlah' => 15, 'terisi' => 1, 'aktif' => true]
                );

                return [$jurusan, $kuota];
            };

            $attachDokumen = function (Pendaftaran $pendaftaran) use ($jenisDokumenList) {
                foreach ($jenisDokumenList as $jd) {
                    Dokumen::updateOrCreate(
                        ['pendaftaran_id' => $pendaftaran->id, 'jenis_dokumen_id' => $jd->id],
                        [
                            'nama_asli' => strtolower(str_replace(' ', '_', $jd->nama)).'.pdf',
                            'path' => 'dokumen/sample_berkas.pdf',
                            'ukuran' => 245000,
                            'mime' => 'application/pdf',
                            'hash' => sha1('sample_'.$jd->kode.'_'.$pendaftaran->id),
                        ]
                    );
                }
            };

            // =========================================================================
            // 7. AKUN PESERTA UTAMA: peserta@gmail.com
            // Status: Diterima & Aktif (Sudah Mengerjakan Tugas & Sudah Mengisi Logbook)
            // =========================================================================
            [$jurTI, $kuotaTI] = $getJurusanAndKuota('Teknik Informatika', JenjangEnum::S1);
            $fotoPesertaUtama = 'profil-peserta/ahmad_peserta.svg';
            $this->createAvatarAsset($fotoPesertaUtama, 'Ahmad Siswa Magang', 'peserta');

            $userPeserta = User::updateOrCreate(
                ['email' => 'peserta@gmail.com'],
                [
                    'nama' => 'Ahmad Siswa Magang',
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::Peserta,
                    'foto' => $fotoPesertaUtama,
                    'status_aktif' => true,
                    'must_change_password' => false,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $userPeserta->id],
                [
                    'nim_nisn' => '22/491001/TK/54001',
                    'institusi' => 'Universitas Gadjah Mada',
                    'jurusan_id' => $jurTI->id,
                    'jenjang' => JenjangEnum::S1,
                    'no_hp' => '081234567890',
                    'alamat' => 'Sleman, D.I. Yogyakarta',
                    'foto' => $fotoPesertaUtama,
                    'nama_kontak_darurat' => 'Siti Wali',
                    'no_kontak_darurat' => '089876543210',
                    'hubungan_kontak_darurat' => 'Orang Tua / Wali',
                ]
            );

            $pendaftaranPeserta = Pendaftaran::updateOrCreate(
                ['user_id' => $userPeserta->id],
                [
                    'bulan_magang_id' => $bulanAktif->id,
                    'kuota_id' => $kuotaTI->id,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $admin->id,
                    'diajukan_at' => $now->copy()->subDays(25),
                    'diputuskan_at' => $now->copy()->subDays(20),
                ]
            );
            $attachDokumen($pendaftaranPeserta);

            $penempatanPeserta = Penempatan::updateOrCreate(
                ['peserta_id' => $userPeserta->id],
                [
                    'pendaftaran_id' => $pendaftaranPeserta->id,
                    'kuota_id' => $kuotaTI->id,
                    'bidang_id' => $bidangEgov->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'tgl_mulai' => $now->copy()->subDays(18)->startOfDay(),
                    'tgl_selesai' => $now->copy()->subDays(18)->addMonths(3)->startOfDay(),
                    'status' => StatusPenempatanEnum::Aktif,
                    'dibuat_oleh' => $admin->id,
                ]
            );

            // Tugas peserta utama (1 selesai, 1 proses)
            Tugas::updateOrCreate(
                ['penempatan_id' => $penempatanPeserta->id, 'judul' => 'Analisis Kebutuhan API SPBE'],
                [
                    'deskripsi' => 'Melakukan pemetaan endpoint data OPD untuk integrasi Satu Data Sleman.',
                    'kategori' => 'Analisis Sistem',
                    'kompleksitas' => KompleksitasEnum::Sedang,
                    'deadline' => $now->copy()->subDays(5),
                    'status' => StatusTugasEnum::Selesai,
                    'status_publikasi' => 'dipublikasikan',
                ]
            );

            Tugas::updateOrCreate(
                ['penempatan_id' => $penempatanPeserta->id, 'judul' => 'Pengembangan Modul Pelaporan Dashboard'],
                [
                    'deskripsi' => 'Membuat widget rekapitulasi data dan ekspor PDF laporan berkala.',
                    'kategori' => 'Pengembangan Fitur',
                    'kompleksitas' => KompleksitasEnum::Tinggi,
                    'deadline' => $now->copy()->addDays(7),
                    'status' => StatusTugasEnum::Proses,
                    'status_publikasi' => 'dipublikasikan',
                ]
            );

            // Logbook peserta utama (Minggu 1 & 2 terisi & disetujui)
            Logbook::updateOrCreate(
                ['penempatan_id' => $penempatanPeserta->id, 'tanggal' => $now->copy()->subDays(12)->toDateString()],
                [
                    'minggu_ke' => 1,
                    'kegiatan' => 'Orientasi lingkungan kerja dinas, pengenalan arsitektur SPBE Sleman, dan instalasi repositori lokal.',
                    'status' => StatusLogbookEnum::Disetujui,
                    'catatan_pembimbing' => 'Bagus, lanjutkan pemahaman struktur database.',
                ]
            );

            Logbook::updateOrCreate(
                ['penempatan_id' => $penempatanPeserta->id, 'tanggal' => $now->copy()->subDays(5)->toDateString()],
                [
                    'minggu_ke' => 2,
                    'kegiatan' => 'Implementasi schema database dan pengujian endpoint RESTful API integrasi dinas.',
                    'status' => StatusLogbookEnum::Disetujui,
                    'catatan_pembimbing' => 'Pekerjaan sesuai target timeline.',
                ]
            );

            // =========================================================================
            // 8. 10 AKUN PESERTA DENGAN STATUS ACAK & REALISTIS
            // Skenario yang dicakup sesuai permintaan pengguna:
            // - Belum diajukan (peserta1, peserta2)
            // - Sudah diajukan (peserta3, peserta4)
            // - Sudah diterima (peserta5)
            // - Belum mengerjakan tugas & belum mengisi logbook (peserta6)
            // - Sudah mengerjakan tugas tapi belum mengisi logbook (peserta7)
            // - Tugas proses & logbook perlu revisi (peserta8)
            // - Tugas selesai & logbook terisi rutin (peserta9)
            // - Magang selesai / alumni (peserta10)
            // =========================================================================

            $daftarPesertaData = [
                // 1. Belum diajukan (Draft pendaftaran, profil lengkap)
                [
                    'email' => 'peserta1@gmail.com',
                    'nama' => 'Bagas Saputra',
                    'institusi' => 'Universitas Gadjah Mada',
                    'jurusan' => 'Teknik Informatika',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '22/495001/TK/54101',
                    'tipe_kasus' => 'draft_belum_diajukan',
                ],
                // 2. Belum diajukan (Baru buat akun, profil belum lengkap, onboarding)
                [
                    'email' => 'peserta2@gmail.com',
                    'nama' => 'Anisa Rahmawati',
                    'institusi' => 'SMK Negeri 1 Sleman',
                    'jurusan' => 'Rekayasa Perangkat Lunak (RPL)',
                    'jenjang' => JenjangEnum::Smk,
                    'nim' => '0067823412',
                    'tipe_kasus' => 'baru_daftar_profil_belum_lengkap',
                ],
                // 3. Sudah diajukan (Menunggu verifikasi admin verifikator)
                [
                    'email' => 'peserta3@gmail.com',
                    'nama' => 'Chandra Wijaya',
                    'institusi' => 'UPN Veteran Yogyakarta',
                    'jurusan' => 'Sistem Informasi',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '123220045',
                    'tipe_kasus' => 'diajukan_menunggu_verifikasi',
                ],
                // 4. Sudah diajukan (Status perlu perbaikan dokumen)
                [
                    'email' => 'peserta4@gmail.com',
                    'nama' => 'Dinda Kirana',
                    'institusi' => 'UIN Sunan Kalijaga Yogyakarta',
                    'jurusan' => 'Informatika',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '22106050012',
                    'tipe_kasus' => 'diajukan_perlu_perbaikan',
                ],
                // 5. Sudah diterima (Pra-magang / menunggu tanggal mulai minggu depan)
                [
                    'email' => 'peserta5@gmail.com',
                    'nama' => 'Eko Prasetyo',
                    'institusi' => 'Universitas AMIKOM Yogyakarta',
                    'jurusan' => 'Teknik Komputer',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '22.11.4580',
                    'tipe_kasus' => 'diterima_pra_magang',
                ],
                // 6. Sudah aktif magang: BELUM mengerjakan tugas & BELUM mengisi logbook
                [
                    'email' => 'peserta6@gmail.com',
                    'nama' => 'Fani Rahmawati',
                    'institusi' => 'Universitas Islam Indonesia',
                    'jurusan' => 'Teknologi Informasi',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '22523098',
                    'tipe_kasus' => 'aktif_belum_tugas_belum_logbook',
                ],
                // 7. Sudah aktif magang: SUDAH mengerjakan tugas, tapi BELUM mengisi logbook
                [
                    'email' => 'peserta7@gmail.com',
                    'nama' => 'Gilang Ramadhan',
                    'institusi' => 'Universitas Sanata Dharma',
                    'jurusan' => 'Teknik Informatika',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '225314050',
                    'tipe_kasus' => 'aktif_sudah_tugas_belum_logbook',
                ],
                // 8. Sudah aktif magang: Tugas proses & logbook PERLU REVISI
                [
                    'email' => 'peserta8@gmail.com',
                    'nama' => 'Hana Safitri',
                    'institusi' => 'Universitas Atma Jaya Yogyakarta',
                    'jurusan' => 'Ilmu Komunikasi',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '220711904',
                    'tipe_kasus' => 'aktif_logbook_revisi',
                ],
                // 9. Sudah aktif magang: SUDAH mengerjakan tugas & SUDAH mengisi logbook rutin (teladan)
                [
                    'email' => 'peserta9@gmail.com',
                    'nama' => 'Indra Kurniawan',
                    'institusi' => 'Universitas Negeri Yogyakarta',
                    'jurusan' => 'Desain Grafis',
                    'jenjang' => JenjangEnum::S1,
                    'nim' => '22537144002',
                    'tipe_kasus' => 'aktif_sudah_tugas_sudah_logbook',
                ],
                // 10. Sudah SELESAI magang (Alumni)
                [
                    'email' => 'peserta10@gmail.com',
                    'nama' => 'Joko Susilo',
                    'institusi' => 'SMK Negeri 2 Depok Sleman',
                    'jurusan' => 'Administrasi Perkantoran',
                    'jenjang' => JenjangEnum::Smk,
                    'nim' => '0054321987',
                    'tipe_kasus' => 'selesai_magang',
                ],
            ];

            foreach ($daftarPesertaData as $idx => $pData) {
                [$jur, $kuo] = $getJurusanAndKuota($pData['jurusan'], $pData['jenjang']);

                $slug = Str::slug($pData['nama'], '_');
                $fotoPath = "profil-peserta/{$slug}.svg";
                $this->createAvatarAsset($fotoPath, $pData['nama'], 'peserta');

                // 1. Akun User Peserta
                $user = User::updateOrCreate(
                    ['email' => $pData['email']],
                    [
                        'nama' => $pData['nama'],
                        'password' => $defaultPassword,
                        'password_awal' => 'Password123!',
                        'role' => RoleEnum::Peserta,
                        'foto' => $fotoPath,
                        'status_aktif' => true,
                        'must_change_password' => false,
                        'email_verified_at' => $now,
                        'created_at' => $now,
                    ]
                );

                // 2. Profil Peserta
                $isProfilLengkap = ($pData['tipe_kasus'] !== 'baru_daftar_profil_belum_lengkap');
                PesertaProfil::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nim_nisn' => $pData['nim'],
                        'institusi' => $pData['institusi'],
                        'jurusan_id' => $jur->id,
                        'jenjang' => $pData['jenjang'],
                        'no_hp' => '0812'.str_pad((string) (11000000 + $idx), 8, '0', STR_PAD_LEFT),
                        'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                        'foto' => $isProfilLengkap ? $fotoPath : null,
                        'nama_kontak_darurat' => $isProfilLengkap ? ('Wali '.$pData['nama']) : null,
                        'no_kontak_darurat' => $isProfilLengkap ? '081399887766' : null,
                        'hubungan_kontak_darurat' => $isProfilLengkap ? 'Orang Tua' : null,
                    ]
                );

                // 3. Konfigurasi Skenario Berdasarkan Kasus
                switch ($pData['tipe_kasus']) {
                    case 'baru_daftar_profil_belum_lengkap':
                        // Belum mengajukan, baru onboarding registrasi
                        break;

                    case 'draft_belum_diajukan':
                        // Pendaftaran tersimpan draft, berkas belum dikirim
                        $regDraft = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
                                'diajukan_at' => null,
                            ]
                        );
                        break;

                    case 'diajukan_menunggu_verifikasi':
                        // Berkas sudah diajukan, menunggu verifikasi di dashboard admin
                        $regDiajukan = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diajukan,
                                'diajukan_at' => $now->copy()->subDay(),
                            ]
                        );
                        $attachDokumen($regDiajukan);
                        break;

                    case 'diajukan_perlu_perbaikan':
                        // Sudah diverifikasi admin, namun perlu revisi dokumen
                        $regRevisi = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::PerluPerbaikan,
                                'catatan' => 'Surat permohonan kampus belum distempel basah fakultas. Silakan perbaiki dan unggah ulang berkas PDF.',
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subDays(3),
                                'diputuskan_at' => $now->copy()->subDay(),
                            ]
                        );
                        $attachDokumen($regRevisi);
                        break;

                    case 'diterima_pra_magang':
                        // Sudah diterima, penempatan siap dengan tanggal mulai minggu depan
                        $regDiterima = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diterima,
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subDays(10),
                                'diputuskan_at' => $now->copy()->subDays(6),
                            ]
                        );
                        $attachDokumen($regDiterima);

                        Penempatan::updateOrCreate(
                            ['peserta_id' => $user->id],
                            [
                                'pendaftaran_id' => $regDiterima->id,
                                'kuota_id' => $kuo->id,
                                'bidang_id' => $bidangEgov->id,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'tgl_mulai' => $now->copy()->addDays(7)->startOfDay(),
                                'tgl_selesai' => $now->copy()->addDays(7)->addMonths(3)->startOfDay(),
                                'status' => StatusPenempatanEnum::Aktif,
                                'dibuat_oleh' => $admin->id,
                            ]
                        );
                        break;

                    case 'aktif_belum_tugas_belum_logbook':
                        // Aktif magang baru mulai, belum mengerjakan tugas & belum mengisi logbook
                        $regAktif1 = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diterima,
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subDays(15),
                                'diputuskan_at' => $now->copy()->subDays(10),
                            ]
                        );
                        $attachDokumen($regAktif1);

                        $penempatanAktif1 = Penempatan::updateOrCreate(
                            ['peserta_id' => $user->id],
                            [
                                'pendaftaran_id' => $regAktif1->id,
                                'kuota_id' => $kuo->id,
                                'bidang_id' => $bidangEgov->id,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'tgl_mulai' => $now->copy()->subDays(3)->startOfDay(),
                                'tgl_selesai' => $now->copy()->subDays(3)->addMonths(3)->startOfDay(),
                                'status' => StatusPenempatanEnum::Aktif,
                                'dibuat_oleh' => $admin->id,
                            ]
                        );

                        // Diberikan tugas namun status masih 'belum' dikerjakan
                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif1->id, 'judul' => 'Instalasi Framework dan Modul Awal'],
                            [
                                'deskripsi' => 'Setup development environment di laptop dinas dan pelajari source code.',
                                'kategori' => 'Teknis',
                                'kompleksitas' => KompleksitasEnum::Rendah,
                                'deadline' => $now->copy()->addDays(4),
                                'status' => StatusTugasEnum::Belum,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );
                        break;

                    case 'aktif_sudah_tugas_belum_logbook':
                        // Aktif magang, sudah mengerjakan tugas (status selesai & proses), logbook belum diisi
                        $regAktif2 = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diterima,
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subDays(20),
                                'diputuskan_at' => $now->copy()->subDays(15),
                            ]
                        );
                        $attachDokumen($regAktif2);

                        $penempatanAktif2 = Penempatan::updateOrCreate(
                            ['peserta_id' => $user->id],
                            [
                                'pendaftaran_id' => $regAktif2->id,
                                'kuota_id' => $kuo->id,
                                'bidang_id' => $bidangEgov->id,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'tgl_mulai' => $now->copy()->subDays(10)->startOfDay(),
                                'tgl_selesai' => $now->copy()->subDays(10)->addMonths(3)->startOfDay(),
                                'status' => StatusPenempatanEnum::Aktif,
                                'dibuat_oleh' => $admin->id,
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif2->id, 'judul' => 'Pembuatan Mockup Halaman Depan'],
                            [
                                'deskripsi' => 'Desain tampilan antarmuka responsif sesuai panduan identitas visual Sleman.',
                                'kategori' => 'UI/UX',
                                'kompleksitas' => KompleksitasEnum::Sedang,
                                'deadline' => $now->copy()->subDays(2),
                                'status' => StatusTugasEnum::Selesai,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif2->id, 'judul' => 'Integrasi API Layanan Publik'],
                            [
                                'deskripsi' => 'Koneksi endpoint data layanan publik ke backend dinas.',
                                'kategori' => 'Integrasi',
                                'kompleksitas' => KompleksitasEnum::Tinggi,
                                'deadline' => $now->copy()->addDays(5),
                                'status' => StatusTugasEnum::Proses,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );
                        break;

                    case 'aktif_logbook_revisi':
                        // Aktif magang, tugas sedang proses, ada logbook dengan status perlu revisi
                        $regAktif3 = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diterima,
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subDays(22),
                                'diputuskan_at' => $now->copy()->subDays(18),
                            ]
                        );
                        $attachDokumen($regAktif3);

                        $penempatanAktif3 = Penempatan::updateOrCreate(
                            ['peserta_id' => $user->id],
                            [
                                'pendaftaran_id' => $regAktif3->id,
                                'kuota_id' => $kuo->id,
                                'bidang_id' => $bidangEgov->id,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'tgl_mulai' => $now->copy()->subDays(14)->startOfDay(),
                                'tgl_selesai' => $now->copy()->subDays(14)->addMonths(3)->startOfDay(),
                                'status' => StatusPenempatanEnum::Aktif,
                                'dibuat_oleh' => $admin->id,
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif3->id, 'judul' => 'Pengujian Keamanan Modul Login'],
                            [
                                'deskripsi' => 'Uji penetrasi celah keamanan CSRF dan proteksi brute force rate limit.',
                                'kategori' => 'Security',
                                'kompleksitas' => KompleksitasEnum::Tinggi,
                                'deadline' => $now->copy()->addDays(3),
                                'status' => StatusTugasEnum::Proses,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Logbook::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif3->id, 'tanggal' => $now->copy()->subDays(7)->toDateString()],
                            [
                                'minggu_ke' => 1,
                                'kegiatan' => 'Melakukan pengujian pengamanan modul otentikasi SPBE dinas.',
                                'status' => StatusLogbookEnum::PerluRevisi,
                                'catatan_pembimbing' => 'Mohon tambahkan daftar screenshot hasil test script dan mitigasi yang disarankan.',
                            ]
                        );
                        break;

                    case 'aktif_sudah_tugas_sudah_logbook':
                        // Aktif teladan, sudah tugas selesai & logbook terisi rutin disetujui
                        $regAktif4 = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diterima,
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subDays(35),
                                'diputuskan_at' => $now->copy()->subDays(30),
                            ]
                        );
                        $attachDokumen($regAktif4);

                        $penempatanAktif4 = Penempatan::updateOrCreate(
                            ['peserta_id' => $user->id],
                            [
                                'pendaftaran_id' => $regAktif4->id,
                                'kuota_id' => $kuo->id,
                                'bidang_id' => $bidangEgov->id,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'tgl_mulai' => $now->copy()->subDays(28)->startOfDay(),
                                'tgl_selesai' => $now->copy()->subDays(28)->addMonths(3)->startOfDay(),
                                'status' => StatusPenempatanEnum::Aktif,
                                'dibuat_oleh' => $admin->id,
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif4->id, 'judul' => 'Desain Infografis Sleman Pintar'],
                            [
                                'deskripsi' => 'Membuat 3 poster infografis publikasi program digitalisasi Sleman.',
                                'kategori' => 'Desain Multimedia',
                                'kompleksitas' => KompleksitasEnum::Sedang,
                                'deadline' => $now->copy()->subDays(14),
                                'status' => StatusTugasEnum::Selesai,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif4->id, 'judul' => 'Produksi Video Sosialisasi SPBE'],
                            [
                                'deskripsi' => 'Editing video edukasi singkat tata cara penggunaan aplikasi dinas.',
                                'kategori' => 'Video Editing',
                                'kompleksitas' => KompleksitasEnum::Tinggi,
                                'deadline' => $now->copy()->subDays(2),
                                'status' => StatusTugasEnum::Selesai,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif4->id, 'judul' => 'Dokumentasi Panduan Teknis'],
                            [
                                'deskripsi' => 'Menyusun booklet panduan bagi pengguna akhir dinas.',
                                'kategori' => 'Dokumentasi',
                                'kompleksitas' => KompleksitasEnum::Rendah,
                                'deadline' => $now->copy()->addDays(8),
                                'status' => StatusTugasEnum::Proses,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Logbook::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif4->id, 'tanggal' => $now->copy()->subDays(21)->toDateString()],
                            [
                                'minggu_ke' => 1,
                                'kegiatan' => 'Brainstorming konsep visual infografis dan pengumpulan materi dinas.',
                                'status' => StatusLogbookEnum::Disetujui,
                                'catatan_pembimbing' => 'Konsep disetujui, eksekusi visual sangat baik.',
                            ]
                        );

                        Logbook::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif4->id, 'tanggal' => $now->copy()->subDays(14)->toDateString()],
                            [
                                'minggu_ke' => 2,
                                'kegiatan' => 'Finalisasi desain infografis dan approval bersama kepala seksi kehumasan.',
                                'status' => StatusLogbookEnum::Disetujui,
                                'catatan_pembimbing' => 'Hasil karya dipublikasikan di media sosial dinas.',
                            ]
                        );

                        Logbook::updateOrCreate(
                            ['penempatan_id' => $penempatanAktif4->id, 'tanggal' => $now->copy()->subDays(7)->toDateString()],
                            [
                                'minggu_ke' => 3,
                                'kegiatan' => 'Perekaman suara (voice-over) dan cutting video edukasi SPBE.',
                                'status' => StatusLogbookEnum::Disetujui,
                                'catatan_pembimbing' => 'Kualitas audio jernih dan visual menarik.',
                            ]
                        );
                        break;

                    case 'selesai_magang':
                        // Sudah selesai masa magang (Alumni)
                        $regSelesai = Pendaftaran::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'bulan_magang_id' => $bulanAktif->id,
                                'kuota_id' => $kuo->id,
                                'durasi_bulan' => 3,
                                'status' => StatusPendaftaranEnum::Diterima,
                                'verifikator_id' => $admin->id,
                                'diajukan_at' => $now->copy()->subMonths(4),
                                'diputuskan_at' => $now->copy()->subMonths(3)->subDays(25),
                            ]
                        );
                        $attachDokumen($regSelesai);

                        $penempatanSelesai = Penempatan::updateOrCreate(
                            ['peserta_id' => $user->id],
                            [
                                'pendaftaran_id' => $regSelesai->id,
                                'kuota_id' => $kuo->id,
                                'bidang_id' => $bidangSekretariat->id,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'tgl_mulai' => $now->copy()->subMonths(3)->subDays(10)->startOfDay(),
                                'tgl_selesai' => $now->copy()->subDays(10)->startOfDay(),
                                'tgl_ditetapkan_selesai' => $now->copy()->subDays(10)->startOfDay(),
                                'status' => StatusPenempatanEnum::Selesai,
                                'dibuat_oleh' => $admin->id,
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanSelesai->id, 'judul' => 'Digitalisasi Arsip Surat Masuk'],
                            [
                                'deskripsi' => 'Scanning dan indexing berkas arsip dinas ke sistem E-Arsip.',
                                'kategori' => 'Administrasi',
                                'kompleksitas' => KompleksitasEnum::Sedang,
                                'deadline' => $now->copy()->subMonths(2),
                                'status' => StatusTugasEnum::Selesai,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Tugas::updateOrCreate(
                            ['penempatan_id' => $penempatanSelesai->id, 'judul' => 'Penyusunan Laporan Akhir Magang'],
                            [
                                'deskripsi' => 'Menyusun laporan komprehensif hasil kegiatan magang 3 bulan di Diskominfo Sleman.',
                                'kategori' => 'Pelaporan',
                                'kompleksitas' => KompleksitasEnum::Tinggi,
                                'deadline' => $now->copy()->subDays(12),
                                'status' => StatusTugasEnum::Selesai,
                                'status_publikasi' => 'dipublikasikan',
                            ]
                        );

                        Logbook::updateOrCreate(
                            ['penempatan_id' => $penempatanSelesai->id, 'tanggal' => $now->copy()->subDays(11)->toDateString()],
                            [
                                'minggu_ke' => 12,
                                'kegiatan' => 'Penyelesaian administrasi penutupan magang dan presentasi evaluasi hasil magang.',
                                'status' => StatusLogbookEnum::Disetujui,
                                'catatan_pembimbing' => 'Selamat, seluruh program magang telah dituntaskan dengan predikat sangat memuaskan.',
                            ]
                        );
                        break;
                }
            }
        });
    }

    /**
     * Helper membuat gambar SVG avatar resmi dengan inisial nama.
     */
    protected function createAvatarAsset(string $path, string $name, string $role): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        $bgGradients = [
            'superadmin' => ['#0F172A', '#1E3A8A'], // Sleman Slate to Navy
            'admin' => ['#1E3A8A', '#0284C7'], // Navy to Ocean Blue
            'pembimbing' => ['#065F46', '#10B981'], // Forest to Emerald
            'peserta' => ['#1E40AF', '#6366F1'], // Royal Blue to Indigo
        ];

        $colors = $bgGradients[$role] ?? ['#1E293B', '#475569'];
        $initials = collect(explode(' ', $name))
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->join('');

        $svgContent = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320" width="320" height="320">
            <defs>
                <linearGradient id="grad_{$role}_{$initials}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{$colors[0]}" />
                    <stop offset="100%" stop-color="{$colors[1]}" />
                </linearGradient>
            </defs>
            <circle cx="160" cy="160" r="160" fill="url(#grad_{$role}_{$initials})" />
            
            <!-- Siluet Potret Modern -->
            <circle cx="160" cy="115" r="50" fill="#FFFFFF" opacity="0.95" />
            <path d="M65,280 C65,200 110,180 160,180 C210,180 255,200 255,280 Z" fill="#FFFFFF" opacity="0.95" />
            
            <!-- Badge Inisial Resmi -->
            <circle cx="250" cy="250" r="38" fill="{$colors[0]}" stroke="#FFFFFF" stroke-width="4" />
            <text x="250" y="258" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-size="20" font-weight="800" fill="#FFFFFF" text-anchor="middle">{$initials}</text>
        </svg>
        SVG;

        Storage::disk('public')->put($path, trim($svgContent));
    }
}
