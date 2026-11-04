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
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AgendaBimbingan;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\RouteMetric;
use App\Models\Rubrik;
use App\Models\SlotKhusus;
use App\Models\SurveiKepuasan;
use App\Models\Tugas;
use App\Models\TugasTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionRealisticDataSeeder extends Seeder
{
    /**
     * Seed data produksi realistis untuk Portal MagKom Diskominfo.
     * Mencakup multi-bidang, multi-pembimbing, siklus hidup peserta lengkap
     * (Alumni/Selesai, Aktif Berjalan, Antrean Seleksi Berkas, Edge Cases),
     * serta telemetri Audit Log dan Route Metrics.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');
        $now = Carbon::now('Asia/Jakarta');

        // =========================================================================
        // 1. MASTER DATA: BIDANG & JURUSAN
        // =========================================================================
        $bidangEgov = Bidang::updateOrCreate(
            ['nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)'],
            [
                'deskripsi' => 'Pengembangan ekosistem digital, integrasi aplikasi SPBE, tata kelola data, dan layanan sistem pemerintahan berbasis elektronik.',
                'aktif' => true,
            ]
        );

        $bidangIkp = Bidang::updateOrCreate(
            ['nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)'],
            [
                'deskripsi' => 'Pengelolaan kanal komunikasi resmi pemerintah kabupaten, publikasi multimedia, peliputan berita, kemitraan media, dan relasi publik.',
                'aktif' => true,
            ]
        );

        $bidangIti = Bidang::updateOrCreate(
            ['nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)'],
            [
                'deskripsi' => 'Pengelolaan jaringan intra pemerintah daerah, infrastruktur data center, server cloud, keamanan informasi siber, dan CSIRT Sleman.',
                'aktif' => true,
            ]
        );

        $bidangSekretariat = Bidang::updateOrCreate(
            ['nama_bidang' => 'Sekretariat Dinas'],
            [
                'deskripsi' => 'Pelayanan administrasi umum perkantoran, persuratan dinas, tata kearsipan, manajemen rekod, kepegawaian, dan perbendaharaan.',
                'aktif' => true,
            ]
        );

        // Ambil jurusan-jurusan utama
        $jTI = Jurusan::where('nama_jurusan', 'Teknik Informatika')->first()
            ?? Jurusan::where('nama_jurusan', 'Informatika')->first();
        $jSI = Jurusan::where('nama_jurusan', 'Sistem Informasi')->first();
        $jIK = Jurusan::where('nama_jurusan', 'Ilmu Komunikasi')->first();
        $jDKV = Jurusan::where('nama_jurusan', 'Desain Grafis (mahasiswa)')->first()
            ?? Jurusan::where('nama_jurusan', 'Desain Grafis')->first();
        $jTKJ = Jurusan::where('nama_jurusan', 'like', '%TKJ%')->first()
            ?? Jurusan::where('nama_jurusan', 'like', '%Jaringan%')->first();
        $jRPL = Jurusan::where('nama_jurusan', 'like', '%RPL%')->first()
            ?? Jurusan::where('nama_jurusan', 'like', '%Perangkat Lunak%')->first();
        $jAP = Jurusan::where('nama_jurusan', 'like', '%Administrasi Perkantoran%')->first()
            ?? Jurusan::where('nama_jurusan', 'like', '%Manajemen Perkantoran%')->first();
        $jStat = Jurusan::where('nama_jurusan', 'like', '%Statistik%')->first();

        // Fallback jika belum ada
        $jTI = $jTI ?? Jurusan::first();
        $jSI = $jSI ?? $jTI;
        $jIK = $jIK ?? $jTI;
        $jDKV = $jDKV ?? $jTI;
        $jTKJ = $jTKJ ?? $jTI;
        $jRPL = $jRPL ?? $jTI;
        $jAP = $jAP ?? $jTI;
        $jStat = $jStat ?? $jTI;

        // =========================================================================
        // 2. STAF INTERNAL: ADMIN & PEMBIMBING LAPANGAN PER BIDANG
        // =========================================================================
        $admin = User::firstOrNew(['email' => 'admin@magkom.go.id']);
        $admin->nama = 'Admin Verifikator Magang Sleman';
        $admin->password = $defaultPassword;
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = $now;
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->save();

        $superAdmin = User::firstOrNew(['email' => 'superadmin@magkom.go.id']);
        $superAdmin->nama = 'Super Administrator Kominfo';
        $superAdmin->password = $defaultPassword;
        $superAdmin->role = RoleEnum::SuperAdmin;
        $superAdmin->email_verified_at = $now;
        $superAdmin->status_aktif = true;
        $superAdmin->must_change_password = false;
        $superAdmin->save();

        // Pembimbing 1: Budi Santoso (EGOV)
        $pembimbingEgov1 = User::firstOrNew(['email' => 'pembimbing@magkom.go.id']);
        $pembimbingEgov1->nama = 'Budi Santoso, S.Kom';
        $pembimbingEgov1->password = $defaultPassword;
        $pembimbingEgov1->role = RoleEnum::Pembimbing;
        $pembimbingEgov1->bidang_id = $bidangEgov->id;
        $pembimbingEgov1->email_verified_at = $now;
        $pembimbingEgov1->status_aktif = true;
        $pembimbingEgov1->must_change_password = false;
        $pembimbingEgov1->save();

        // Pembimbing 2: Rahmat Hidayat (EGOV - Web & API)
        $pembimbingEgov2 = User::firstOrNew(['email' => 'rahmat.hidayat@magkom.go.id']);
        $pembimbingEgov2->nama = 'Rahmat Hidayat, M.Kom';
        $pembimbingEgov2->password = $defaultPassword;
        $pembimbingEgov2->role = RoleEnum::Pembimbing;
        $pembimbingEgov2->bidang_id = $bidangEgov->id;
        $pembimbingEgov2->email_verified_at = $now;
        $pembimbingEgov2->status_aktif = true;
        $pembimbingEgov2->must_change_password = false;
        $pembimbingEgov2->save();

        // Pembimbing 3: Sri Wahyuni (IKP - Publikasi & Media)
        $pembimbingIkp1 = User::firstOrNew(['email' => 'sri.wahyuni@magkom.go.id']);
        $pembimbingIkp1->nama = 'Sri Wahyuni, S.I.Kom';
        $pembimbingIkp1->password = $defaultPassword;
        $pembimbingIkp1->role = RoleEnum::Pembimbing;
        $pembimbingIkp1->bidang_id = $bidangIkp->id;
        $pembimbingIkp1->email_verified_at = $now;
        $pembimbingIkp1->status_aktif = true;
        $pembimbingIkp1->must_change_password = false;
        $pembimbingIkp1->save();

        // Pembimbing 4: Deni Prasetya (IKP - Desain Multimedia)
        $pembimbingIkp2 = User::firstOrNew(['email' => 'deni.prasetya@magkom.go.id']);
        $pembimbingIkp2->nama = 'Deni Prasetya, S.Sn';
        $pembimbingIkp2->password = $defaultPassword;
        $pembimbingIkp2->role = RoleEnum::Pembimbing;
        $pembimbingIkp2->bidang_id = $bidangIkp->id;
        $pembimbingIkp2->email_verified_at = $now;
        $pembimbingIkp2->status_aktif = true;
        $pembimbingIkp2->must_change_password = false;
        $pembimbingIkp2->save();

        // Pembimbing 5: Agus Wibowo (ITI - NOC & Jaringan)
        $pembimbingIti = User::firstOrNew(['email' => 'agus.wibowo@magkom.go.id']);
        $pembimbingIti->nama = 'Agus Wibowo, S.T';
        $pembimbingIti->password = $defaultPassword;
        $pembimbingIti->role = RoleEnum::Pembimbing;
        $pembimbingIti->bidang_id = $bidangIti->id;
        $pembimbingIti->email_verified_at = $now;
        $pembimbingIti->status_aktif = true;
        $pembimbingIti->must_change_password = false;
        $pembimbingIti->save();

        // Pembimbing 6: Endang Sulastri (Sekretariat)
        $pembimbingSekretariat = User::firstOrNew(['email' => 'endang.sulastri@magkom.go.id']);
        $pembimbingSekretariat->nama = 'Endang Sulastri, S.AP';
        $pembimbingSekretariat->password = $defaultPassword;
        $pembimbingSekretariat->role = RoleEnum::Pembimbing;
        $pembimbingSekretariat->bidang_id = $bidangSekretariat->id;
        $pembimbingSekretariat->email_verified_at = $now;
        $pembimbingSekretariat->status_aktif = true;
        $pembimbingSekretariat->must_change_password = false;
        $pembimbingSekretariat->save();

        // =========================================================================
        // 3. BULAN MAGANG & KUOTA
        // =========================================================================
        $resolveBulan = function (string $tanggalMulai, array $attributes): BulanMagang {
            $bulan = BulanMagang::whereDate('bulan_mulai', $tanggalMulai)->first();
            if ($bulan) {
                $bulan->update($attributes);

                return $bulan;
            }

            return BulanMagang::create(array_merge(['bulan_mulai' => $tanggalMulai], $attributes));
        };

        // Batch Lalu (Agustus 2026): Selesai / Alumni
        $bulanAgustus = $resolveBulan('2026-08-01', [
            'nama_bulan' => 'Agustus 2026',
            'tgl_buka' => '2026-07-01',
            'tgl_tutup' => '2026-07-25',
            'aktif' => false,
        ]);

        // Batch Aktif Saat Ini (November 2026): Sedang Berjalan
        $bulanNovember = $resolveBulan('2026-11-01', [
            'nama_bulan' => 'November 2026',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        // Batch Pendaftaran Terbuka (Desember 2026): Sedang Seleksi Berkas
        $bulanDesember = $resolveBulan('2026-12-01', [
            'nama_bulan' => 'Desember 2026',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-25',
            'aktif' => true,
        ]);

        // Batch Tahun Depan (Januari 2027): Segera Dibuka
        $bulanJanuari = $resolveBulan('2027-01-01', [
            'nama_bulan' => 'Januari 2027',
            'tgl_buka' => '2026-12-01',
            'tgl_tutup' => '2026-12-25',
            'aktif' => true,
        ]);

        // Kuota Helper
        $semuaJurusan = Jurusan::where('aktif', true)->get();
        foreach ([$bulanAgustus, $bulanNovember, $bulanDesember, $bulanJanuari] as $bm) {
            foreach ($semuaJurusan as $j) {
                Kuota::firstOrCreate(
                    ['bulan_magang_id' => $bm->id, 'jurusan_id' => $j->id],
                    [
                        'jumlah' => 6,
                        'terisi' => $bm->id === $bulanAgustus->id ? 6 : 2,
                        'aktif' => true,
                    ]
                );
            }
        }

        // Slot Khusus MoU MBKM Kampus & Kemitraan Vokasi
        $slotKhususMbkm = SlotKhusus::whereDate('bulan_mulai', '2026-11-01')->first()
            ?? SlotKhusus::create([
                'bulan_mulai' => '2026-11-01',
                'jumlah' => 5,
                'terisi' => 2,
                'aktif' => true,
                'dibuat_oleh' => $superAdmin->id,
            ]);
        $slotKhususMbkm->update([
            'jumlah' => 5,
            'terisi' => 2,
            'aktif' => true,
            'dibuat_oleh' => $superAdmin->id,
        ]);

        // =========================================================================
        // 4. TEMPLATE TUGAS KEDINASAN REUSABLE
        // =========================================================================
        $templates = [
            [
                'pembimbing_id' => $pembimbingEgov1->id,
                'judul' => 'Pembuatan Modul Integrasi API Layanan Publik Sleman',
                'deskripsi' => 'Rancang dan implementasikan endpoint RESTful dengan standar OpenAPI untuk menghubungkan basis data dinas.',
                'kategori' => 'Backend Development',
                'kompleksitas' => KompleksitasEnum::Tinggi,
                'link_referensi' => 'https://github.com/diskominfo-sleman/api-specs',
                'is_global' => true,
            ],
            [
                'pembimbing_id' => $pembimbingEgov2->id,
                'judul' => 'Slicing & Styling UI Komponen Dashboard Sesuai Token DS-MagKom',
                'deskripsi' => 'Implementasikan view Blade modular menggunakan palet warna resmi Diskominfo Sleman (#01347C, #003566).',
                'kategori' => 'Frontend Development',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'link_referensi' => 'https://magkom.slemankab.go.id/design-system',
                'is_global' => true,
            ],
            [
                'pembimbing_id' => $pembimbingIkp1->id,
                'judul' => 'Penyusunan Rilis Berita & Liputan Kegiatan Pelayanan Publik',
                'deskripsi' => 'Susun naskah rilis pers dan artikel portal berita resmi pemerintah kabupaten dengan kaidah jurnalistik 5W+1H.',
                'kategori' => 'Jurnalistik & Humas',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'link_referensi' => 'https://slemankab.go.id/berita',
                'is_global' => true,
            ],
            [
                'pembimbing_id' => $pembimbingIkp2->id,
                'judul' => 'Desain Infografis Edukasi Call Center 112 & Layanan Darurat',
                'deskripsi' => 'Rancang konten visual infografis media sosial ukuran 1:1 dan 9:16 untuk sosialisasi layanan darurat bebas pulsa 112.',
                'kategori' => 'Desain Grafis & Multimedia',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'link_referensi' => 'https://slemankab.go.id/call-112',
                'is_global' => true,
            ],
            [
                'pembimbing_id' => $pembimbingIti->id,
                'judul' => 'Audit Keamanan Port Server & Monitoring Lalu Lintas NOC',
                'deskripsi' => 'Lakukan pemindaian port tertutup, pantau grafik utilisasi bandwidth Mikrotik, dan laporkan anomali traffic.',
                'kategori' => 'Jaringan & Keamanan Sistem',
                'kompleksitas' => KompleksitasEnum::Tinggi,
                'link_referensi' => 'https://csirt.slemankab.go.id',
                'is_global' => true,
            ],
            [
                'pembimbing_id' => $pembimbingSekretariat->id,
                'judul' => 'Digitalisasi dan Klasifikasi Arsip Surat Masuk Perkantoran',
                'deskripsi' => 'Lakukan pemindaian dokumen fisik surat dinas dan input metadata ke dalam aplikasi tata kelola persuratan dinas.',
                'kategori' => 'Administrasi & Kearsipan',
                'kompleksitas' => KompleksitasEnum::Rendah,
                'link_referensi' => 'https://arsip.slemankab.go.id',
                'is_global' => true,
            ],
        ];

        foreach ($templates as $tmpl) {
            TugasTemplate::updateOrCreate(
                ['judul' => $tmpl['judul']],
                $tmpl
            );
        }

        // Helper untuk membuat atau memperbarui User & PesertaProfil
        $buatPeserta = function (
            string $nama,
            string $email,
            string $institusi,
            Jurusan $jurusan,
            JenjangEnum $jenjang,
            string $nim,
            bool $aktif = true
        ) use ($defaultPassword, $now): User {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'nama' => $nama,
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Peserta,
                    'status_aktif' => $aktif,
                    'email_verified_at' => $now,
                    'must_change_password' => false,
                    'no_telp' => '0812'.rand(10000000, 99999999),
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim_nisn' => $nim,
                    'institusi' => $institusi,
                    'jurusan_id' => $jurusan->id,
                    'jenjang' => $jenjang,
                    'no_hp' => $user->no_telp,
                    'nama_kontak_darurat' => 'Keluarga / Wali '.$nama,
                    'no_kontak_darurat' => '0813'.rand(10000000, 99999999),
                    'hubungan_kontak_darurat' => 'Orang Tua / Wali',
                    'persetujuan_wali_at' => $now,
                ]
            );

            return $user;
        };

        // Helper untuk melampirkan berkas pendaftaran
        $jenisDokumenList = JenisDokumen::all();
        $lampirkanBerkas = function (Pendaftaran $pendaftaran) use ($jenisDokumenList) {
            foreach ($jenisDokumenList as $jd) {
                Dokumen::updateOrCreate(
                    ['pendaftaran_id' => $pendaftaran->id, 'jenis_dokumen_id' => $jd->id],
                    [
                        'nama_asli' => strtolower(str_replace(' ', '_', $jd->nama)).'.pdf',
                        'path' => 'dokumen/sample_'.$jd->kode.'.pdf',
                        'ukuran' => rand(180000, 450000),
                        'mime' => 'application/pdf',
                        'hash' => sha1('sample_'.$jd->kode.'_'.$pendaftaran->id),
                    ]
                );
            }
        };

        $rubrikList = Rubrik::where('aktif', true)->get();

        // =========================================================================
        // 5. COHORT A: ALUMNI MAGANG LULUS (BATCH AGUSTUS - OKTOBER 2026)
        // 10 Peserta Lulus Sempurna dengan Nilai Final, Logbook Penuh, dan Sertifikat
        // =========================================================================
        $alumniData = [
            ['Anisa Rahmawati', 'anisa.rahma@mail.ugm.ac.id', 'Universitas Gadjah Mada', $jTI, JenjangEnum::S1, '21/478201/TK/52100', $bidangEgov, $pembimbingEgov1, 92.5, 'A'],
            ['Bayu Setiawan', 'bayu.setiawan@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jIK, JenjangEnum::S1, '21208144012', $bidangIkp, $pembimbingIkp1, 88.0, 'A'],
            ['Citra Dewi Permata', 'citra.dewi@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jSI, JenjangEnum::S1, '123210044', $bidangEgov, $pembimbingEgov2, 90.0, 'A'],
            ['Dicki Wahyudi', 'dicki.wahyudi@smkn2depoksleman.sch.id', 'SMK Negeri 2 Depok Sleman', $jTKJ, JenjangEnum::Smk, '0065432190', $bidangIti, $pembimbingIti, 87.5, 'A'],
            ['Erna Fatmawati', 'erna.fatmawati@students.amikom.ac.id', 'Universitas Amikom Yogyakarta', $jDKV, JenjangEnum::S1, '21.11.4120', $bidangIkp, $pembimbingIkp2, 85.0, 'A'],
            ['Fajar Ramadhan', 'fajar.ramadhan@uii.ac.id', 'Universitas Islam Indonesia', $jTI, JenjangEnum::S1, '21523044', $bidangEgov, $pembimbingEgov1, 94.0, 'A'],
            ['Gita Novitasari', 'gita.novita@smkn1sleman.sch.id', 'SMK Negeri 1 Sleman', $jAP, JenjangEnum::Smk, '0078901234', $bidangSekretariat, $pembimbingSekretariat, 86.0, 'A'],
            ['Hasan Basri', 'hasan.basri@uin-suka.ac.id', 'UIN Sunan Kalijaga', $jIK, JenjangEnum::S1, '21102040055', $bidangIkp, $pembimbingIkp1, 79.5, 'B'],
            ['Intan Permatasari', 'intan.permata@usd.ac.id', 'Universitas Sanata Dharma', $jTI, JenjangEnum::S1, '215314012', $bidangIti, $pembimbingIti, 83.0, 'B'],
            ['Joko Prasetyo', 'joko.prasetyo@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jSI, JenjangEnum::S1, '123210089', $bidangEgov, $pembimbingEgov2, 89.0, 'A'],
        ];

        foreach ($alumniData as $idx => [$nama, $email, $kampus, $jur, $jenjang, $nim, $bidang, $pembimbing, $nilaiAkhir, $predikat]) {
            $user = $buatPeserta($nama, $email, $kampus, $jur, $jenjang, $nim, false);

            $kuotaLalu = Kuota::firstOrCreate(
                ['bulan_magang_id' => $bulanAgustus->id, 'jurusan_id' => $jur->id],
                ['jumlah' => 10, 'terisi' => 10, 'aktif' => false]
            );

            $reg = Pendaftaran::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bulan_magang_id' => $bulanAgustus->id,
                    'kuota_id' => $kuotaLalu->id,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $admin->id,
                    'diajukan_at' => Carbon::parse('2026-07-10 10:00:00'),
                    'diputuskan_at' => Carbon::parse('2026-07-15 14:30:00'),
                    'catatan' => 'Berkas pendaftaran terverifikasi lengkap. Diterima pada '.$bidang->nama_bidang.'.',
                ]
            );
            $lampirkanBerkas($reg);

            $penempatan = Penempatan::updateOrCreate(
                ['peserta_id' => $user->id],
                [
                    'pendaftaran_id' => $reg->id,
                    'kuota_id' => $kuotaLalu->id,
                    'bidang_id' => $bidang->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => '2026-08-01',
                    'tgl_selesai' => '2026-10-31',
                    'tgl_ditetapkan_selesai' => '2026-10-31',
                    'tgl_nonaktif' => '2026-11-14',
                    'status' => StatusPenempatanEnum::Selesai,
                ]
            );

            // Buat Tugas Selesai
            $tugas1 = Tugas::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'judul' => 'Implementasi Proyek Magang Bidang '.$bidang->nama_bidang],
                [
                    'deskripsi' => 'Pengembangan modul terintegrasi serta laporan capaian proyek bimbingan.',
                    'kategori' => 'Tugas Utama',
                    'kompleksitas' => KompleksitasEnum::Tinggi,
                    'deadline' => '2026-10-20',
                    'status' => StatusTugasEnum::Selesai,
                    'link_referensi' => 'https://github.com/magkom-sleman/alumni-batch-aug',
                ]
            );

            ProgresLog::updateOrCreate(
                ['tugas_id' => $tugas1->id, 'tgl_lapor' => '2026-10-18'],
                [
                    'catatan' => 'Pekerjaan proyek selesai 100%, source code dan berkas uji telah diunggah ke repository.',
                    'link' => 'https://github.com/diskominfo-sleman/showcase-'.$idx,
                    'status_review' => StatusReviewProgresEnum::Diterima,
                    'feedback' => 'Hasil pengerjaan sangat rapi dan memenuhi seluruh kriteria penerimaan kedinasan.',
                ]
            );

            // Logbook Mingguan Lengkap (10 Minggu Magang)
            for ($w = 1; $w <= 10; $w++) {
                $tglLog = Carbon::parse('2026-08-01')->addWeeks($w - 1)->addDays(rand(1, 4))->toDateString();
                Logbook::updateOrCreate(
                    ['penempatan_id' => $penempatan->id, 'minggu_ke' => $w],
                    [
                        'tanggal' => $tglLog,
                        'kegiatan' => 'Aktivitas minggu ke-'.$w.': Melaksanakan agenda praktis di '.$bidang->nama_bidang.', koordinasi bersama pembimbing ('.$pembimbing->nama.'), penyusunan dokumentasi teknis, dan verifikasi luaran.',
                        'status' => StatusLogbookEnum::Disetujui,
                        'catatan_pembimbing' => 'Logbook diverifikasi. Pertahankan konsistensi kinerja.',
                    ]
                );
            }

            // Penilaian Final & Rubrik Detail
            $penilaian = Penilaian::updateOrCreate(
                ['penempatan_id' => $penempatan->id],
                [
                    'pembimbing_id' => $pembimbing->id,
                    'nilai_akhir' => $nilaiAkhir,
                    'predikat' => $predikat,
                    'catatan' => 'Peserta menunjukkan dedikasi, kedisiplinan, dan integritas tinggi selama 3 bulan magang di Diskominfo Sleman.',
                    'status' => StatusPenilaianEnum::Final,
                ]
            );

            foreach ($rubrikList as $rubrik) {
                PenilaianDetail::updateOrCreate(
                    ['penilaian_id' => $penilaian->id, 'rubrik_id' => $rubrik->id],
                    [
                        'skor' => (int) round($nilaiAkhir + rand(-3, 3)),
                        'bobot_snapshot' => $rubrik->bobot,
                    ]
                );
            }

            // Survei Kepuasan Alumni
            SurveiKepuasan::updateOrCreate(
                ['penempatan_id' => $penempatan->id],
                [
                    'skor_mentoring' => 5,
                    'skor_tugas' => rand(4, 5),
                    'skor_fasilitas' => rand(4, 5),
                    'skor_nps' => rand(9, 10),
                    'umpan_balik' => 'Pengalaman magang di Diskominfo Sleman sangat mendidik dan relevan dengan kebutuhan industri teknologi.',
                    'saran' => 'Fasilitas jaringan wifi dan ruang kolaborasi sudah sangat prima, pertahankan program magang ini.',
                ]
            );
        }

        // =========================================================================
        // 6. COHORT B: PESERTA AKTIF SAAT INI (BATCH NOVEMBER 2026 - JANUARI 2027)
        // 16 Peserta Aktif di Seluruh 4 Bidang & Berbagai Kondisi Operasional
        // =========================================================================
        $kuotaNovTI = Kuota::where('bulan_magang_id', $bulanNovember->id)->where('jurusan_id', $jTI->id)->first();
        $kuotaNovSI = Kuota::where('bulan_magang_id', $bulanNovember->id)->where('jurusan_id', $jSI->id)->first() ?? $kuotaNovTI;
        $kuotaNovIK = Kuota::where('bulan_magang_id', $bulanNovember->id)->where('jurusan_id', $jIK->id)->first() ?? $kuotaNovTI;
        $kuotaNovDKV = Kuota::where('bulan_magang_id', $bulanNovember->id)->where('jurusan_id', $jDKV->id)->first() ?? $kuotaNovTI;
        $kuotaNovTKJ = Kuota::where('bulan_magang_id', $bulanNovember->id)->where('jurusan_id', $jTKJ->id)->first() ?? $kuotaNovTI;
        $kuotaNovAP = Kuota::where('bulan_magang_id', $bulanNovember->id)->where('jurusan_id', $jAP->id)->first() ?? $kuotaNovTI;

        $pesertaAktifData = [
            // EGOV (Aptika) - 5 Peserta
            ['Naufal Pratama', 'peserta@magkom.go.id', 'SMK Negeri 2 Depok Sleman', $jTKJ, JenjangEnum::Smk, '0054321987', $bidangEgov, $pembimbingEgov1, $kuotaNovTKJ, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Aditya Pratama', 'aditya.pratama@mail.ugm.ac.id', 'Universitas Gadjah Mada', $jTI, JenjangEnum::S1, '22/491001/TK/54001', $bidangEgov, $pembimbingEgov1, $kuotaNovTI, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Chandra Wijaya', 'chandra.wijaya@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jSI, JenjangEnum::S1, '123220045', $bidangEgov, $pembimbingEgov2, $kuotaNovSI, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Kevin Sanjaya', 'kevin.sanjaya@amikom.ac.id', 'Universitas Amikom Yogyakarta', $jTI, JenjangEnum::S1, '22.11.5540', $bidangEgov, $pembimbingEgov2, $kuotaNovTI, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Larasati Putri', 'larasati.putri@uii.ac.id', 'Universitas Islam Indonesia', $jSI, JenjangEnum::S1, '22523112', $bidangEgov, $pembimbingEgov1, $kuotaNovSI, '2026-11-01', '2027-01-31', 'aktif', false],

            // IKP (Informasi & Komunikasi Publik) - 4 Peserta
            ['Bunga Citra Lestari', 'bunga.citra@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jIK, JenjangEnum::S1, '22537144002', $bidangIkp, $pembimbingIkp1, $kuotaNovIK, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Mahendra Putra', 'mahendra.putra@uin-suka.ac.id', 'UIN Sunan Kalijaga', $jIK, JenjangEnum::S1, '22102050011', $bidangIkp, $pembimbingIkp1, $kuotaNovIK, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Nadia Salsabila', 'nadia.salsabila@amikom.ac.id', 'Universitas Amikom Yogyakarta', $jDKV, JenjangEnum::S1, '22.11.5900', $bidangIkp, $pembimbingIkp2, $kuotaNovDKV, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Oki Setiawan', 'oki.setiawan@smkn1sleman.sch.id', 'SMK Negeri 1 Sleman', $jDKV, JenjangEnum::Smk, '0076543211', $bidangIkp, $pembimbingIkp2, $kuotaNovDKV, '2026-11-01', '2027-01-31', 'aktif', false],

            // ITI (Infrastruktur Teknologi Informasi / Jaringan) - 4 Peserta
            ['Panji Nugroho', 'panji.nugroho@ugm.ac.id', 'Universitas Gadjah Mada', $jTI, JenjangEnum::S1, '21/481900/TK/53900', $bidangIti, $pembimbingIti, $kuotaNovTI, '2026-08-15', '2026-11-08', 'aktif', true], // Ending in 6 days!
            ['Rizky Firmansyah', 'rizky.firmansyah@smkn2depoksleman.sch.id', 'SMK Negeri 2 Depok Sleman', $jTKJ, JenjangEnum::Smk, '0069876543', $bidangIti, $pembimbingIti, $kuotaNovTKJ, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Satria Bagaskara', 'satria.bagas@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jTI, JenjangEnum::S1, '123220101', $bidangIti, $pembimbingIti, $kuotaNovTI, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Tia Maharani', 'tia.maharani@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jTI, JenjangEnum::S1, '22537144089', $bidangIti, $pembimbingIti, $kuotaNovTI, '2026-11-01', '2027-01-31', 'aktif', false],

            // Sekretariat Dinas - 3 Peserta
            ['Utari Handayani', 'utari.handayani@smkn1sleman.sch.id', 'SMK Negeri 1 Sleman', $jAP, JenjangEnum::Smk, '0071234567', $bidangSekretariat, $pembimbingSekretariat, $kuotaNovAP, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Vino Bastian', 'vino.bastian@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jAP, JenjangEnum::S1, '141220022', $bidangSekretariat, $pembimbingSekretariat, $kuotaNovAP, '2026-11-01', '2027-01-31', 'aktif', false],
            ['Winda Kusuma', 'winda.kusuma@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jAP, JenjangEnum::S1, '22802244015', $bidangSekretariat, $pembimbingSekretariat, $kuotaNovAP, '2026-11-01', '2027-01-31', 'aktif', false],
        ];

        foreach ($pesertaAktifData as [$nama, $email, $kampus, $jur, $jenjang, $nim, $bidang, $pembimbing, $kuotaTarget, $tglMulai, $tglSelesai, $statusP, $isEndingSoon]) {
            $user = $buatPeserta($nama, $email, $kampus, $jur, $jenjang, $nim, true);

            $reg = Pendaftaran::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bulan_magang_id' => $bulanNovember->id,
                    'kuota_id' => $kuotaTarget->id,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $admin->id,
                    'diajukan_at' => Carbon::parse($tglMulai)->subDays(15),
                    'diputuskan_at' => Carbon::parse($tglMulai)->subDays(7),
                    'catatan' => 'Lolos verifikasi berkas magang periode '.$bulanNovember->nama_bulan.'. Ditempatkan pada '.$bidang->nama_bidang.'.',
                ]
            );
            $lampirkanBerkas($reg);

            $penempatan = Penempatan::updateOrCreate(
                ['peserta_id' => $user->id],
                [
                    'pendaftaran_id' => $reg->id,
                    'kuota_id' => $kuotaTarget->id,
                    'bidang_id' => $bidang->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => $tglMulai,
                    'tgl_selesai' => $tglSelesai,
                    'status' => StatusPenempatanEnum::Aktif,
                ]
            );

            // Tugas 1: Selesai
            $tugasSelesai = Tugas::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'judul' => 'Orientasi & Pemahaman Arsitektur Sistem '.$bidang->nama_bidang],
                [
                    'deskripsi' => 'Mempelajari SOP kedinasan, struktur tata kelola data, dan konfigurasi lingkungan kerja lokal.',
                    'kategori' => 'Orientasi Teknis',
                    'kompleksitas' => KompleksitasEnum::Rendah,
                    'deadline' => Carbon::parse($tglMulai)->addDays(10)->toDateString(),
                    'status' => StatusTugasEnum::Selesai,
                    'link_referensi' => 'https://magkom.slemankab.go.id/docs/guidelines',
                ]
            );

            ProgresLog::updateOrCreate(
                ['tugas_id' => $tugasSelesai->id, 'tgl_lapor' => Carbon::parse($tglMulai)->addDays(8)->toDateString()],
                [
                    'catatan' => 'Orientasi selesai, seluruh dokumen pedoman teknis telah dipelajari dan lingkungan dev siap digunakan.',
                    'status_review' => StatusReviewProgresEnum::Diterima,
                    'feedback' => 'Bagus, persiapan kerja sangat baik.',
                ]
            );

            // Tugas 2: Sedang Dikerjakan (Proses)
            $tugasProses = Tugas::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'judul' => 'Pengerjaan Modul Teknis: '.$bidang->nama_bidang],
                [
                    'deskripsi' => 'Pengembangan fitur atau penugasan spesifik sesuai arahan harian pembimbing lapangan.',
                    'kategori' => 'Tugas Inti',
                    'kompleksitas' => KompleksitasEnum::Sedang,
                    'deadline' => $isEndingSoon ? Carbon::now('Asia/Jakarta')->subDay()->toDateString() : Carbon::now('Asia/Jakarta')->addDays(rand(3, 10))->toDateString(),
                    'status' => StatusTugasEnum::Proses,
                    'link_referensi' => 'https://magkom.slemankab.go.id/tasks/spec',
                ]
            );

            // Sebagian submit progres log yang berstatus Menunggu Reviu di Action Hub
            ProgresLog::updateOrCreate(
                ['tugas_id' => $tugasProses->id, 'tgl_lapor' => Carbon::now('Asia/Jakarta')->subDay()->toDateString()],
                [
                    'catatan' => 'Pengerjaan fitur telah mencapai 75%. Mohon review draf pull request atau dokumen kerja terlampir.',
                    'link' => 'https://github.com/magkom-sleman/pr/'.rand(10, 99),
                    'status_review' => StatusReviewProgresEnum::Menunggu,
                ]
            );

            // Tugas 3: Belum Dikerjakan
            Tugas::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'judul' => 'Penyusunan Draf Laporan Akhir Magang & Portofolio'],
                [
                    'deskripsi' => 'Menyusun laporan akhir PKL/Magang Bab 1 s/d Bab 4 dan melampirkan lembar pengesahan pembimbing.',
                    'kategori' => 'Laporan Akhir',
                    'kompleksitas' => KompleksitasEnum::Sedang,
                    'deadline' => Carbon::parse($tglSelesai)->subDays(7)->toDateString(),
                    'status' => StatusTugasEnum::Belum,
                ]
            );

            // Logbook Harian/Mingguan: Minggu 1 (Disetujui), Minggu 2 (Disetujui), Minggu 3 (Terkirim - Menunggu Review)
            Logbook::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'minggu_ke' => 1],
                [
                    'tanggal' => Carbon::parse($tglMulai)->addDays(4)->toDateString(),
                    'kegiatan' => 'Orientasi lingkungan kerja dinas, perkenalan dengan rekan staf dan pembimbing lapangan, serta inventarisasi kebutuhan penugasan di '.$bidang->nama_bidang.'.',
                    'status' => StatusLogbookEnum::Disetujui,
                    'catatan_pembimbing' => 'Aktivitas awal tercatat dengan baik.',
                ]
            );

            Logbook::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'minggu_ke' => 2],
                [
                    'tanggal' => Carbon::parse($tglMulai)->addDays(11)->toDateString(),
                    'kegiatan' => 'Melaksanakan tugas teknis kedinasan, berpartisipasi dalam rapat koordinasi mingguan, serta menyelesaikan penugasan mandiri bimbingan.',
                    'status' => StatusLogbookEnum::Disetujui,
                    'catatan_pembimbing' => 'Pekerjaan berjalan sesuai jadwal.',
                ]
            );

            // Logbook Minggu 3: Sebagian "Terkirim" (Menunggu Review Action Hub) dan 1 "Perlu Revisi"
            $statusLogbook3 = ($nama === 'Aditya Pratama') ? StatusLogbookEnum::PerluRevisi : StatusLogbookEnum::Terkirim;
            $catatanLogbook3 = ($nama === 'Aditya Pratama') ? 'Mohon lengkapi dokumentasi foto saat melakukan koordinasi bersama tim jaringan.' : null;

            Logbook::updateOrCreate(
                ['penempatan_id' => $penempatan->id, 'minggu_ke' => 3],
                [
                    'tanggal' => Carbon::now('Asia/Jakarta')->subDays(rand(1, 2))->toDateString(),
                    'kegiatan' => 'Melakukan pengujian sistem integrasi, evaluasi data, serta menyiapkan rekap capaian bimbingan minggu ketiga.',
                    'status' => $statusLogbook3,
                    'catatan_pembimbing' => $catatanLogbook3,
                ]
            );

            // Kasus Khusus Peserta Menjelang Selesai (Panji Nugroho): Penilaian Draft
            if ($isEndingSoon) {
                $penilaianDraft = Penilaian::updateOrCreate(
                    ['penempatan_id' => $penempatan->id],
                    [
                        'pembimbing_id' => $pembimbing->id,
                        'nilai_akhir' => 84.50,
                        'predikat' => 'B',
                        'catatan' => 'Draft penilaian awal bimbingan. Menunggu kelengkapan draf laporan akhir magang.',
                        'status' => StatusPenilaianEnum::Draft,
                    ]
                );

                foreach ($rubrikList as $rubrik) {
                    PenilaianDetail::updateOrCreate(
                        ['penilaian_id' => $penilaianDraft->id, 'rubrik_id' => $rubrik->id],
                        [
                            'skor' => 85,
                            'bobot_snapshot' => $rubrik->bobot,
                        ]
                    );
                }
            }
        }

        // =========================================================================
        // 7. KELOMPOK MAGANG REALISTIS (3 TIM KELOMPOK KEMITRAAN KAMPUS & SMK)
        // =========================================================================
        $ketua1 = User::where('email', 'kelompok@magkom.go.id')->first()
            ?? $buatPeserta('Dimas Pratama (Ketua Tim)', 'kelompok@magkom.go.id', 'Universitas Gadjah Mada', $jTI, JenjangEnum::S1, '22/498765/SV/21001');

        $timSmartSleman = KelompokMagang::updateOrCreate(
            ['ketua_user_id' => $ketua1->id],
            ['nama_kelompok' => 'Tim Inovasi Smart Sleman (UGM)']
        );
        $timSmartSleman->anggota()->delete();
        $timSmartSleman->anggota()->createMany([
            ['nama' => 'Citra Lestari', 'nim_nisn' => '22/498766/SV/21002', 'no_hp' => '081298765433', 'email' => 'citra@mail.ugm.ac.id'],
            ['nama' => 'Dian Wahyudi', 'nim_nisn' => '22/498767/SV/21003', 'no_hp' => '081298765434', 'email' => 'dian@mail.ugm.ac.id'],
        ]);

        $ketua2 = User::where('email', 'aditya.pratama@mail.ugm.ac.id')->first();
        if ($ketua2) {
            $timEgov = KelompokMagang::updateOrCreate(
                ['ketua_user_id' => $ketua2->id],
                ['nama_kelompok' => 'Tim SPBE & E-Government Sleman']
            );
            $timEgov->anggota()->delete();
            $timEgov->anggota()->createMany([
                ['nama' => 'Bagus Wicaksono', 'nim_nisn' => '22/491002/TK/54002', 'no_hp' => '081298765440', 'email' => 'bagus@mail.ugm.ac.id'],
                ['nama' => 'Farah Nabila', 'nim_nisn' => '22/491003/TK/54003', 'no_hp' => '081298765441', 'email' => 'farah@mail.ugm.ac.id'],
            ]);
        }

        $ketua3 = User::where('email', 'dicki.wahyudi@smkn2depoksleman.sch.id')->first();
        if ($ketua3) {
            $timNoc = KelompokMagang::updateOrCreate(
                ['ketua_user_id' => $ketua3->id],
                ['nama_kelompok' => 'Tim Network Operations Center (SMKN 2 Depok)']
            );
            $timNoc->anggota()->delete();
            $timNoc->anggota()->createMany([
                ['nama' => 'Gilang Perkasa', 'nim_nisn' => '0065432191', 'no_hp' => '081298765450', 'email' => 'gilang@smkn2depok.sch.id'],
                ['nama' => 'Hanif Alamsyah', 'nim_nisn' => '0065432192', 'no_hp' => '081298765451', 'email' => 'hanif@smkn2depok.sch.id'],
            ]);
        }

        // =========================================================================
        // 8. COHORT C: CALON PESERTA / ANTREAN PENDAFTARAN (DESEMBER 2026 & JANUARI 2027)
        // 16 Pendaftar dengan Ragam Status Nyata (Diajukan, SLA Kritis, Perlu Perbaikan,
        // Waiting List, Diterima, Ditolak)
        // =========================================================================
        $kuotaDesTI = Kuota::where('bulan_magang_id', $bulanDesember->id)->where('jurusan_id', $jTI->id)->first();
        $kuotaDesSI = Kuota::where('bulan_magang_id', $bulanDesember->id)->where('jurusan_id', $jSI->id)->first() ?? $kuotaDesTI;
        $kuotaDesIK = Kuota::where('bulan_magang_id', $bulanDesember->id)->where('jurusan_id', $jIK->id)->first() ?? $kuotaDesTI;
        $kuotaDesDKV = Kuota::where('bulan_magang_id', $bulanDesember->id)->where('jurusan_id', $jDKV->id)->first() ?? $kuotaDesTI;
        $kuotaDesTKJ = Kuota::where('bulan_magang_id', $bulanDesember->id)->where('jurusan_id', $jTKJ->id)->first() ?? $kuotaDesTI;
        $kuotaDesAP = Kuota::where('bulan_magang_id', $bulanDesember->id)->where('jurusan_id', $jAP->id)->first() ?? $kuotaDesTI;

        $pendaftarBaru = [
            // 1-4. Diajukan (Dalam SLA < 3 Hari)
            ['Ahmad Fauzi', 'ahmad.fauzi@uii.ac.id', 'Universitas Islam Indonesia', $jTI, JenjangEnum::S1, '23523001', StatusPendaftaranEnum::Diajukan, $now->copy()->subHours(18), null, null, $kuotaDesTI],
            ['Bella Safira', 'bella.safira@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jIK, JenjangEnum::S1, '23537144001', StatusPendaftaranEnum::Diajukan, $now->copy()->subHours(26), null, null, $kuotaDesIK],
            ['Cipto Mangunkusumo', 'cipto.mangun@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jSI, JenjangEnum::S1, '123230010', StatusPendaftaranEnum::Diajukan, $now->copy()->subDays(1), null, null, $kuotaDesSI],
            ['Dina Mariana', 'dina.mariana@smkn1sleman.sch.id', 'SMK Negeri 1 Sleman', $jAP, JenjangEnum::Smk, '0081234560', StatusPendaftaranEnum::Diajukan, $now->copy()->subDays(2), null, null, $kuotaDesAP],

            // 5-6. Diajukan (SLA BREACHED > 3 Hari Belum Diverifikasi -> Hazard Red Chip di Admin Dashboard!)
            ['Erlangga Putra', 'erlangga.putra@amikom.ac.id', 'Universitas Amikom Yogyakarta', $jTI, JenjangEnum::S1, '23.11.7701', StatusPendaftaranEnum::Diajukan, $now->copy()->subDays(5), null, null, $kuotaDesTI],
            ['Fitri Handayani', 'fitri.handayani@mail.ugm.ac.id', 'Universitas Gadjah Mada', $jDKV, JenjangEnum::S1, '23/501234/SV/23001', StatusPendaftaranEnum::Diajukan, $now->copy()->subDays(6), null, null, $kuotaDesDKV],

            // 7-9. Perlu Perbaikan (Dengan Catatan Resmi Admin Verifikator)
            ['Galih Wicaksono', 'galih.wicaksono@smkn2depoksleman.sch.id', 'SMK Negeri 2 Depok Sleman', $jTKJ, JenjangEnum::Smk, '0075432101', StatusPendaftaranEnum::PerluPerbaikan, $now->copy()->subDays(3), $now->copy()->subDay(), 'Surat permohonan magang belum mencantumkan stempel basah kepala sekolah. Mohon unggah ulang surat resmi bertanda tangan dan berstempel.', $kuotaDesTKJ],
            ['Hesti Purwanti', 'hesti.purwanti@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jSI, JenjangEnum::S1, '123230055', StatusPendaftaranEnum::PerluPerbaikan, $now->copy()->subDays(4), $now->copy()->subDays(2), 'Scan transkrip nilai akademik terpotong pada bagian IPK kumulatif. Harap unggah scan dokumen utuh beresolusi tinggi.', $kuotaDesSI],
            ['Irfan Hakim', 'irfan.hakim@uin-suka.ac.id', 'UIN Sunan Kalijaga', $jIK, JenjangEnum::S1, '23102040019', StatusPendaftaranEnum::PerluPerbaikan, $now->copy()->subDays(3), $now->copy()->subDay(), 'Proposal magang belum menyertakan rencana topik atau pembagian tugas yang diharapkan. Mohon lengkapi sesuai template dinas.', $kuotaDesIK],

            // 10-12. Waiting List (Antrean Cadangan karena Kuota Jurusan Terisi Penuh)
            ['Jasmine Azzahra', 'jasmine.azzahra@mail.ugm.ac.id', 'Universitas Gadjah Mada', $jTI, JenjangEnum::S1, '23/502345/TK/56001', StatusPendaftaranEnum::WaitingList, $now->copy()->subDays(8), $now->copy()->subDays(5), 'Kuota umum jurusan Teknik Informatika telah terpenuhi. Anda ditempatkan pada antrean cadangan (Waiting List) prioritas #1.', $kuotaDesTI],
            ['Kurnia Sandi', 'kurnia.sandi@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jTI, JenjangEnum::S1, '23537144050', StatusPendaftaranEnum::WaitingList, $now->copy()->subDays(7), $now->copy()->subDays(4), 'Antrean cadangan (Waiting List) prioritas #2.', $kuotaDesTI],
            ['Lutfi Ardiansyah', 'lutfi.ardiansyah@smkn2depoksleman.sch.id', 'SMK Negeri 2 Depok Sleman', $jTKJ, JenjangEnum::Smk, '0075432199', StatusPendaftaranEnum::WaitingList, $now->copy()->subDays(9), $now->copy()->subDays(6), 'Antrean cadangan (Waiting List) jurusan TKJ prioritas #1.', $kuotaDesTKJ],

            // 13-14. Diterima (Lolos Seleksi Berkas, Menunggu Alokasi Penempatan Bidang oleh Admin)
            ['Mira Kartika', 'mira.kartika@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jSI, JenjangEnum::S1, '123230088', StatusPendaftaranEnum::Diterima, $now->copy()->subDays(10), $now->copy()->subDays(2), 'Selamat! Berkas terverifikasi memenuhi kriteria. Menunggu penetapan bidang dan pembimbing lapangan.', $kuotaDesSI],
            ['Nugroho Adi', 'nugroho.adi@uii.ac.id', 'Universitas Islam Indonesia', $jTI, JenjangEnum::S1, '23523099', StatusPendaftaranEnum::Diterima, $now->copy()->subDays(12), $now->copy()->subDays(3), 'Selamat! Berkas lolos verifikasi. Menunggu SK penetapan penempatan.', $kuotaDesTI],

            // 15-16. Ditolak (Alasan Resmi Verifikator)
            ['Pratama Wijaya', 'pratama.wijaya@mail.uajy.ac.id', 'Universitas Atma Jaya Yogyakarta', $jIK, JenjangEnum::S1, '230712001', StatusPendaftaranEnum::Ditolak, $now->copy()->subDays(14), $now->copy()->subDays(10), 'Kualifikasi proposal magang tidak linier dengan agenda kegiatan publikasi dinas pada periode yang diajukan.', $kuotaDesIK],
            ['Rina Marlina', 'rina.marlina@smkn1sleman.sch.id', 'SMK Negeri 1 Sleman', $jAP, JenjangEnum::Smk, '0089876540', StatusPendaftaranEnum::Ditolak, $now->copy()->subDays(15), $now->copy()->subDays(11), 'Surat izin magang dari pihak sekolah tidak menyertakan legalitas resmi dan kuota formasi administrasi telah penuh.', $kuotaDesAP],
        ];

        foreach ($pendaftarBaru as [$nama, $email, $kampus, $jur, $jenjang, $nim, $status, $diajukanAt, $diputuskanAt, $catatan, $kuota]) {
            $user = $buatPeserta($nama, $email, $kampus, $jur, $jenjang, $nim, true);

            $reg = Pendaftaran::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bulan_magang_id' => $bulanDesember->id,
                    'kuota_id' => $kuota->id,
                    'durasi_bulan' => 3,
                    'status' => $status,
                    'verifikator_id' => $diputuskanAt ? $admin->id : null,
                    'diajukan_at' => $diajukanAt,
                    'diputuskan_at' => $diputuskanAt,
                    'catatan' => $catatan,
                ]
            );

            $lampirkanBerkas($reg);
        }

        // =========================================================================
        // 9. COHORT D: EDGE CASES (MENGUNDURKAN DIRI & JALUR KHUSUS MBKM)
        // =========================================================================
        // Kasus Mengundurkan Diri
        $userMundur = $buatPeserta('Taufik Hidayat', 'taufik.hidayat@uii.ac.id', 'Universitas Islam Indonesia', $jTI, JenjangEnum::S1, '22523299', false);
        $regMundur = Pendaftaran::updateOrCreate(
            ['user_id' => $userMundur->id],
            [
                'bulan_magang_id' => $bulanNovember->id,
                'kuota_id' => $kuotaNovTI->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Dibatalkan,
                'catatan' => 'Peserta mengundurkan diri karena diterima pada program Magang Bersertifikat BUMN.',
                'diajukan_at' => $now->copy()->subMonths(1),
                'diputuskan_at' => $now->copy()->subDays(20),
            ]
        );
        Penempatan::updateOrCreate(
            ['peserta_id' => $userMundur->id],
            [
                'pendaftaran_id' => $regMundur->id,
                'kuota_id' => $kuotaNovTI->id,
                'bidang_id' => $bidangEgov->id,
                'pembimbing_id' => $pembimbingEgov1->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => $now->copy()->subDays(25)->toDateString(),
                'tgl_selesai' => $now->copy()->addMonths(2)->toDateString(),
                'tgl_nonaktif' => $now->copy()->subDays(10)->toDateString(),
                'status' => StatusPenempatanEnum::MengundurkanDiri,
            ]
        );

        // Kasus Jalur Khusus (Slot Khusus MBKM)
        $userKhusus1 = $buatPeserta('Zulfikar Ali', 'zulfikar.ali@mail.ugm.ac.id', 'Universitas Gadjah Mada', $jTI, JenjangEnum::S1, '22/499111/TK/55000', true);
        $regKhusus1 = Pendaftaran::updateOrCreate(
            ['user_id' => $userKhusus1->id],
            [
                'bulan_magang_id' => $bulanNovember->id,
                'kuota_id' => $kuotaNovTI->id,
                'durasi_bulan' => 4,
                'status' => StatusPendaftaranEnum::Diterima,
                'diajukan_at' => $now->copy()->subDays(20),
                'diputuskan_at' => $now->copy()->subDays(15),
                'catatan' => 'Diterima melalui jalur kemitraan Slot Khusus MBKM Mandiri Kampus UGM.',
            ]
        );
        Penempatan::updateOrCreate(
            ['peserta_id' => $userKhusus1->id],
            [
                'pendaftaran_id' => $regKhusus1->id,
                'kuota_id' => $kuotaNovTI->id,
                'slot_khusus_id' => $slotKhususMbkm->id,
                'bidang_id' => $bidangEgov->id,
                'pembimbing_id' => $pembimbingEgov1->id,
                'jalur' => JalurEnum::Khusus,
                'sumber' => SumberPenempatanEnum::Manual,
                'dibuat_oleh' => $superAdmin->id,
                'tgl_mulai' => '2026-11-01',
                'tgl_selesai' => '2027-02-28',
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );

        // =========================================================================
        // 10. AGENDA BIMBINGAN TERPADU (SELESAI & TERJADWAL)
        // =========================================================================
        $penempatanAktif = Penempatan::where('status', StatusPenempatanEnum::Aktif)->get();
        foreach ($penempatanAktif->take(8) as $idx => $p) {
            // Agenda yang sudah selesai
            AgendaBimbingan::updateOrCreate(
                ['penempatan_id' => $p->id, 'judul_kegiatan' => 'Briefing Awal & Workplan'],
                [
                    'pembimbing_id' => $p->pembimbing_id,
                    'tipe' => 'tatap_muka',
                    'tgl_bimbingan' => Carbon::parse($p->tgl_mulai)->addDays(3)->toDateString(),
                    'jam_mulai' => '09:00',
                    'lokasi_ruang' => 'Ruang Bimbingan Diskominfo',
                    'notulensi_arahan' => 'Peserta diarahkan untuk memahami SOP kedinasan, menyelesaikan modul pembelajaran internal, dan menyusun timeline mingguan.',
                    'status' => 'selesai',
                ]
            );

            // Agenda yang terjadwal minggu ini / depan
            AgendaBimbingan::updateOrCreate(
                ['penempatan_id' => $p->id, 'judul_kegiatan' => 'Evaluasi Progres Tengah Periode'],
                [
                    'pembimbing_id' => $p->pembimbing_id,
                    'tipe' => 'tatap_muka',
                    'tgl_bimbingan' => $now->copy()->addDays(rand(2, 7))->toDateString(),
                    'jam_mulai' => '10:30',
                    'lokasi_ruang' => 'Lab Komputer Lt. 2 Diskominfo Sleman',
                    'notulensi_arahan' => 'Mempersiapkan demonstrasi fitur modul, catatan kendala, dan draf bab 3 laporan magang.',
                    'status' => 'terjadwal',
                ]
            );
        }

        // =========================================================================
        // 11. AUDIT LOGS REALISTIS (30 HARI TERAKHIR)
        // Mengisi 15 Channel Audit Log Stream SuperAdmin
        // =========================================================================
        $auditSamples = [
            [TipeAuditEnum::Auth, 'auth-session', 'info', 'login_sukses', 'User', $superAdmin->id, 'Login SuperAdmin dari konsol utama.', 'POST', '/login', 200],
            [TipeAuditEnum::Auth, 'auth-session', 'info', 'login_sukses', 'User', $admin->id, 'Login Admin Verifikator Magang.', 'POST', '/login', 200],
            [TipeAuditEnum::Auth, 'auth-session', 'info', 'login_sukses', 'User', $pembimbingEgov1->id, 'Login Pembimbing Lapangan Aptika.', 'POST', '/login', 200],
            [TipeAuditEnum::Aksi, 'pendaftaran', 'info', 'pendaftaran_diajukan', 'Pendaftaran', 1, 'Peserta mengajukan formulir pendaftaran magang online.', 'POST', '/peserta/pendaftaran', 302],
            [TipeAuditEnum::Aksi, 'verifikasi-admin', 'info', 'terima_berkas', 'Pendaftaran', 1, 'Admin memverifikasi dan meloloskan berkas pendaftaran.', 'POST', '/admin/verifikasi/1/terima', 302],
            [TipeAuditEnum::Aksi, 'penempatan-mentor', 'info', 'tetapkan_penempatan', 'Penempatan', 1, 'Admin menempatkan peserta ke Bidang EGOV dan menugaskan Budi Santoso.', 'POST', '/admin/penempatan', 302],
            [TipeAuditEnum::Aksi, 'tugas-classroom', 'info', 'buat_tugas', 'Tugas', 1, 'Pembimbing memberikan tugas teknis integrasi API.', 'POST', '/pembimbing/tugas', 302],
            [TipeAuditEnum::Aksi, 'logbook-presensi', 'info', 'setujui_logbook', 'Logbook', 1, 'Pembimbing menyetujui logbook mingguan peserta.', 'POST', '/pembimbing/logbook/1/setujui', 302],
            [TipeAuditEnum::Aksi, 'penilaian-sk', 'info', 'simpan_nilai_final', 'Penilaian', 1, 'Pembimbing menginput nilai rubrik akhir dengan predikat A.', 'POST', '/pembimbing/penilaian', 302],
            [TipeAuditEnum::Aksi, 'kuota-periode', 'info', 'update_kuota', 'Kuota', 1, 'Admin memperbarui alokasi kuota jurusan Teknik Informatika periode Desember 2026.', 'PUT', '/admin/kuota/1', 200],
            [TipeAuditEnum::Aksi, 'survei-evaluasi', 'info', 'kirim_survei', 'SurveiKepuasan', 1, 'Peserta magang selesai mengisi instrumen evaluasi kepuasan IKM.', 'POST', '/peserta/survei', 302],
        ];

        for ($i = 0; $i < 40; $i++) {
            $sample = $auditSamples[$i % count($auditSamples)];
            $waktuLog = $now->copy()->subDays(rand(0, 28))->subHours(rand(1, 23))->subMinutes(rand(1, 59));

            try {
                AuditLog::create([
                    'request_id' => (string) Str::uuid(),
                    'user_id' => $sample[4] === 'User' ? $sample[5] : $admin->id,
                    'tipe' => $sample[0],
                    'channel' => $sample[1],
                    'severity' => $sample[2],
                    'aksi' => $sample[3],
                    'entitas' => $sample[4],
                    'entitas_id' => $sample[5] + $i,
                    'nilai_lama' => null,
                    'nilai_baru' => ['status' => 'updated', 'iteration' => $i],
                    'detail' => ['pesan' => $sample[6], 'log_index' => $i],
                    'metode' => $sample[7],
                    'rute' => $sample[8],
                    'status_http' => $sample[9],
                    'durasi_ms' => rand(25, 120),
                    'ip' => '192.168.1.'.rand(10, 250),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0',
                    'waktu' => $waktuLog,
                ]);
            } catch (\Throwable $e) {
                // Ignore duplicate or append restrictions
            }
        }

        // =========================================================================
        // 12. ROUTE METRICS & TELEMETRI LATENSI (60 ENTRI TERAKHIR)
        // Menghidupkan Grafik Canvas 2D SuperAdmin Health Telemetry
        // =========================================================================
        $routes = [
            ['GET', 'admin.dashboard', '/admin/dashboard', 200, 32.5, 14.2],
            ['GET', 'pembimbing.dashboard', '/pembimbing/dashboard', 200, 28.4, 12.8],
            ['GET', 'peserta.dashboard', '/peserta/dashboard', 200, 19.8, 11.5],
            ['GET', 'public.landing', '/', 200, 15.2, 9.8],
            ['POST', 'peserta.logbook.store', '/peserta/logbook', 302, 45.1, 15.0],
            ['GET', 'realtime.stream', '/realtime/stream', 200, 12.0, 8.4],
            ['GET', 'admin.kuota.index', '/admin/kuota', 200, 24.6, 12.0],
            ['GET', 'superadmin.health.index', '/superadmin/health-monitor', 200, 35.8, 16.5],
            ['GET', 'superadmin.audit.index', '/superadmin/audit-logs', 200, 42.0, 18.2],
        ];

        for ($k = 0; $k < 60; $k++) {
            $r = $routes[$k % count($routes)];
            try {
                RouteMetric::create([
                    'method' => $r[0],
                    'route_name' => $r[1],
                    'uri' => $r[2],
                    'status_code' => $r[3],
                    'duration_ms' => $r[4] + rand(-5, 15) + (rand(0, 99) / 100),
                    'memory_mb' => $r[5] + rand(-1, 3) + (rand(0, 99) / 100),
                    'user_id' => $admin->id,
                    'ip' => '127.0.0.1',
                    'created_at' => $now->copy()->subMinutes(60 - $k),
                ]);
            } catch (\Throwable $e) {
                // Ignore telemetry insert quirks
            }
        }

        // =========================================================================
        // 13. SINKRONISASI HITUNGAN TERISI PADA KUOTA
        // =========================================================================
        foreach (Kuota::all() as $k) {
            $countTerisi = Penempatan::where('kuota_id', $k->id)
                ->whereIn('status', [StatusPenempatanEnum::Aktif, StatusPenempatanEnum::Selesai])
                ->count();

            if ($countTerisi > 0) {
                $k->update([
                    'terisi' => min($k->jumlah, $countTerisi),
                ]);
            }
        }
    }
}
