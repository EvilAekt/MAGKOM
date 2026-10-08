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
            ['nama_jurusan' => 'Teknik Komputer dan Jaringan (TKJ)', 'kategori' => 'smk', 'aktif' => true],
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak (RPL)', 'kategori' => 'smk', 'aktif' => true],
            ['nama_jurusan' => 'Sistem Informasi', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Manajemen Informatika', 'kategori' => 'mahasiswa', 'aktif' => true],
            ['nama_jurusan' => 'Informatika', 'kategori' => 'mahasiswa', 'aktif' => true],
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
