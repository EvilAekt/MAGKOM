<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SuratSelesaiDummySeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data peserta individu dan rombongan kelompok selesai magang
     * lengkap dengan foto asli, metadata profil, dan file surat selesai.
     */
    public function run(): void
    {
        // 1. Pastikan berkas contoh PDF tersimpan di disk lokal (storage/app/surat-selesai/)
        $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000108 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF\n";

        Storage::disk('local')->put('surat-selesai/surat_selesai_dimas.pdf', $pdfContent);
        Storage::disk('local')->put('surat-selesai/surat_selesai_putri.pdf', $pdfContent);
        Storage::disk('local')->put('surat-selesai/surat_selesai_smartcity.pdf', $pdfContent);

        // Ambil pembimbing dan bidang
        $pembimbingAptika = User::where('role', RoleEnum::Pembimbing)->first() ?? User::first();
        $pembimbingIkp = User::where('role', RoleEnum::Pembimbing)->skip(1)->first() ?? $pembimbingAptika;
        $pembimbingIti = User::where('role', RoleEnum::Pembimbing)->skip(2)->first() ?? $pembimbingAptika;

        $bidangAptika = Bidang::where('nama_bidang', 'like', '%Aptika%')->orWhere('nama_bidang', 'like', '%Aplikasi%')->first();
        $bidangIkp = Bidang::where('nama_bidang', 'like', '%IKP%')->orWhere('nama_bidang', 'like', '%Komunikasi%')->first();
        $bidangIti = Bidang::where('nama_bidang', 'like', '%ITI%')->orWhere('nama_bidang', 'like', '%Infrastruktur%')->first();

        $bulanMagang = BulanMagang::first();
        $kuota = Kuota::first();

        $adminUser = User::where('role', RoleEnum::Admin)->first() ?? User::first();

        // 2. PESERTA INDIVIDU (5 Peserta: 2 Sudah Terbit Surat, 3 Belum Diunggah)
        // Individu 1: Dimas Wahyudi (Sudah Terbit)
        $userDimas = User::find(120);
        if ($userDimas) {
            $userDimas->profil()->updateOrCreate(
                ['user_id' => $userDimas->id],
                [
                    'foto' => 'profil-peserta/portrait_men_5.jpg',
                    'institusi' => 'Universitas Negeri Yogyakarta',
                    'nim_nisn' => '20520241012',
                ]
            );
            Penempatan::where('peserta_id', $userDimas->id)->update([
                'status' => StatusPenempatanEnum::Selesai,
                'bidang_id' => $bidangAptika?->id ?? 1,
                'pembimbing_id' => $pembimbingAptika?->id,
                'tgl_mulai' => Carbon::now()->subMonths(3),
                'tgl_selesai' => Carbon::now()->subDays(5),
                'tgl_ditetapkan_selesai' => Carbon::now()->subDays(5),
                'file_surat_selesai' => 'surat-selesai/surat_selesai_dimas.pdf',
                'tgl_upload_surat_selesai' => Carbon::now()->subDays(3),
            ]);
        }

        // Individu 2: Putri Maharani (Sudah Terbit)
        $userPutri = User::find(121);
        if ($userPutri) {
            $userPutri->profil()->updateOrCreate(
                ['user_id' => $userPutri->id],
                [
                    'foto' => 'profil-peserta/portrait_women_5.jpg',
                    'institusi' => 'UIN Sunan Kalijaga Yogyakarta',
                    'nim_nisn' => '20106050033',
                ]
            );
            Penempatan::where('peserta_id', $userPutri->id)->update([
                'status' => StatusPenempatanEnum::Selesai,
                'bidang_id' => $bidangIkp?->id ?? 2,
                'pembimbing_id' => $pembimbingIkp?->id,
                'tgl_mulai' => Carbon::now()->subMonths(3),
                'tgl_selesai' => Carbon::now()->subDays(4),
                'tgl_ditetapkan_selesai' => Carbon::now()->subDays(4),
                'file_surat_selesai' => 'surat-selesai/surat_selesai_putri.pdf',
                'tgl_upload_surat_selesai' => Carbon::now()->subDays(2),
            ]);
        }

        // Individu 3: Rizky Ramadhan, S.Kom (Belum Diunggah)
        $userRizky = User::find(118);
        if ($userRizky) {
            $userRizky->profil()->updateOrCreate(
                ['user_id' => $userRizky->id],
                [
                    'foto' => 'profil-peserta/portrait_men_4.jpg',
                    'institusi' => 'Universitas Gadjah Mada',
                    'nim_nisn' => '19/443210/TK/49811',
                ]
            );
            Penempatan::where('peserta_id', $userRizky->id)->update([
                'status' => StatusPenempatanEnum::Selesai,
                'bidang_id' => $bidangIti?->id ?? 3,
                'pembimbing_id' => $pembimbingIti?->id,
                'tgl_mulai' => Carbon::now()->subMonths(3),
                'tgl_selesai' => Carbon::now()->subDays(3),
                'tgl_ditetapkan_selesai' => Carbon::now()->subDays(3),
                'file_surat_selesai' => null,
                'tgl_upload_surat_selesai' => null,
            ]);
        }

        // Individu 4: Nabila Syifa Putri (Belum Diunggah)
        $userNabila = User::find(119);
        if ($userNabila) {
            $userNabila->profil()->updateOrCreate(
                ['user_id' => $userNabila->id],
                [
                    'foto' => 'profil-peserta/portrait_women_4.jpg',
                    'institusi' => 'UPN "Veteran" Yogyakarta',
                    'nim_nisn' => '123200045',
                ]
            );
            Penempatan::where('peserta_id', $userNabila->id)->update([
                'status' => StatusPenempatanEnum::Selesai,
                'bidang_id' => $bidangAptika?->id ?? 1,
                'pembimbing_id' => $pembimbingAptika?->id,
                'tgl_mulai' => Carbon::now()->subMonths(3),
                'tgl_selesai' => Carbon::now()->subDays(2),
                'tgl_ditetapkan_selesai' => Carbon::now()->subDays(2),
                'file_surat_selesai' => null,
                'tgl_upload_surat_selesai' => null,
            ]);
        }

        // Individu 5: Aditya Pratama (Belum Diunggah)
        $userAditya = User::find(9);
        if ($userAditya) {
            $userAditya->profil()->updateOrCreate(
                ['user_id' => $userAditya->id],
                [
                    'foto' => 'profil-peserta/portrait_men_2.jpg',
                    'institusi' => 'Universitas AMIKOM Yogyakarta',
                    'nim_nisn' => '21.11.4589',
                ]
            );
            $penempatanAditya = Penempatan::where('peserta_id', $userAditya->id)->first();
            if ($penempatanAditya) {
                $penempatanAditya->update([
                    'status' => StatusPenempatanEnum::Selesai,
                    'bidang_id' => $bidangAptika?->id ?? 1,
                    'pembimbing_id' => $pembimbingAptika?->id,
                    'tgl_mulai' => Carbon::now()->subMonths(3),
                    'tgl_selesai' => Carbon::now()->subDays(1),
                    'tgl_ditetapkan_selesai' => Carbon::now()->subDays(1),
                    'file_surat_selesai' => null,
                    'tgl_upload_surat_selesai' => null,
                ]);
            }
        }

        // 3. ROMBONGAN KELOMPOK 1: "Tim Pengembang Portal Smart City Sleman" (Sudah Terbit Surat Selesai Tim)
        // Ketua: Anisa Rahmawati (115), Anggota: Bagas Saputra (116), Citra Dewi Lestari (117)
        $kelompok1 = KelompokMagang::find(4);
        if ($kelompok1) {
            $kelompok1->update(['nama_kelompok' => 'Tim Pengembang Portal Smart City Sleman']);

            // Update profil anggota kelompok 1
            $anisa = User::find(115);
            if ($anisa) {
                $anisa->profil()->updateOrCreate(
                    ['user_id' => $anisa->id],
                    [
                        'foto' => 'profil-peserta/portrait_women_1.jpg',
                        'institusi' => 'Universitas Gadjah Mada',
                        'nim_nisn' => '20/457123/TK/50123',
                    ]
                );
            }
            $bagas = User::find(116);
            if ($bagas) {
                $bagas->profil()->updateOrCreate(
                    ['user_id' => $bagas->id],
                    [
                        'foto' => 'profil-peserta/portrait_men_1.jpg',
                        'institusi' => 'Universitas Gadjah Mada',
                        'nim_nisn' => '20/458234/TK/50234',
                    ]
                );
            }
            $citra = User::find(117);
            if ($citra) {
                $citra->profil()->updateOrCreate(
                    ['user_id' => $citra->id],
                    [
                        'foto' => 'profil-peserta/portrait_women_2.jpg',
                        'institusi' => 'Universitas Gadjah Mada',
                        'nim_nisn' => '20/459345/SP/50345',
                    ]
                );
            }

            // Update penempatan ke-3 anggota menjadi selesai dan memiliki surat selesai tim
            Penempatan::whereIn('id', [58, 59, 60])->update([
                'status' => StatusPenempatanEnum::Selesai,
                'bidang_id' => $bidangAptika?->id ?? 1,
                'pembimbing_id' => $pembimbingAptika?->id,
                'tgl_mulai' => Carbon::now()->subMonths(3),
                'tgl_selesai' => Carbon::now()->subDays(6),
                'tgl_ditetapkan_selesai' => Carbon::now()->subDays(6),
                'file_surat_selesai' => 'surat-selesai/surat_selesai_smartcity.pdf',
                'tgl_upload_surat_selesai' => Carbon::now()->subDays(4),
            ]);
        }

        // 4. ROMBONGAN KELOMPOK 2: "Tim CSIRT & Keamanan Siber Sleman" (Belum Diunggah)
        // Anggota: Farhan Aditya Pratama (122), Maya Safira Kusuma (123), Gilang Pamungkas (124)
        $farhan = User::find(122);
        $maya = User::find(123);
        $gilang = User::find(124);

        if ($farhan && $maya && $gilang) {
            $farhan->profil()->updateOrCreate(
                ['user_id' => $farhan->id],
                [
                    'foto' => 'profil-peserta/portrait_men_6.jpg',
                    'institusi' => 'Universitas Negeri Yogyakarta',
                    'nim_nisn' => '21520244001',
                ]
            );
            $maya->profil()->updateOrCreate(
                ['user_id' => $maya->id],
                [
                    'foto' => 'profil-peserta/portrait_women_6.jpg',
                    'institusi' => 'Universitas Negeri Yogyakarta',
                    'nim_nisn' => '21520244002',
                ]
            );
            $gilang->profil()->updateOrCreate(
                ['user_id' => $gilang->id],
                [
                    'foto' => 'profil-peserta/portrait_men_7.jpg',
                    'institusi' => 'Universitas Negeri Yogyakarta',
                    'nim_nisn' => '21520244003',
                ]
            );

            // Buat / ambil KelompokMagang
            $kelompok2 = KelompokMagang::firstOrCreate(
                ['nama_kelompok' => 'Tim CSIRT & Keamanan Siber Sleman'],
                ['ketua_user_id' => $farhan->id]
            );
            $kelompok2->update(['ketua_user_id' => $farhan->id]);

            // Hubungkan KelompokAnggota
            KelompokAnggota::updateOrCreate(
                ['kelompok_magang_id' => $kelompok2->id, 'user_id' => $farhan->id],
                [
                    'nama' => $farhan->nama,
                    'nim_nisn' => '21520244001',
                    'email' => $farhan->email,
                    'no_hp' => '081234567891',
                ]
            );
            KelompokAnggota::updateOrCreate(
                ['kelompok_magang_id' => $kelompok2->id, 'user_id' => $maya->id],
                [
                    'nama' => $maya->nama,
                    'nim_nisn' => '21520244002',
                    'email' => $maya->email,
                    'no_hp' => '081234567892',
                ]
            );
            KelompokAnggota::updateOrCreate(
                ['kelompok_magang_id' => $kelompok2->id, 'user_id' => $gilang->id],
                [
                    'nama' => $gilang->nama,
                    'nim_nisn' => '21520244003',
                    'email' => $gilang->email,
                    'no_hp' => '081234567893',
                ]
            );

            // Buat Pendaftaran Kelompok 2
            $pendaftaran2 = Pendaftaran::updateOrCreate(
                ['kelompok_magang_id' => $kelompok2->id],
                [
                    'user_id' => $farhan->id,
                    'bulan_magang_id' => $bulanMagang?->id ?? 1,
                    'kuota_id' => $kuota?->id ?? 35,
                    'tipe' => 'kelompok',
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $adminUser?->id,
                    'diajukan_at' => Carbon::now()->subMonths(4),
                    'diputuskan_at' => Carbon::now()->subMonths(3)->subDays(7),
                ]
            );

            // Buat Penempatan untuk ke-3 anggota kelompok 2 (status selesai, belum ada surat)
            foreach ([$farhan, $maya, $gilang] as $member) {
                Penempatan::updateOrCreate(
                    [
                        'pendaftaran_id' => $pendaftaran2->id,
                        'peserta_id' => $member->id,
                    ],
                    [
                        'bidang_id' => $bidangIti?->id ?? 3,
                        'pembimbing_id' => $pembimbingIti?->id,
                        'jalur' => JalurEnum::Umum,
                        'sumber' => SumberPenempatanEnum::Online,
                        'dibuat_oleh' => $adminUser?->id,
                        'tgl_mulai' => Carbon::now()->subMonths(3),
                        'tgl_selesai' => Carbon::now()->subDays(2),
                        'tgl_ditetapkan_selesai' => Carbon::now()->subDays(2),
                        'status' => StatusPenempatanEnum::Selesai,
                        'file_surat_selesai' => null,
                        'tgl_upload_surat_selesai' => null,
                    ]
                );
            }
        }
    }
}
