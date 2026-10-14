<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Jurusan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangJurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pemetaan Jurusan yang diterima per Bidang di Diskominfo Sleman
        $mapping = [
            'Informasi dan Komunikasi Publik (IKP)' => [
                'Statistik',
                'Ilmu Komunikasi',
                'Desain Grafis / DKV (SMK)',
                'Desain Komunikasi Visual / Desain Grafis',
                'Komunikasi Penyiaran Islam',
            ],
            'Aplikasi Informatika & E-Government (EGOV)' => [
                'Informatika',
                'Manajemen Informatika',
                'Sistem Informasi',
                'Teknologi Informasi',
            ],
            'Infrastruktur Teknologi Informasi (ITI)' => [
                'Informatika',
                'Teknik Komputer dan Jaringan (TKJ)',
                'Teknologi Informasi',
            ],
            'Sekretariat Dinas' => [
                'Ilmu Komunikasi',
                'Komunikasi Penyiaran Islam',
                'Desain Grafis / DKV (SMK)',
                'Desain Komunikasi Visual / Desain Grafis',
                'Ekonomi dan Sosial (KPE)',
                'Administrasi Perkantoran / Manajemen Perkantoran (SMK)',
                'Administrasi / Manajemen Perkantoran',
                'Kearsipan (Ilmu Kearsipan / Manajemen Rekod)',
            ],
        ];

        foreach ($mapping as $namaBidang => $namaJurusanList) {
            $bidang = Bidang::where('nama_bidang', $namaBidang)->first();
            if (!$bidang) {
                continue;
            }

            $jurusanIds = Jurusan::whereIn('nama_jurusan', $namaJurusanList)->pluck('id')->toArray();
            
            // Sync secara aman tanpa duplikasi
            $bidang->jurusan()->syncWithoutDetaching($jurusanIds);
        }
    }
}
