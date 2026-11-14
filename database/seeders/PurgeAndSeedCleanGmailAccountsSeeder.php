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
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PurgeAndSeedCleanGmailAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');
        $now = now();

        // 1. Pastikan folder foto profil ada di storage disk public
        Storage::disk('public')->makeDirectory('foto-staf');
        Storage::disk('public')->makeDirectory('profil-peserta');

        DB::transaction(function () use ($defaultPassword, $now) {
            // ==========================================
            // LANGKAH 1: UNIT KERJA / BIDANG DINAS
            // ==========================================
            $bidangEgov = Bidang::firstOrCreate(
                ['nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)'],
                ['deskripsi' => 'Pengembangan SPBE dan integrasi aplikasi dinas.', 'aktif' => true]
            );
            $bidangIkp = Bidang::firstOrCreate(
                ['nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)'],
                ['deskripsi' => 'Publikasi multimedia, kehumasan, dan relasi media massa.', 'aktif' => true]
            );
            $bidangIti = Bidang::firstOrCreate(
                ['nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)'],
                ['deskripsi' => 'Jaringan intra-pemerintah, server dinas, dan CSIRT Sleman.', 'aktif' => true]
            );
            $bidangSekretariat = Bidang::firstOrCreate(
                ['nama_bidang' => 'Sekretariat Dinas'],
                ['deskripsi' => 'Administrasi umum, keuangan, dan tata kelola kepegawaian.', 'aktif' => true]
            );

            // ==========================================
            // LANGKAH 2: SUPERADMIN (2 AKUN)
            // ==========================================
            $superAdmins = [
                [
                    'email' => 'superadmin@gmail.com',
                    'nama' => 'Super Administrator Utama',
                    'foto' => 'foto-staf/superadmin-utama.svg',
                ],
                [
                    'email' => 'superadmin.sleman@gmail.com',
                    'nama' => 'Super Administrator Sleman',
                    'foto' => 'foto-staf/superadmin-sleman.svg',
                ],
            ];

            $superAdminModels = [];
            foreach ($superAdmins as $sa) {
                $this->createAvatarAsset($sa['foto'], $sa['nama'], 'superadmin');

                $user = User::where('email', $sa['email'])->first() ?? new User;
                $user->nama = $sa['nama'];
                $user->email = $sa['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::SuperAdmin;
                $user->foto = $sa['foto'];
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();

                $superAdminModels[] = $user;
            }
            $superAdminMain = $superAdminModels[0];

            // ==========================================
            // LANGKAH 3: ADMIN VERIFIKATOR (2 AKUN)
            // ==========================================
            $admins = [
                [
                    'email' => 'admin@gmail.com',
                    'nama' => 'Admin Verifikator Utama',
                    'foto' => 'foto-staf/admin-utama.svg',
                ],
                [
                    'email' => 'admin.verifikator@gmail.com',
                    'nama' => 'Admin Verifikator Sleman',
                    'foto' => 'foto-staf/admin-sleman.svg',
                ],
            ];

            $adminModels = [];
            foreach ($admins as $ad) {
                $this->createAvatarAsset($ad['foto'], $ad['nama'], 'admin');

                $user = User::where('email', $ad['email'])->first() ?? new User;
                $user->nama = $ad['nama'];
                $user->email = $ad['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::Admin;
                $user->foto = $ad['foto'];
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();

                $adminModels[] = $user;
            }
            $adminMain = $adminModels[0];

            // ==========================================
            // LANGKAH 4: PEMBIMBING LAPANGAN (4 AKUN)
            // ==========================================
            $pembimbingsData = [
                [
                    'email' => 'pembimbing@gmail.com',
                    'nama' => 'Budi Santoso, S.Kom',
                    'bidang_id' => $bidangEgov->id,
                    'foto' => 'foto-staf/pembimbing-egov.svg',
                ],
                [
                    'email' => 'pembimbing.ikp@gmail.com',
                    'nama' => 'Sri Wahyuni, S.I.Kom',
                    'bidang_id' => $bidangIkp->id,
                    'foto' => 'foto-staf/pembimbing-ikp.svg',
                ],
                [
                    'email' => 'pembimbing.iti@gmail.com',
                    'nama' => 'Agus Wibowo, S.T',
                    'bidang_id' => $bidangIti->id,
                    'foto' => 'foto-staf/pembimbing-iti.svg',
                ],
                [
                    'email' => 'pembimbing.sekretariat@gmail.com',
                    'nama' => 'Endang Sulastri, S.AP',
                    'bidang_id' => $bidangSekretariat->id,
                    'foto' => 'foto-staf/pembimbing-sekretariat.svg',
                ],
            ];

            $pembimbingModels = [];
            foreach ($pembimbingsData as $pb) {
                $this->createAvatarAsset($pb['foto'], $pb['nama'], 'pembimbing');

                $user = User::where('email', $pb['email'])->first() ?? new User;
                $user->nama = $pb['nama'];
                $user->email = $pb['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::Pembimbing;
                $user->bidang_id = $pb['bidang_id'];
                $user->foto = $pb['foto'];
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();

                $pembimbingModels[$pb['bidang_id']] = $user;
            }
            $pembimbingEgov = $pembimbingModels[$bidangEgov->id];

            // ==========================================
            // LANGKAH 5: RELINK FK KESELURUHAN & HAPUS NON-GMAIL
            // ==========================================
            $nonGmailUserIds = User::where('email', 'not like', '%@gmail.com')->pluck('id')->toArray();

            if (! empty($nonGmailUserIds)) {
                // Relink audit_log
                DB::table('audit_log')->whereIn('user_id', $nonGmailUserIds)->update(['user_id' => $superAdminMain->id]);

                // Relink agenda_bimbingan
                DB::table('agenda_bimbingan')->whereIn('pembimbing_id', $nonGmailUserIds)->update(['pembimbing_id' => $pembimbingEgov->id]);

                // Relink tugas_template
                DB::table('tugas_template')->whereIn('pembimbing_id', $nonGmailUserIds)->update(['pembimbing_id' => $pembimbingEgov->id]);

                // Relink pendaftaran.verifikator_id
                DB::table('pendaftaran')->whereIn('verifikator_id', $nonGmailUserIds)->update(['verifikator_id' => $adminMain->id]);

                // Relink slot_khusus.dibuat_oleh
                DB::table('slot_khusus')->whereIn('dibuat_oleh', $nonGmailUserIds)->update(['dibuat_oleh' => $superAdminMain->id]);

                // Relink penempatan.dibuat_oleh & pembimbing_id
                DB::table('penempatan')->whereIn('dibuat_oleh', $nonGmailUserIds)->update(['dibuat_oleh' => $adminMain->id]);
                DB::table('penempatan')->whereIn('pembimbing_id', $nonGmailUserIds)->update(['pembimbing_id' => $pembimbingEgov->id]);

                // Relink users.dibuat_oleh
                DB::table('users')->whereIn('dibuat_oleh', $nonGmailUserIds)->update(['dibuat_oleh' => $superAdminMain->id]);

                // Relink realtime_events
                DB::table('realtime_events')->whereIn('user_id', $nonGmailUserIds)->delete();

                // Bersihkan penempatan milik peserta non-gmail beserta relasi anaknya
                $pesertaNonGmailPenempatanIds = DB::table('penempatan')->whereIn('peserta_id', $nonGmailUserIds)->pluck('id')->toArray();
                if (! empty($pesertaNonGmailPenempatanIds)) {
                    DB::table('agenda_bimbingan')->whereIn('penempatan_id', $pesertaNonGmailPenempatanIds)->delete();
                    DB::table('dokumen_verifikasi')->whereIn('penempatan_id', $pesertaNonGmailPenempatanIds)->delete();
                    DB::table('tugas')->whereIn('penempatan_id', $pesertaNonGmailPenempatanIds)->delete();
                    DB::table('logbook')->whereIn('penempatan_id', $pesertaNonGmailPenempatanIds)->delete();
                    DB::table('penempatan')->whereIn('id', $pesertaNonGmailPenempatanIds)->delete();
                }

                // Bersihkan pendaftaran milik peserta non-gmail
                $pesertaNonGmailPendaftaranIds = DB::table('pendaftaran')->whereIn('user_id', $nonGmailUserIds)->pluck('id')->toArray();
                if (! empty($pesertaNonGmailPendaftaranIds)) {
                    DB::table('dokumen')->whereIn('pendaftaran_id', $pesertaNonGmailPendaftaranIds)->delete();
                    DB::table('pendaftaran')->whereIn('id', $pesertaNonGmailPendaftaranIds)->delete();
                }

                // Bersihkan profil peserta non-gmail
                DB::table('peserta_profil')->whereIn('user_id', $nonGmailUserIds)->delete();

                // Hapus kelompok magang ketua non-gmail
                DB::table('kelompok_magang')->whereIn('ketua_user_id', $nonGmailUserIds)->delete();

                // Hapus akun non-gmail
                User::whereIn('id', $nonGmailUserIds)->delete();
            }

            // ==========================================
            // LANGKAH 6: 20 PESERTA MAGANG @GMAIL.COM LENGKAP
            // ==========================================
            $bulanAktif = BulanMagang::where('aktif', true)->first() ?? BulanMagang::first();

            $pesertaDatasets = [
                // EGOV (1 - 5)
                ['nama' => 'Aditya Pratama', 'email' => 'aditya.pratama@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'Universitas Gadjah Mada', 'jurusan_id' => 18, 'jenjang' => JenjangEnum::S1, 'nim' => '20/456789/TK/51234'],
                ['nama' => 'Bunga Citra', 'email' => 'bunga.citra@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'UPN Veteran Yogyakarta', 'jurusan_id' => 7, 'jenjang' => JenjangEnum::S1, 'nim' => '123200045'],
                ['nama' => 'Chandra Wijaya', 'email' => 'chandra.wijaya@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'Universitas AMIKOM Yogyakarta', 'jurusan_id' => 5, 'jenjang' => JenjangEnum::S1, 'nim' => '21.11.4589'],
                ['nama' => 'Dinda Kirana', 'email' => 'dinda.kirana@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'Universitas Negeri Yogyakarta', 'jurusan_id' => 6, 'jenjang' => JenjangEnum::D3, 'nim' => '21520241012'],
                ['nama' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'SMK N 2 Depok Sleman', 'jurusan_id' => 4, 'jenjang' => JenjangEnum::Smk, 'nim' => '0054321987'],

                // IKP (6 - 10)
                ['nama' => 'Fani Rahmawati', 'email' => 'fani.rahma@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'UIN Sunan Kalijaga Yogyakarta', 'jurusan_id' => 10, 'jenjang' => JenjangEnum::S1, 'nim' => '20102050041'],
                ['nama' => 'Gilang Ramadhan', 'email' => 'gilang.ramadhan@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'Institut Seni Indonesia Yogyakarta', 'jurusan_id' => 12, 'jenjang' => JenjangEnum::S1, 'nim' => '2012015024'],
                ['nama' => 'Hana Safitri', 'email' => 'hana.safitri@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'Universitas Atma Jaya Yogyakarta', 'jurusan_id' => 11, 'jenjang' => JenjangEnum::S1, 'nim' => '190906874'],
                ['nama' => 'Indra Kurniawan', 'email' => 'indra.kurniawan@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'SMK N 1 Bantul', 'jurusan_id' => 2, 'jenjang' => JenjangEnum::Smk, 'nim' => '0061298453'],
                ['nama' => 'Joko Susilo', 'email' => 'joko.susilo@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'Universitas Ahmad Dahlan', 'jurusan_id' => 10, 'jenjang' => JenjangEnum::S1, 'nim' => '2000024158'],

                // ITI (11 - 15)
                ['nama' => 'Kartika Putri', 'email' => 'kartika.putri@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Universitas Gadjah Mada', 'jurusan_id' => 8, 'jenjang' => JenjangEnum::S1, 'nim' => '20/460124/TK/51892'],
                ['nama' => 'Lukman Hakim', 'email' => 'lukman.hakim@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Universitas Sanata Dharma', 'jurusan_id' => 17, 'jenjang' => JenjangEnum::S1, 'nim' => '205314082'],
                ['nama' => 'Maya Anggraini', 'email' => 'maya.anggraini@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'SMK N 2 Depok Sleman', 'jurusan_id' => 1, 'jenjang' => JenjangEnum::Smk, 'nim' => '0057891234'],
                ['nama' => 'Naufal Rizky', 'email' => 'naufal.rizky@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Universitas Negeri Yogyakarta', 'jurusan_id' => 9, 'jenjang' => JenjangEnum::S1, 'nim' => '21518241005'],
                ['nama' => 'Olivia Tan', 'email' => 'olivia.tan@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Institut Teknologi Nasional Yogyakarta', 'jurusan_id' => 18, 'jenjang' => JenjangEnum::S1, 'nim' => '710021004'],

                // SEKRETARIAT (16 - 20)
                ['nama' => 'Panji Nugroho', 'email' => 'panji.nugroho@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'Universitas Negeri Yogyakarta', 'jurusan_id' => 14, 'jenjang' => JenjangEnum::S1, 'nim' => '20802241033'],
                ['nama' => 'Qori Amanda', 'email' => 'qori.amanda@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'Universitas Islam Indonesia', 'jurusan_id' => 13, 'jenjang' => JenjangEnum::S1, 'nim' => '20311245'],
                ['nama' => 'Rian Ardiansyah', 'email' => 'rian.ardiansyah@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'SMK N 1 Depok Sleman', 'jurusan_id' => 3, 'jenjang' => JenjangEnum::Smk, 'nim' => '0065432190'],
                ['nama' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'UPN Veteran Yogyakarta', 'jurusan_id' => 16, 'jenjang' => JenjangEnum::D3, 'nim' => '141200088'],
                ['nama' => 'Taufik Hidayat', 'email' => 'taufik.hidayat@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'STIE YKPN Yogyakarta', 'jurusan_id' => 15, 'jenjang' => JenjangEnum::D3, 'nim' => '112031498'],
            ];

            foreach ($pesertaDatasets as $idx => $pData) {
                $slug = strtolower(str_replace(' ', '_', $pData['nama']));
                $fotoPath = "profil-peserta/{$slug}.svg";

                // Buat berkas fisik foto peserta
                $this->createAvatarAsset($fotoPath, $pData['nama'], 'peserta');

                // User
                $u = User::where('email', $pData['email'])->first() ?? new User;
                $u->nama = $pData['nama'];
                $u->email = $pData['email'];
                $u->password = $defaultPassword;
                $u->role = RoleEnum::Peserta;
                $u->foto = $fotoPath;
                $u->status_aktif = true;
                $u->must_change_password = false;
                $u->email_verified_at = $now;
                $u->save();

                // Profil Peserta
                PesertaProfil::updateOrCreate(
                    ['user_id' => $u->id],
                    [
                        'nim_nisn' => $pData['nim'],
                        'institusi' => $pData['institusi'],
                        'jurusan_id' => $pData['jurusan_id'],
                        'jenjang' => $pData['jenjang'],
                        'no_hp' => '0812'.str_pad((string) (10000000 + $idx), 8, '0', STR_PAD_LEFT),
                        'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                        'foto' => $fotoPath,
                        'nama_kontak_darurat' => 'Wali '.$pData['nama'],
                        'no_kontak_darurat' => '081399887766',
                        'hubungan_kontak_darurat' => 'Orang Tua',
                    ]
                );

                // Kuota untuk jurusan peserta
                $kuota = null;
                if ($bulanAktif) {
                    $kuota = Kuota::firstOrCreate(
                        [
                            'bulan_magang_id' => $bulanAktif->id,
                            'jurusan_id' => $pData['jurusan_id'],
                        ],
                        [
                            'jumlah' => 10,
                            'terisi' => 1,
                            'aktif' => true,
                        ]
                    );
                }

                // Pendaftaran
                $pendaftaran = Pendaftaran::firstOrCreate(
                    ['user_id' => $u->id],
                    [
                        'bulan_magang_id' => $bulanAktif?->id,
                        'kuota_id' => $kuota?->id,
                        'tipe' => 'mandiri',
                        'durasi_bulan' => 3,
                        'status' => StatusPendaftaranEnum::Diterima,
                        'diajukan_at' => $now->copy()->subDays(30),
                        'diputuskan_at' => $now->copy()->subDays(25),
                    ]
                );

                // Penempatan
                $penempatan = Penempatan::firstOrCreate(
                    ['peserta_id' => $u->id],
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'kuota_id' => $kuota?->id,
                        'bidang_id' => $pData['bidang']->id,
                        'pembimbing_id' => $pData['pembimbing']->id,
                        'jalur' => JalurEnum::Umum,
                        'sumber' => SumberPenempatanEnum::Online,
                        'status' => StatusPenempatanEnum::Aktif,
                        'tgl_mulai' => $now->copy()->subDays(20)->startOfDay(),
                        'tgl_selesai' => $now->copy()->addDays(70)->endOfDay(),
                    ]
                );

                // Pastikan penempatan terhubung ke pembimbing yang tepat
                $penempatan->update([
                    'bidang_id' => $pData['bidang']->id,
                    'pembimbing_id' => $pData['pembimbing']->id,
                    'status' => StatusPenempatanEnum::Aktif,
                ]);

                // Buat 1 Tugas Awal & 1 Logbook Terverifikasi
                Tugas::firstOrCreate(
                    ['penempatan_id' => $penempatan->id, 'judul' => 'Orientasi dan Pengenalan Alur Kerja Unit '.$pData['bidang']->nama_bidang],
                    [
                        'deskripsi' => 'Mempelajari SOP, pengenalan sistem kerja internal, dan instalasi tools pendukung operasional.',
                        'kategori' => 'Orientasi',
                        'kompleksitas' => KompleksitasEnum::Rendah,
                        'status' => StatusTugasEnum::Selesai,
                        'deadline' => $now->copy()->subDays(10),
                    ]
                );

                $logbookDate = $now->copy()->subDays(15)->startOfDay()->toDateString();
                Logbook::firstOrCreate(
                    ['penempatan_id' => $penempatan->id, 'tanggal' => $logbookDate],
                    [
                        'minggu_ke' => 1,
                        'kegiatan' => 'Menghadiri sesi pengarahan dengan pembimbing lapangan dan menyusun rencana kerja magang.',
                        'status' => StatusLogbookEnum::Disetujui,
                        'catatan_pembimbing' => 'Rencana kerja sangat baik. Lanjutkan ke tahap implementasi.',
                    ]
                );
            }

            // ==========================================
            // LANGKAH 7: PRESERVASI & POLISH AKUN DEV GOOGLE
            // ==========================================
            $devUser = User::where('email', 'adhityagalang553@gmail.com')->first();
            if ($devUser) {
                $devFotoPath = 'profil-peserta/galang_adhit.svg';
                $this->createAvatarAsset($devFotoPath, $devUser->nama ?? 'Galang Adhit', 'peserta');

                if (empty($devUser->foto)) {
                    $devUser->foto = $devFotoPath;
                    $devUser->save();
                }

                // Ensure profil exists
                PesertaProfil::firstOrCreate(
                    ['user_id' => $devUser->id],
                    [
                        'nim_nisn' => '20/460000/PA/19999',
                        'institusi' => 'Universitas Gadjah Mada',
                        'jurusan_id' => 18,
                        'jenjang' => JenjangEnum::S1,
                        'no_hp' => '081234567890',
                        'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                        'foto' => $devFotoPath,
                    ]
                );
            }

            // ==========================================
            // LANGKAH 8: PEMBERSIHAN PESERTA DUMMY SURPLUS
            // ==========================================
            $allowedEmails = array_merge(
                ['superadmin@gmail.com', 'superadmin.sleman@gmail.com'],
                ['admin@gmail.com', 'admin.verifikator@gmail.com'],
                ['pembimbing@gmail.com', 'pembimbing.ikp@gmail.com', 'pembimbing.iti@gmail.com', 'pembimbing.sekretariat@gmail.com'],
                ['adhityagalang553@gmail.com'], // Akun Pengembang
                array_column($pesertaDatasets, 'email')
            );

            $surplusUsers = User::whereNotIn('email', $allowedEmails)->get();
            $surplusUserIds = $surplusUsers->pluck('id')->toArray();
            if (! empty($surplusUserIds)) {
                // Relink audit_log untuk surplus users
                DB::table('audit_log')->whereIn('user_id', $surplusUserIds)->update(['user_id' => $superAdminMain->id]);

                // Relink pendaftaran.verifikator_id & slot_khusus & penempatan dibuat_oleh/pembimbing_id
                DB::table('pendaftaran')->whereIn('verifikator_id', $surplusUserIds)->update(['verifikator_id' => $adminMain->id]);
                DB::table('penempatan')->whereIn('dibuat_oleh', $surplusUserIds)->update(['dibuat_oleh' => $adminMain->id]);
                DB::table('penempatan')->whereIn('pembimbing_id', $surplusUserIds)->update(['pembimbing_id' => $pembimbingEgov->id]);
                DB::table('slot_khusus')->whereIn('dibuat_oleh', $surplusUserIds)->update(['dibuat_oleh' => $superAdminMain->id]);
                DB::table('users')->whereIn('dibuat_oleh', $surplusUserIds)->update(['dibuat_oleh' => $superAdminMain->id]);

                // Relink kelompok_magang
                $firstPeserta = User::where('email', 'aditya.pratama@gmail.com')->first();
                if ($firstPeserta) {
                    DB::table('kelompok_magang')->whereIn('ketua_user_id', $surplusUserIds)->update(['ketua_user_id' => $firstPeserta->id]);
                }

                // Bersihkan penempatan surplus
                $surplusPenempatanIds = DB::table('penempatan')->whereIn('peserta_id', $surplusUserIds)->pluck('id')->toArray();
                if (! empty($surplusPenempatanIds)) {
                    DB::table('agenda_bimbingan')->whereIn('penempatan_id', $surplusPenempatanIds)->delete();
                    DB::table('dokumen_verifikasi')->whereIn('penempatan_id', $surplusPenempatanIds)->delete();
                    DB::table('tugas')->whereIn('penempatan_id', $surplusPenempatanIds)->delete();
                    DB::table('logbook')->whereIn('penempatan_id', $surplusPenempatanIds)->delete();
                    DB::table('penempatan')->whereIn('id', $surplusPenempatanIds)->delete();
                }

                // Bersihkan pendaftaran surplus
                $surplusPendaftaranIds = DB::table('pendaftaran')->whereIn('user_id', $surplusUserIds)->pluck('id')->toArray();
                if (! empty($surplusPendaftaranIds)) {
                    DB::table('dokumen')->whereIn('pendaftaran_id', $surplusPendaftaranIds)->delete();
                    DB::table('pendaftaran')->whereIn('id', $surplusPendaftaranIds)->delete();
                }

                DB::table('peserta_profil')->whereIn('user_id', $surplusUserIds)->delete();
                DB::table('realtime_events')->whereIn('user_id', $surplusUserIds)->delete();

                User::whereIn('id', $surplusUserIds)->delete();
            }
        });
    }

    /**
     * Helper membuat berkas grafik avatar SVG beresolusi tinggi di storage public.
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
