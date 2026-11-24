<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RealPesertaAndArsipSeeder extends Seeder
{
    /**
     * Seed realistic interns (individual & group) and archived alumni with real photos.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');
        $now = Carbon::now('Asia/Jakarta');

        // Pastikan folder foto profil peserta tersedia
        Storage::disk('public')->makeDirectory('profil-peserta');

        // Master referensi
        $bidangEgov = Bidang::firstWhere('nama_bidang', 'like', '%E-Government%') ?? Bidang::first();
        $bidangIkp = Bidang::firstWhere('nama_bidang', 'like', '%Informasi dan Komunikasi%') ?? Bidang::find(2) ?? $bidangEgov;
        $bidangIti = Bidang::firstWhere('nama_bidang', 'like', '%Infrastruktur%') ?? Bidang::find(3) ?? $bidangEgov;
        $bidangSekretariat = Bidang::firstWhere('nama_bidang', 'like', '%Sekretariat%') ?? Bidang::find(4) ?? $bidangEgov;

        $pembimbingEgov = User::where('role', RoleEnum::Pembimbing)->where('bidang_id', $bidangEgov->id)->first()
            ?? User::where('role', RoleEnum::Pembimbing)->first();
        $pembimbingIkp = User::where('role', RoleEnum::Pembimbing)->where('bidang_id', $bidangIkp->id)->first() ?? $pembimbingEgov;
        $pembimbingIti = User::where('role', RoleEnum::Pembimbing)->where('bidang_id', $bidangIti->id)->first() ?? $pembimbingEgov;
        $pembimbingSekretariat = User::where('role', RoleEnum::Pembimbing)->where('bidang_id', $bidangSekretariat->id)->first() ?? $pembimbingEgov;

        $jurusanTI = Jurusan::firstWhere('nama_jurusan', 'Teknik Informatika') ?? Jurusan::first();
        $jurusanSI = Jurusan::firstWhere('nama_jurusan', 'Sistem Informasi') ?? Jurusan::first();
        $jurusanTKJ = Jurusan::firstWhere('nama_jurusan', 'like', '%TKJ%') ?? Jurusan::first();
        $jurusanIlkom = Jurusan::firstWhere('nama_jurusan', 'Ilmu Komunikasi') ?? Jurusan::first();
        $jurusanArsip = Jurusan::firstWhere('nama_jurusan', 'like', '%Kearsipan%') ?? Jurusan::first();

        $bulanAktif = BulanMagang::where('aktif', true)->first() ?? BulanMagang::first();
        $bulanLalu = BulanMagang::where('aktif', false)->first() ?? $bulanAktif;

        $kuotaAktif = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulanAktif->id, 'jurusan_id' => $jurusanTI->id],
            ['jumlah' => 30, 'terisi' => 15, 'aktif' => true]
        );

        $kuotaLalu = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulanLalu->id, 'jurusan_id' => $jurusanTI->id],
            ['jumlah' => 20, 'terisi' => 20, 'aktif' => false]
        );

        // 1. UPDATE SELURUH AKUN PESERTA EKSISTING DENGAN FOTO POTRET REALISTIS
        $existingPeserta = User::where('role', RoleEnum::Peserta)->get();
        $maleNames = ['aditya', 'budi', 'chandra', 'eko', 'gilang', 'indra', 'joko', 'lukman', 'bagas', 'rizky', 'dimas', 'farhan', 'fajar', 'ilham', 'taufik', 'rian'];

        foreach ($existingPeserta as $idx => $pesertaUser) {
            $isMale = false;
            foreach ($maleNames as $mn) {
                if (stripos($pesertaUser->nama, $mn) !== false) {
                    $isMale = true;
                    break;
                }
            }

            $photoNum = ($idx % 12) + 1;
            $photoPath = $isMale ? "profil-peserta/portrait_men_{$photoNum}.jpg" : "profil-peserta/portrait_women_{$photoNum}.jpg";

            $pesertaUser->foto = $photoPath;
            $pesertaUser->save();

            PesertaProfil::updateOrCreate(
                ['user_id' => $pesertaUser->id],
                [
                    'foto' => $photoPath,
                    'jurusan_id' => $pesertaUser->profil?->jurusan_id ?? $jurusanTI->id,
                    'institusi' => $pesertaUser->profil?->institusi ?? 'Universitas Gadjah Mada',
                    'nim_nisn' => $pesertaUser->profil?->nim_nisn ?? '22/498711/TK/54120',
                    'jenjang' => $pesertaUser->profil?->jenjang ?? JenjangEnum::S1,
                    'no_hp' => $pesertaUser->profil?->no_hp ?? '081223344550',
                    'alamat' => $pesertaUser->profil?->alamat ?? 'Kabupaten Sleman, D.I. Yogyakarta',
                    'nama_kontak_darurat' => 'Bambang Sudarmanto',
                    'no_kontak_darurat' => '081398765432',
                    'hubungan_kontak_darurat' => 'Orang Tua / Wali',
                ]
            );
        }

        // 2. SEED KELOMPOK MAGANG AKTIF LENGKAP DENGAN KETUA, ANGGOTA & PENUGASAN KELOMPOK
        $timSmartCity = [
            'nama_kelompok' => 'Tim Pengembang Portal Smart City Sleman',
            'bidang' => $bidangEgov,
            'pembimbing' => $pembimbingEgov,
            'jurusan' => $jurusanTI,
            'ketua' => [
                'nama' => 'Anisa Rahmawati',
                'email' => 'anisa.rahmawati@gmail.com',
                'nim' => '22/497120/TK/54001',
                'institusi' => 'Universitas Gadjah Mada',
                'foto' => 'profil-peserta/portrait_women_1.jpg',
            ],
            'anggota' => [
                [
                    'nama' => 'Bagas Saputra',
                    'email' => 'bagas.saputra@gmail.com',
                    'nim' => '22/497121/TK/54002',
                    'institusi' => 'Universitas Gadjah Mada',
                    'foto' => 'profil-peserta/portrait_men_1.jpg',
                ],
                [
                    'nama' => 'Citra Dewi Lestari',
                    'email' => 'citra.dewi@gmail.com',
                    'nim' => '22/497122/TK/54003',
                    'institusi' => 'Universitas Gadjah Mada',
                    'foto' => 'profil-peserta/portrait_women_2.jpg',
                ],
            ],
        ];

        // Buat user ketua
        $ketuaUser = User::updateOrCreate(
            ['email' => $timSmartCity['ketua']['email']],
            [
                'nama' => $timSmartCity['ketua']['nama'],
                'password' => $defaultPassword,
                'password_awal' => 'Password123!',
                'role' => RoleEnum::Peserta,
                'status_aktif' => true,
                'foto' => $timSmartCity['ketua']['foto'],
                'email_verified_at' => $now,
                'must_change_password' => false,
            ]
        );
        PesertaProfil::updateOrCreate(
            ['user_id' => $ketuaUser->id],
            [
                'nim_nisn' => $timSmartCity['ketua']['nim'],
                'institusi' => $timSmartCity['ketua']['institusi'],
                'jurusan_id' => $jurusanTI->id,
                'jenjang' => JenjangEnum::S1,
                'no_hp' => '081234567891',
                'alamat' => 'Sleman, D.I. Yogyakarta',
                'foto' => $timSmartCity['ketua']['foto'],
                'nama_kontak_darurat' => 'Raharjo',
                'no_kontak_darurat' => '081298765401',
                'hubungan_kontak_darurat' => 'Orang Tua',
            ]
        );

        $kelompokModel = KelompokMagang::updateOrCreate(
            ['nama_kelompok' => $timSmartCity['nama_kelompok']],
            ['ketua_user_id' => $ketuaUser->id]
        );

        // Pendaftaran kelompok
        $pendaftaranKelompok = Pendaftaran::updateOrCreate(
            ['user_id' => $ketuaUser->id, 'bulan_magang_id' => $bulanAktif->id],
            [
                'kuota_id' => $kuotaAktif->id,
                'tipe' => 'kelompok',
                'kelompok_magang_id' => $kelompokModel->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diterima,
                'diajukan_at' => $now->copy()->subDays(20),
                'diputuskan_at' => $now->copy()->subDays(15),
                'catatan' => 'Pendaftaran kelompok disetujui untuk penempatan Bidang E-Government.',
            ]
        );

        // Penempatan ketua
        $penempatanKetua = Penempatan::updateOrCreate(
            ['peserta_id' => $ketuaUser->id],
            [
                'pendaftaran_id' => $pendaftaranKelompok->id,
                'kuota_id' => $kuotaAktif->id,
                'bidang_id' => $bidangEgov->id,
                'pembimbing_id' => $pembimbingEgov->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => $now->copy()->subDays(14)->toDateString(),
                'tgl_selesai' => $now->copy()->addDays(76)->toDateString(),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );

        $kelompokModel->anggota()->delete();
        $penempatanAnggotaList = [$penempatanKetua];

        foreach ($timSmartCity['anggota'] as $ang) {
            $anggotaUser = User::updateOrCreate(
                ['email' => $ang['email']],
                [
                    'nama' => $ang['nama'],
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::Peserta,
                    'status_aktif' => true,
                    'foto' => $ang['foto'],
                    'email_verified_at' => $now,
                    'must_change_password' => false,
                ]
            );
            PesertaProfil::updateOrCreate(
                ['user_id' => $anggotaUser->id],
                [
                    'nim_nisn' => $ang['nim'],
                    'institusi' => $ang['institusi'],
                    'jurusan_id' => $jurusanTI->id,
                    'jenjang' => JenjangEnum::S1,
                    'no_hp' => '08123456789'.rand(2, 9),
                    'alamat' => 'Sleman, D.I. Yogyakarta',
                    'foto' => $ang['foto'],
                    'nama_kontak_darurat' => 'Wali Murid',
                    'no_kontak_darurat' => '081298765402',
                    'hubungan_kontak_darurat' => 'Orang Tua',
                ]
            );

            KelompokAnggota::create([
                'kelompok_magang_id' => $kelompokModel->id,
                'user_id' => $anggotaUser->id,
                'nama' => $ang['nama'],
                'nim_nisn' => $ang['nim'],
                'no_hp' => '081234567899',
                'email' => $ang['email'],
            ]);

            $penempatanAnggota = Penempatan::updateOrCreate(
                ['peserta_id' => $anggotaUser->id],
                [
                    'pendaftaran_id' => $pendaftaranKelompok->id,
                    'kuota_id' => $kuotaAktif->id,
                    'bidang_id' => $bidangEgov->id,
                    'pembimbing_id' => $pembimbingEgov->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'tgl_mulai' => $now->copy()->subDays(14)->toDateString(),
                    'tgl_selesai' => $now->copy()->addDays(76)->toDateString(),
                    'status' => StatusPenempatanEnum::Aktif,
                ]
            );
            $penempatanAnggotaList[] = $penempatanAnggota;
        }

        // Tugas Kelompok 1: Sedang pengerjaan (proses & perlu review)
        $tugasKelompok1 = Tugas::updateOrCreate(
            ['judul' => 'Pengembangan Endpoint REST API Layanan Mandiri Sleman'],
            [
                'penempatan_id' => $penempatanKetua->id,
                'tipe' => 'kelompok',
                'kelompok_magang_id' => $kelompokModel->id,
                'deskripsi' => 'Rancang dan buat endpoint autentikasi, pengambilan profil kependudukan, serta dokumentasi Swagger/OpenAPI bersama seluruh anggota tim.',
                'kategori' => 'Backend Development',
                'kompleksitas' => KompleksitasEnum::Tinggi,
                'deadline' => $now->copy()->addDays(7)->toDateString(),
                'status' => StatusTugasEnum::Proses,
                'status_publikasi' => 'terbit',
                'jadwal_publikasi' => $now->copy()->subDays(10),
            ]
        );

        // Pasang pivot tugas_peserta untuk seluruh anggota kelompok
        DB::table('tugas_peserta')->where('tugas_id', $tugasKelompok1->id)->delete();
        foreach ($penempatanAnggotaList as $pItem) {
            DB::table('tugas_peserta')->insert([
                'tugas_id' => $tugasKelompok1->id,
                'penempatan_id' => $pItem->id,
                'user_id' => $pItem->peserta_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Buat progres log yang menunggu review pembimbing
        ProgresLog::updateOrCreate(
            ['tugas_id' => $tugasKelompok1->id, 'catatan' => 'Modul autentikasi JWT dan schema database kependudukan selesai diimplementasikan, siap ditinjau.'],
            [
                'tgl_lapor' => $now->copy()->subDays(1)->toDateString(),
                'status_review' => StatusReviewProgresEnum::Menunggu,
                'link' => 'https://github.com/diskominfo-sleman/smartcity-api',
            ]
        );

        // Tugas Kelompok 2: Selesai
        $tugasKelompok2 = Tugas::updateOrCreate(
            ['judul' => 'Analisis Arsitektur Layanan & Penyusunan Dokumen SRS Tim'],
            [
                'penempatan_id' => $penempatanKetua->id,
                'tipe' => 'kelompok',
                'kelompok_magang_id' => $kelompokModel->id,
                'deskripsi' => 'Pelajari kebutuhan integrasi sistem antar dinas di Pemkab Sleman dan susun dokumen spesifikasi kebutuhan sistem.',
                'kategori' => 'Analisis & Riset',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'deadline' => $now->copy()->subDays(2)->toDateString(),
                'status' => StatusTugasEnum::Selesai,
                'status_publikasi' => 'terbit',
                'jadwal_publikasi' => $now->copy()->subDays(14),
            ]
        );
        DB::table('tugas_peserta')->where('tugas_id', $tugasKelompok2->id)->delete();
        foreach ($penempatanAnggotaList as $pItem) {
            DB::table('tugas_peserta')->insert([
                'tugas_id' => $tugasKelompok2->id,
                'penempatan_id' => $pItem->id,
                'user_id' => $pItem->peserta_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        ProgresLog::updateOrCreate(
            ['tugas_id' => $tugasKelompok2->id, 'catatan' => 'Dokumen SRS dan Diagram Usecase final telah disetujui pembimbing lapangan.'],
            [
                'tgl_lapor' => $now->copy()->subDays(3)->toDateString(),
                'status_review' => StatusReviewProgresEnum::Diterima,
                'feedback' => 'Analisis sudah sangat matang dan terstruktur rapi. Lanjutkan ke tahap implementasi backend.',
                'link' => 'https://drive.google.com/file/d/srs-smartcity-final/view',
            ]
        );

        // 3. SEED PESERTA INDIVIDU AKTIF DENGAN TUGAS & PROGRES BERAGAM
        $individuAktif = [
            [
                'nama' => 'Aditya Pratama',
                'email' => 'aditya.pratama@gmail.com',
                'bidang' => $bidangEgov,
                'pembimbing' => $pembimbingEgov,
                'foto' => 'profil-peserta/portrait_men_2.jpg',
                'jurusan' => $jurusanTI,
                'institusi' => 'Universitas Amikom Yogyakarta',
                'nim' => '22.11.4920',
                'tugas' => [
                    [
                        'judul' => 'Slicing & Integrasi Frontend Dashboard Peserta Magang',
                        'kategori' => 'Frontend Development',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'status' => StatusTugasEnum::Proses,
                        'deadline' => $now->copy()->addDays(5)->toDateString(),
                        'progres' => 'Pengerjaan komponen kartu statistik dan tab filter classroom selesai 80%.',
                        'review' => StatusReviewProgresEnum::Menunggu,
                    ],
                    [
                        'judul' => 'Penyusunan Desain Mockup UI/UX Portal MagKom',
                        'kategori' => 'UI/UX Design',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'status' => StatusTugasEnum::Selesai,
                        'deadline' => $now->copy()->subDays(5)->toDateString(),
                        'progres' => 'Seluruh rancangan Figma desain sistem dan palet warna telah tuntas.',
                        'review' => StatusReviewProgresEnum::Diterima,
                    ],
                ],
            ],
            [
                'nama' => 'Bunga Citra Lestari',
                'email' => 'bunga.citra@gmail.com',
                'bidang' => $bidangIkp,
                'pembimbing' => $pembimbingIkp,
                'foto' => 'profil-peserta/portrait_women_3.jpg',
                'jurusan' => $jurusanIlkom,
                'institusi' => 'UPN "Veteran" Yogyakarta',
                'nim' => '153210045',
                'tugas' => [
                    [
                        'judul' => 'Produksi Konten Publikasi Edukasi Siber Pemkab Sleman',
                        'kategori' => 'Media & Publikasi',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'status' => StatusTugasEnum::Proses,
                        'deadline' => $now->copy()->addDays(3)->toDateString(),
                        'progres' => 'Draf infografis keamanan kata sandi dan poster literasi digital tahap revisi.',
                        'review' => StatusReviewProgresEnum::Menunggu,
                    ],
                ],
            ],
            [
                'nama' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@gmail.com',
                'bidang' => $bidangIti,
                'pembimbing' => $pembimbingIti,
                'foto' => 'profil-peserta/portrait_men_3.jpg',
                'jurusan' => $jurusanTKJ,
                'institusi' => 'SMKN 2 Depok Sleman',
                'nim' => '0068912301',
                'tugas' => [
                    [
                        'judul' => 'Konfigurasi VLAN & Manajemen Bandwidth Ruang Server Kominfo',
                        'kategori' => 'Jaringan & Server',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'status' => StatusTugasEnum::Proses,
                        'deadline' => $now->copy()->addDays(8)->toDateString(),
                        'progres' => 'Pengujian kabel patch cord dan mapping port switch selesai.',
                        'review' => null,
                    ],
                ],
            ],
        ];

        foreach ($individuAktif as $ind) {
            $userInd = User::updateOrCreate(
                ['email' => $ind['email']],
                [
                    'nama' => $ind['nama'],
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::Peserta,
                    'status_aktif' => true,
                    'foto' => $ind['foto'],
                    'email_verified_at' => $now,
                    'must_change_password' => false,
                ]
            );
            PesertaProfil::updateOrCreate(
                ['user_id' => $userInd->id],
                [
                    'nim_nisn' => $ind['nim'],
                    'institusi' => $ind['institusi'],
                    'jurusan_id' => $ind['jurusan']->id,
                    'jenjang' => JenjangEnum::S1,
                    'no_hp' => '08129876541'.rand(1, 9),
                    'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                    'foto' => $ind['foto'],
                    'nama_kontak_darurat' => 'Keluarga Peserta',
                    'no_kontak_darurat' => '081298765499',
                    'hubungan_kontak_darurat' => 'Orang Tua',
                ]
            );

            $penInd = Penempatan::updateOrCreate(
                ['peserta_id' => $userInd->id],
                [
                    'kuota_id' => $kuotaAktif->id,
                    'bidang_id' => $ind['bidang']->id,
                    'pembimbing_id' => $ind['pembimbing']->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'tgl_mulai' => $now->copy()->subDays(10)->toDateString(),
                    'tgl_selesai' => $now->copy()->addDays(80)->toDateString(),
                    'status' => StatusPenempatanEnum::Aktif,
                ]
            );

            // Logbook mingguan
            Logbook::updateOrCreate(
                ['penempatan_id' => $penInd->id, 'minggu_ke' => 1],
                [
                    'tanggal' => $now->copy()->subDays(7)->toDateString(),
                    'kegiatan' => 'Orientasi tugas bidang dinas dan pengenalan alur kerja operasional magang.',
                    'status' => 'disetujui',
                    'catatan_pembimbing' => 'Bagus, terus pertahankan kedisiplinan dan komunikasi aktif.',
                ]
            );

            foreach ($ind['tugas'] as $tData) {
                $tugasInd = Tugas::updateOrCreate(
                    ['penempatan_id' => $penInd->id, 'judul' => $tData['judul']],
                    [
                        'tipe' => 'individu',
                        'deskripsi' => 'Tugas mandiri peserta magang di bawah bimbingan pembimbing lapangan.',
                        'kategori' => $tData['kategori'],
                        'kompleksitas' => $tData['kompleksitas'],
                        'deadline' => $tData['deadline'],
                        'status' => $tData['status'],
                        'status_publikasi' => 'terbit',
                        'jadwal_publikasi' => $now->copy()->subDays(5),
                    ]
                );

                if (! empty($tData['progres'])) {
                    ProgresLog::updateOrCreate(
                        ['tugas_id' => $tugasInd->id],
                        [
                            'catatan' => $tData['progres'],
                            'tgl_lapor' => $now->copy()->subDays(2)->toDateString(),
                            'status_review' => $tData['review'] ?? StatusReviewProgresEnum::Menunggu,
                            'feedback' => $tData['review'] === StatusReviewProgresEnum::Diterima ? 'Hasil kerja memuaskan dan memenuhi standar dinas.' : null,
                        ]
                    );
                }
            }
        }

        // 4. SEED ARSIP PESERTA (ALUMNI SELESAI, NONAKTIF, MENGUNDURKAN DIRI) DENGAN TAHUN 2026 & 2025
        $alumniData = [
            // Status: SELESAI (Alumni Lulus Magang)
            [
                'nama' => 'Rizky Ramadhan, S.Kom',
                'email' => 'rizky.ramadhan@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_men_4.jpg',
                'nim' => '21/473821/TK/52110',
                'institusi' => 'Universitas Gadjah Mada',
                'jurusan' => $jurusanTI,
                'bidang' => $bidangEgov,
                'pembimbing' => $pembimbingEgov,
                'tgl_mulai' => '2026-03-01',
                'tgl_selesai' => '2026-06-30',
                'tgl_ditetapkan_selesai' => '2026-06-30',
                'status' => StatusPenempatanEnum::Selesai,
                'status_user' => true,
                'file_surat_selesai' => 'surat-selesai/surat_keterangan_selesai_rizky.pdf',
                'tugas_selesai' => [
                    'Implementasi Modul Sinkronisasi API Satu Data Sleman',
                    'Dokumentasi Panduan Teknis & Pengujian Keamanan Web MagKom',
                ],
                'logbook' => 'Menyelesaikan seluruh integrasi sistem dan serah terima dokumentasi teknis akhir kepada pembimbing.',
            ],
            [
                'nama' => 'Nabila Syifa Putri',
                'email' => 'nabila.syifa@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_women_4.jpg',
                'nim' => '153210088',
                'institusi' => 'UPN "Veteran" Yogyakarta',
                'jurusan' => $jurusanIlkom,
                'bidang' => $bidangIkp,
                'pembimbing' => $pembimbingIkp,
                'tgl_mulai' => '2026-04-01',
                'tgl_selesai' => '2026-07-31',
                'tgl_ditetapkan_selesai' => '2026-07-31',
                'status' => StatusPenempatanEnum::Selesai,
                'status_user' => true,
                'file_surat_selesai' => 'surat-selesai/surat_keterangan_selesai_nabila.pdf',
                'tugas_selesai' => [
                    'Penyusunan Narasi Berita Liputan Pelayanan Publik Diskominfo',
                    'Pengelolaan Agenda Siaran Pers & Kemitraan Jurnalistik',
                ],
                'logbook' => 'Peliputan kegiatan peringatan HUT Sleman dan publikasi rilis media cetak dan online.',
            ],
            [
                'nama' => 'Dimas Wahyudi',
                'email' => 'dimas.wahyudi@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_men_5.jpg',
                'nim' => '20/458912/SV/18234',
                'institusi' => 'Universitas Negeri Yogyakarta',
                'jurusan' => $jurusanTKJ,
                'bidang' => $bidangIti,
                'pembimbing' => $pembimbingIti,
                'tgl_mulai' => '2025-08-01',
                'tgl_selesai' => '2025-11-30',
                'tgl_ditetapkan_selesai' => '2025-11-30',
                'status' => StatusPenempatanEnum::Selesai,
                'status_user' => true,
                'file_surat_selesai' => 'surat-selesai/surat_keterangan_selesai_dimas.pdf',
                'tugas_selesai' => [
                    'Migrasi Server Backup Cloud Pemkab Sleman ke Cluster Baru',
                    'Audit Topologi Jaringan Fiber Optik OPD Lingkup Sleman',
                ],
                'logbook' => 'Verifikasi kestabilan koneksi FO dan backup data berkala ruang data center.',
            ],
            [
                'nama' => 'Putri Maharani',
                'email' => 'putri.maharani@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_women_5.jpg',
                'nim' => '200411100089',
                'institusi' => 'UIN Sunan Kalijaga Yogyakarta',
                'jurusan' => $jurusanArsip,
                'bidang' => $bidangSekretariat,
                'pembimbing' => $pembimbingSekretariat,
                'tgl_mulai' => '2025-09-01',
                'tgl_selesai' => '2025-12-31',
                'tgl_ditetapkan_selesai' => '2025-12-31',
                'status' => StatusPenempatanEnum::Selesai,
                'status_user' => true,
                'file_surat_selesai' => 'surat-selesai/surat_keterangan_selesai_putri.pdf',
                'tugas_selesai' => [
                    'Digitalisasi dan Penataan Arsip Tata Usaha Kominfo 2024-2025',
                ],
                'logbook' => 'Penyusunan daftar berkas inaktif dan input metadata pada sistem kearsipan dinas.',
            ],

            // Status: NONAKTIF (Purna Retensi / Akun Dikunci)
            [
                'nama' => 'Farhan Aditya Pratama',
                'email' => 'farhan.aditya@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_men_6.jpg',
                'nim' => '19.11.3200',
                'institusi' => 'Universitas Amikom Yogyakarta',
                'jurusan' => $jurusanTI,
                'bidang' => $bidangIti,
                'pembimbing' => $pembimbingIti,
                'tgl_mulai' => '2025-02-01',
                'tgl_selesai' => '2025-05-31',
                'tgl_ditetapkan_selesai' => '2025-05-31',
                'tgl_nonaktif' => '2025-06-30',
                'status' => StatusPenempatanEnum::Nonaktif,
                'status_user' => false,
                'file_surat_selesai' => 'surat-selesai/surat_keterangan_selesai_farhan.pdf',
                'tugas_selesai' => [
                    'Peremajaan Antivirus & Patching Sistem Operasi PC Staf Dinas',
                ],
                'logbook' => 'Laporan rekapitulasi maintenance hardware dan penutupan akun bimbingan.',
            ],
            [
                'nama' => 'Maya Safira Kusuma',
                'email' => 'maya.safira@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_women_6.jpg',
                'nim' => '20190801044',
                'institusi' => 'Universitas Islam Indonesia',
                'jurusan' => $jurusanSI,
                'bidang' => $bidangIkp,
                'pembimbing' => $pembimbingIkp,
                'tgl_mulai' => '2025-03-01',
                'tgl_selesai' => '2025-06-30',
                'tgl_ditetapkan_selesai' => '2025-06-30',
                'tgl_nonaktif' => '2025-07-31',
                'status' => StatusPenempatanEnum::Nonaktif,
                'status_user' => false,
                'file_surat_selesai' => 'surat-selesai/surat_keterangan_selesai_maya.pdf',
                'tugas_selesai' => [
                    'Riset Kepuasan Masyarakat terhadap Layanan Pengaduan Sleman',
                ],
                'logbook' => 'Penyusunan laporan analisis statistik kuesioner aduan publik.',
            ],

            // Status: MENGUNDURKAN DIRI
            [
                'nama' => 'Gilang Pamungkas',
                'email' => 'gilang.pamungkas@alumni.magkom.go.id',
                'foto' => 'profil-peserta/portrait_men_7.jpg',
                'nim' => '22/499811/SV/21990',
                'institusi' => 'Universitas Gadjah Mada',
                'jurusan' => $jurusanTI,
                'bidang' => $bidangEgov,
                'pembimbing' => $pembimbingEgov,
                'tgl_mulai' => '2026-02-01',
                'tgl_selesai' => '2026-03-15',
                'tgl_ditetapkan_selesai' => null,
                'status' => StatusPenempatanEnum::MengundurkanDiri,
                'status_user' => false,
                'file_surat_selesai' => null,
                'tugas_selesai' => [
                    'Studi Literatur Kerangka Kerja Microservices Smart Governance',
                ],
                'logbook' => 'Serah terima inventaris dan penyelesaian administrasi pengunduran diri karena lolos beasiswa luar negeri.',
            ],
        ];

        foreach ($alumniData as $alm) {
            $userAlm = User::updateOrCreate(
                ['email' => $alm['email']],
                [
                    'nama' => $alm['nama'],
                    'password' => $defaultPassword,
                    'password_awal' => 'Password123!',
                    'role' => RoleEnum::Peserta,
                    'status_aktif' => $alm['status_user'],
                    'foto' => $alm['foto'],
                    'email_verified_at' => $now,
                    'must_change_password' => false,
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $userAlm->id],
                [
                    'nim_nisn' => $alm['nim'],
                    'institusi' => $alm['institusi'],
                    'jurusan_id' => $alm['jurusan']->id,
                    'jenjang' => JenjangEnum::S1,
                    'no_hp' => '0812987654'.rand(10, 99),
                    'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                    'foto' => $alm['foto'],
                    'nama_kontak_darurat' => 'Orang Tua / Wali',
                    'no_kontak_darurat' => '081398765432',
                    'hubungan_kontak_darurat' => 'Orang Tua',
                ]
            );

            $penAlm = Penempatan::updateOrCreate(
                ['peserta_id' => $userAlm->id],
                [
                    'kuota_id' => $kuotaLalu->id,
                    'bidang_id' => $alm['bidang']->id,
                    'pembimbing_id' => $alm['pembimbing']->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'tgl_mulai' => $alm['tgl_mulai'],
                    'tgl_selesai' => $alm['tgl_selesai'],
                    'tgl_ditetapkan_selesai' => $alm['tgl_ditetapkan_selesai'] ?? null,
                    'tgl_nonaktif' => $alm['tgl_nonaktif'] ?? null,
                    'status' => $alm['status'],
                    'file_surat_selesai' => $alm['file_surat_selesai'] ?? null,
                    'tgl_upload_surat_selesai' => $alm['file_surat_selesai'] ? Carbon::parse($alm['tgl_selesai'])->subDays(2) : null,
                ]
            );

            // Logbook historis
            Logbook::updateOrCreate(
                ['penempatan_id' => $penAlm->id, 'minggu_ke' => 1],
                [
                    'tanggal' => Carbon::parse($alm['tgl_mulai'])->addDays(7)->toDateString(),
                    'kegiatan' => 'Pekan awal: Orientasi tupoksi dinas dan penyusunan rencana aksi magang.',
                    'status' => 'disetujui',
                    'catatan_pembimbing' => 'Rencana aksi disetujui.',
                ]
            );
            Logbook::updateOrCreate(
                ['penempatan_id' => $penAlm->id, 'minggu_ke' => 4],
                [
                    'tanggal' => $alm['tgl_selesai'],
                    'kegiatan' => $alm['logbook'],
                    'status' => 'disetujui',
                    'catatan_pembimbing' => 'Seluruh kewajiban bimbingan telah terpenuhi dengan sangat baik.',
                ]
            );

            // Tugas historis
            foreach ($alm['tugas_selesai'] as $tJudul) {
                $tgs = Tugas::updateOrCreate(
                    ['penempatan_id' => $penAlm->id, 'judul' => $tJudul],
                    [
                        'tipe' => 'individu',
                        'deskripsi' => 'Penugasan historis selama masa program magang berjalan.',
                        'kategori' => 'Tugas Utama',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'deadline' => Carbon::parse($alm['tgl_selesai'])->subDays(5)->toDateString(),
                        'status' => StatusTugasEnum::Selesai,
                        'status_publikasi' => 'terbit',
                        'jadwal_publikasi' => Carbon::parse($alm['tgl_mulai'])->addDays(10),
                    ]
                );

                ProgresLog::updateOrCreate(
                    ['tugas_id' => $tgs->id],
                    [
                        'catatan' => 'Pengerjaan telah tuntas 100% dan hasil diserahkan ke pembimbing lapangan.',
                        'tgl_lapor' => Carbon::parse($alm['tgl_selesai'])->subDays(6)->toDateString(),
                        'status_review' => StatusReviewProgresEnum::Diterima,
                        'feedback' => 'Hasil tugas luar biasa baik dan langsung dapat digunakan dinas.',
                    ]
                );
            }
        }
    }
}
