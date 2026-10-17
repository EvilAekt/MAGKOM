<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. EGOV (Update bidang Aptika jika ada, atau buat baru)
        $bidangAptika = Bidang::find(1);
        if ($bidangAptika) {
            $bidangAptika->update([
                'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
                'deskripsi' => 'Pengembangan ekosistem digital, integrasi aplikasi SPBE, tata kelola data, dan layanan sistem pemerintahan berbasis elektronik.',
                'aktif' => true,
            ]);
        } else {
            Bidang::updateOrCreate(
                ['nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)'],
                [
                    'deskripsi' => 'Pengembangan ekosistem digital, integrasi aplikasi SPBE, tata kelola data, dan layanan sistem pemerintahan berbasis elektronik.',
                    'aktif' => true,
                ]
            );
        }

        // 2. IKP
        $bidangIkp = Bidang::find(2);
        if ($bidangIkp) {
            $bidangIkp->update([
                'nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)',
                'deskripsi' => 'Pengelolaan kanal komunikasi resmi pemerintah kabupaten, publikasi multimedia, peliputan berita, kemitraan media, dan relasi publik.',
                'aktif' => true,
            ]);
        } else {
            Bidang::updateOrCreate(
                ['nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)'],
                [
                    'deskripsi' => 'Pengelolaan kanal komunikasi resmi pemerintah kabupaten, publikasi multimedia, peliputan berita, kemitraan media, dan relasi publik.',
                    'aktif' => true,
                ]
            );
        }

        // 3. ITI
        Bidang::updateOrCreate(
            ['nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)'],
            [
                'deskripsi' => 'Pengelolaan jaringan intra pemerintah daerah, infrastruktur data center, server cloud, keamanan informasi siber, dan CSIRT Sleman.',
                'aktif' => true,
            ]
        );

        // 4. Sekretariat
        Bidang::updateOrCreate(
            ['nama_bidang' => 'Sekretariat Dinas'],
            [
                'deskripsi' => 'Pelayanan administrasi umum perkantoran, persuratan, tata kearsipan, manajemen rekod, kepegawaian, dan perbendaharaan dinas.',
                'aktif' => true,
            ]
        );
    }
}
