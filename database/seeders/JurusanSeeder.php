<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusanList = [
            // Jalur SMK
            ['nama_jurusan' => 'Teknik Komputer dan Jaringan (TKJ)', 'kategori' => 'smk', 'aktif' => true],
            ['nama_jurusan' => 'Desain Grafis / DKV (SMK)', 'kategori' => 'smk', 'aktif' => true],
            ['nama_jurusan' => 'Administrasi Perkantoran / Manajemen Perkantoran (SMK)', 'kategori' => 'smk', 'aktif' => true],
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak (RPL)', 'kategori' => 'smk', 'aktif' => true],

            // Jalur Mahasiswa (D3 / D4 / S1)
            ['nama_jurusan' => 'Informatika', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Sistem Informasi', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Teknologi Informasi', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Manajemen Informatika', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Statistik', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Ilmu Komunikasi', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Komunikasi Penyiaran Islam', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Desain Komunikasi Visual / Desain Grafis', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Ekonomi dan Sosial (KPE)', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Administrasi / Manajemen Perkantoran', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Kearsipan (Ilmu Kearsipan / Manajemen Rekod)', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Teknik Komputer', 'kategori' => 'mahasiswa', 'aktif' => true],
        ];

        foreach ($jurusanList as $jurusan) {
            Jurusan::updateOrCreate(
                ['nama_jurusan' => $jurusan['nama_jurusan']],
                $jurusan
            );
        }
    }
}
