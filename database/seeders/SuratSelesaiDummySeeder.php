<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SuratSelesaiDummySeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data peserta individu dan rombongan tim selesai magang
     * lengkap dengan foto asli, metadata profil, dan file surat selesai.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');

        // 1. Pastikan berkas contoh PDF tersimpan di disk lokal (storage/app/surat-selesai/)
        $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000108 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF\n";

        Storage::disk('local')->put('surat-selesai/surat_selesai_dimas.pdf', $pdfContent);
        Storage::disk('local')->put('surat-selesai/surat_selesai_putri.pdf', $pdfContent);
        Storage::disk('local')->put('surat-selesai/surat_selesai_smartcity.pdf', $pdfContent);
        Storage::disk('local')->put('surat-selesai/F4092fMmDh4B3mBlooH4KfVtuCu3s3nLhXU4EkMo.pdf', $pdfContent);

        // Ambil / buat master referensi
        $bidangAptika = Bidang::where('nama_bidang', 'like', '%Aptika%')->orWhere('nama_bidang', 'like', '%Aplikasi%')->first()
            ?? Bidang::firstOrCreate(['nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)'], ['deskripsi' => 'Bidang Aptika', 'aktif' => true]);

        $bidangIkp = Bidang::where('nama_bidang', 'like', '%IKP%')->orWhere('nama_bidang', 'like', '%Komunikasi%')->first()
            ?? Bidang::firstOrCreate(['nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)'], ['deskripsi' => 'Bidang IKP', 'aktif' => true]);

        $bidangIti = Bidang::where('nama_bidang', 'like', '%ITI%')->orWhere('nama_bidang', 'like', '%Infrastruktur%')->first()
            ?? Bidang::firstOrCreate(['nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)'], ['deskripsi' => 'Bidang ITI', 'aktif' => true]);

        $jurusan = Jurusan::first() ?? Jurusan::create(['nama_jurusan' => 'Teknik Informatika', 'kategori' => 'informatika', 'aktif' => true]);

        $pembimbingAptika = User::where('role', RoleEnum::Pembimbing)->first()
            ?? User::create(['nama' => 'Budi Santoso, S.Kom.', 'email' => 'pembimbing.aptika@magkom.test', 'password' => $defaultPassword, 'role' => RoleEnum::Pembimbing, 'email_verified_at' => now()]);

        $pembimbingIkp = User::where('role', RoleEnum::Pembimbing)->where('id', '!=', $pembimbingAptika->id)->first()
            ?? User::create(['nama' => 'Rina Wijayanti, M.Kom.', 'email' => 'pembimbing.ikp@magkom.test', 'password' => $defaultPassword, 'role' => RoleEnum::Pembimbing, 'email_verified_at' => now()]);

        $pembimbingIti = User::where('role', RoleEnum::Pembimbing)->where('nama', 'like', '%Sri Wahyuni%')->first()
            ?? User::where('role', RoleEnum::Pembimbing)->whereNotIn('id', [$pembimbingAptika->id, $pembimbingIkp->id])->first()
            ?? User::create(['nama' => 'Sri Wahyuni, S.I.Kom', 'email' => 'sri.wahyuni@magkom.test', 'password' => $defaultPassword, 'role' => RoleEnum::Pembimbing, 'email_verified_at' => now()]);

        $bulanMagang = BulanMagang::first() ?? BulanMagang::create(['nama_bulan' => 'Agustus - November 2026', 'bulan_mulai' => now()->subMonths(3)->startOfMonth(), 'tgl_buka' => now()->subMonths(4), 'tgl_tutup' => now()->subMonths(3), 'aktif' => true]);
        $kuota = Kuota::first() ?? Kuota::create(['bulan_magang_id' => $bulanMagang->id, 'jurusan_id' => $jurusan->id, 'jumlah' => 20, 'terisi' => 10, 'aktif' => true]);

        $adminUser = User::where('role', RoleEnum::Admin)->first() ?? User::create(['nama' => 'Admin Verifikator', 'email' => 'admin@magkom.test', 'password' => $defaultPassword, 'role' => RoleEnum::Admin, 'email_verified_at' => now()]);

        // =========================================================================
        // 2. PESERTA INDIVIDU (5 Peserta: 2 Sudah Terbit Surat, 3 Belum Diunggah)
        // =========================================================================
        $individuList = [
            // Individu 1: Dimas Wahyudi (Sudah Terbit)
            [
                'email' => 'dimas.wahyudi@alumni.magkom.go.id',
                'nama' => 'Dimas Wahyudi',
                'institusi' => 'Universitas Negeri Yogyakarta',
                'nim_nisn' => '20520241012',
                'foto' => 'profil-peserta/portrait_men_5.jpg',
                'bidang' => $bidangAptika,
                'pembimbing' => $pembimbingAptika,
                'file_surat' => 'surat-selesai/surat_selesai_dimas.pdf',
                'tgl_upload' => Carbon::now()->subDays(3),
            ],
            // Individu 2: Putri Maharani (Sudah Terbit)
            [
                'email' => 'putri.maharani@alumni.magkom.go.id',
                'nama' => 'Putri Maharani',
                'institusi' => 'UIN Sunan Kalijaga Yogyakarta',
                'nim_nisn' => '20106050033',
                'foto' => 'profil-peserta/portrait_women_5.jpg',
                'bidang' => $bidangIkp,
                'pembimbing' => $pembimbingIkp,
                'file_surat' => 'surat-selesai/surat_selesai_putri.pdf',
                'tgl_upload' => Carbon::now()->subDays(2),
            ],
            // Individu 3: Rizky Ramadhan, S.Kom (Belum Diunggah)
            [
                'email' => 'rizky.ramadhan@magkom.test',
                'nama' => 'Rizky Ramadhan, S.Kom',
                'institusi' => 'Universitas Gadjah Mada',
                'nim_nisn' => '19/443210/TK/49811',
                'foto' => 'profil-peserta/portrait_men_4.jpg',
                'bidang' => $bidangIti,
                'pembimbing' => $pembimbingIti,
                'file_surat' => null,
                'tgl_upload' => null,
            ],
            // Individu 4: Nabila Syifa Putri (Belum Diunggah)
            [
                'email' => 'nabila.syifa@magkom.test',
                'nama' => 'Nabila Syifa Putri',
                'institusi' => 'UPN "Veteran" Yogyakarta',
                'nim_nisn' => '123200045',
                'foto' => 'profil-peserta/portrait_women_4.jpg',
                'bidang' => $bidangAptika,
                'pembimbing' => $pembimbingAptika,
                'file_surat' => null,
                'tgl_upload' => null,
            ],
            // Individu 5: Aditya Pratama (Belum Diunggah)
            [
                'email' => 'aditya.pratama@magkom.test',
                'nama' => 'Aditya Pratama',
                'institusi' => 'Universitas AMIKOM Yogyakarta',
                'nim_nisn' => '21.11.4589',
                'foto' => 'profil-peserta/portrait_men_2.jpg',
                'bidang' => $bidangAptika,
                'pembimbing' => $pembimbingAptika,
                'file_surat' => null,
                'tgl_upload' => null,
            ],
        ];

        foreach ($individuList as $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'nama' => $item['nama'],
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Peserta,
                    'email_verified_at' => Carbon::now()->subMonths(4),
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'jurusan_id' => $jurusan->id,
                    'institusi' => $item['institusi'],
                    'nim_nisn' => $item['nim_nisn'],
                    'jenjang' => JenjangEnum::S1,
                    'no_hp' => '081234567890',
                    'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                    'foto' => $item['foto'],
                ]
            );

            $pendaftaran = Pendaftaran::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'tipe' => 'individu',
                ],
                [
                    'bulan_magang_id' => $bulanMagang->id,
                    'kuota_id' => $kuota->id,
                    'kelompok_magang_id' => null,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $adminUser->id,
                    'diajukan_at' => Carbon::now()->subMonths(4),
                    'diputuskan_at' => Carbon::now()->subMonths(3)->subDays(7),
                ]
            );

            Penempatan::updateOrCreate(
                ['peserta_id' => $user->id],
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'bidang_id' => $item['bidang']->id,
                    'pembimbing_id' => $item['pembimbing']->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'dibuat_oleh' => $adminUser->id,
                    'tgl_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                    'tgl_selesai' => Carbon::now()->subDays(3)->toDateString(),
                    'tgl_ditetapkan_selesai' => Carbon::now()->subDays(3)->toDateString(),
                    'status' => StatusPenempatanEnum::Selesai,
                    'file_surat_selesai' => $item['file_surat'],
                    'tgl_upload_surat_selesai' => $item['tgl_upload'],
                ]
            );
        }

        // =========================================================================
        // 3. ROMBONGAN TIM 1: "Tim Pengembang Portal Smart City Sleman" (Sudah Terbit)
        // =========================================================================
        $anisa = User::firstOrCreate(
            ['email' => 'anisa.rahmawati@magkom.test'],
            ['nama' => 'Anisa Rahmawati', 'password' => $defaultPassword, 'role' => RoleEnum::Peserta, 'email_verified_at' => now()]
        );
        $bagas = User::firstOrCreate(
            ['email' => 'bagas.saputra@magkom.test'],
            ['nama' => 'Bagas Saputra', 'password' => $defaultPassword, 'role' => RoleEnum::Peserta, 'email_verified_at' => now()]
        );
        $citra = User::firstOrCreate(
            ['email' => 'citra.dewi@magkom.test'],
            ['nama' => 'Citra Dewi Lestari', 'password' => $defaultPassword, 'role' => RoleEnum::Peserta, 'email_verified_at' => now()]
        );

        $anisa->profil()->updateOrCreate(['user_id' => $anisa->id], ['jurusan_id' => $jurusan->id, 'jenjang' => JenjangEnum::S1, 'institusi' => 'Universitas Gadjah Mada', 'nim_nisn' => '20/457123/TK/50123', 'foto' => 'profil-peserta/portrait_women_1.jpg', 'no_hp' => '081234567810', 'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta']);
        $bagas->profil()->updateOrCreate(['user_id' => $bagas->id], ['jurusan_id' => $jurusan->id, 'jenjang' => JenjangEnum::S1, 'institusi' => 'Universitas Gadjah Mada', 'nim_nisn' => '20/458234/TK/50234', 'foto' => 'profil-peserta/portrait_men_1.jpg', 'no_hp' => '081234567820', 'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta']);
        $citra->profil()->updateOrCreate(['user_id' => $citra->id], ['jurusan_id' => $jurusan->id, 'jenjang' => JenjangEnum::S1, 'institusi' => 'Universitas Gadjah Mada', 'nim_nisn' => '20/459345/SP/50345', 'foto' => 'profil-peserta/portrait_women_2.jpg', 'no_hp' => '081234567830', 'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta']);

        $kelompok1 = KelompokMagang::firstOrCreate(
            ['nama_kelompok' => 'Tim Pengembang Portal Smart City Sleman'],
            ['ketua_user_id' => $anisa->id]
        );
        $kelompok1->update(['ketua_user_id' => $anisa->id]);

        foreach ([$anisa, $bagas, $citra] as $m) {
            KelompokAnggota::updateOrCreate(
                ['kelompok_magang_id' => $kelompok1->id, 'user_id' => $m->id],
                ['nama' => $m->nama, 'email' => $m->email, 'nim_nisn' => $m->profil?->nim_nisn ?? '20/450000', 'no_hp' => '081234567800']
            );
        }

        $pendaftaran1 = Pendaftaran::firstOrCreate(
            ['kelompok_magang_id' => $kelompok1->id],
            [
                'user_id' => $anisa->id,
                'bulan_magang_id' => $bulanMagang->id,
                'kuota_id' => $kuota->id,
                'tipe' => 'kelompok',
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diterima,
                'verifikator_id' => $adminUser->id,
                'diajukan_at' => Carbon::now()->subMonths(4),
                'diputuskan_at' => Carbon::now()->subMonths(3)->subDays(7),
            ]
        );

        foreach ([$anisa, $bagas, $citra] as $m) {
            Penempatan::updateOrCreate(
                ['peserta_id' => $m->id],
                [
                    'pendaftaran_id' => $pendaftaran1->id,
                    'bidang_id' => $bidangAptika->id,
                    'pembimbing_id' => $pembimbingAptika->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'dibuat_oleh' => $adminUser->id,
                    'tgl_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                    'tgl_selesai' => Carbon::now()->subDays(6)->toDateString(),
                    'tgl_ditetapkan_selesai' => Carbon::now()->subDays(6)->toDateString(),
                    'status' => StatusPenempatanEnum::Selesai,
                    'file_surat_selesai' => 'surat-selesai/surat_selesai_smartcity.pdf',
                    'tgl_upload_surat_selesai' => Carbon::now()->subDays(4),
                ]
            );
        }

        // =========================================================================
        // 4. ROMBONGAN TIM 2: "Tim CSIRT & Keamanan Siber Sleman" (Farhan Aditya Pratama)
        // =========================================================================
        $farhan = User::firstOrCreate(
            ['email' => 'farhan.aditya@alumni.magkom.go.id'],
            ['nama' => 'Farhan Aditya Pratama', 'password' => $defaultPassword, 'role' => RoleEnum::Peserta, 'email_verified_at' => now()]
        );
        $maya = User::firstOrCreate(
            ['email' => 'maya.safira@alumni.magkom.go.id'],
            ['nama' => 'Maya Safira Kusuma', 'password' => $defaultPassword, 'role' => RoleEnum::Peserta, 'email_verified_at' => now()]
        );
        $gilang = User::firstOrCreate(
            ['email' => 'gilang.pamungkas@alumni.magkom.go.id'],
            ['nama' => 'Gilang Pamungkas', 'password' => $defaultPassword, 'role' => RoleEnum::Peserta, 'email_verified_at' => now()]
        );

        $farhan->profil()->updateOrCreate(['user_id' => $farhan->id], ['jurusan_id' => $jurusan->id, 'jenjang' => JenjangEnum::S1, 'institusi' => 'Universitas Negeri Yogyakarta', 'nim_nisn' => '21520244001', 'foto' => 'profil-peserta/portrait_men_6.jpg', 'no_hp' => '081298765413', 'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta']);
        $maya->profil()->updateOrCreate(['user_id' => $maya->id], ['jurusan_id' => $jurusan->id, 'jenjang' => JenjangEnum::S1, 'institusi' => 'Universitas Negeri Yogyakarta', 'nim_nisn' => '21520244002', 'foto' => 'profil-peserta/portrait_women_6.jpg', 'no_hp' => '081298765414', 'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta']);
        $gilang->profil()->updateOrCreate(['user_id' => $gilang->id], ['jurusan_id' => $jurusan->id, 'jenjang' => JenjangEnum::S1, 'institusi' => 'Universitas Negeri Yogyakarta', 'nim_nisn' => '21520244003', 'foto' => 'profil-peserta/portrait_men_7.jpg', 'no_hp' => '081298765415', 'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta']);

        $kelompok2 = KelompokMagang::firstOrCreate(
            ['nama_kelompok' => 'Tim CSIRT & Keamanan Siber Sleman'],
            ['ketua_user_id' => $farhan->id]
        );
        $kelompok2->update(['ketua_user_id' => $farhan->id]);

        foreach ([$farhan, $maya, $gilang] as $m) {
            KelompokAnggota::updateOrCreate(
                ['kelompok_magang_id' => $kelompok2->id, 'user_id' => $m->id],
                ['nama' => $m->nama, 'email' => $m->email, 'nim_nisn' => $m->profil?->nim_nisn ?? '21520000', 'no_hp' => $m->profil?->no_hp ?? '081298765400']
            );
        }

        $pendaftaran2 = Pendaftaran::firstOrCreate(
            ['kelompok_magang_id' => $kelompok2->id],
            [
                'user_id' => $farhan->id,
                'bulan_magang_id' => $bulanMagang->id,
                'kuota_id' => $kuota->id,
                'tipe' => 'kelompok',
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diterima,
                'verifikator_id' => $adminUser->id,
                'diajukan_at' => Carbon::now()->subMonths(4),
                'diputuskan_at' => Carbon::now()->subMonths(3)->subDays(7),
            ]
        );

        // Buat penempatan untuk ke-3 anggota kelompok 2 (dengan Farhan sudah terbit file surat resmi sesuai Foto 2)
        foreach ([$farhan, $maya, $gilang] as $member) {
            Penempatan::updateOrCreate(
                ['peserta_id' => $member->id],
                [
                    'pendaftaran_id' => $pendaftaran2->id,
                    'bidang_id' => $bidangIti->id,
                    'pembimbing_id' => $pembimbingIti->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'dibuat_oleh' => $adminUser->id,
                    'tgl_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                    'tgl_selesai' => Carbon::now()->subDays(2)->toDateString(),
                    'tgl_ditetapkan_selesai' => Carbon::now()->subDays(2)->toDateString(),
                    'status' => StatusPenempatanEnum::Selesai,
                    'file_surat_selesai' => 'surat-selesai/F4092fMmDh4B3mBlooH4KfVtuCu3s3nLhXU4EkMo.pdf',
                    'tgl_upload_surat_selesai' => Carbon::now()->subHours(2),
                ]
            );
        }
    }
}
