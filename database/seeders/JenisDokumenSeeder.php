<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Models\JenisDokumen;
use Illuminate\Database\Seeder;

class JenisDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $dokumenList = [
            // Jalur SMK
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Permohonan PKL/Magang dari Kepala Sekolah',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Portofolio Keahlian',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'kartu_pelajar',
                'nama' => 'Salinan Kartu Pelajar',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'surat_ortu',
                'nama' => 'Surat Persetujuan Orang Tua / Wali',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => false,
                'aktif' => true,
            ],

            // Jalur Mahasiswa D3
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Pengantar Magang Resmi dari Fakultas / Program Studi',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'proposal',
                'nama' => 'Proposal Kegiatan Magang',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Portofolio Proyek',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'transkrip',
                'nama' => 'Salinan Transkrip Nilai Akademik Terakhir',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],

            // Jalur Mahasiswa S1
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Pengantar Magang Resmi dari Dekanat / Fakultas',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'proposal',
                'nama' => 'Proposal Rencana Kegiatan Magang & Riset',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Tautan Portofolio Proyek',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'transkrip',
                'nama' => 'Transkrip Nilai Akademik Sementara',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
        ];

        foreach ($dokumenList as $item) {
            JenisDokumen::updateOrCreate(
                [
                    'kode' => $item['kode'],
                    'jenjang' => $item['jenjang']->value,
                ],
                [
                    'nama' => $item['nama'],
                    'wajib' => $item['wajib'],
                    'aktif' => $item['aktif'],
                ]
            );
        }
    }
}
