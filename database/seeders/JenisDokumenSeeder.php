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
            // =========================================================================
            // 1. KELOMPOK SMK (4 DOKUMEN RESMI)
            // =========================================================================
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Permohonan PKL dari Sekolah',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Tautan Portofolio Proyek',
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
                'nama' => 'Surat Pengantar dari Orang Tua',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => false,
                'aktif' => true,
            ],

            // =========================================================================
            // 2. KELOMPOK KAMPUS - D3 (4 DOKUMEN RESMI)
            // =========================================================================
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Pengantar Magang Resmi dari Dekanat / Fakultas',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Tautan Portofolio Proyek',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'transkrip',
                'nama' => 'Transkrip Nilai Akademik Sementara',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'proposal',
                'nama' => 'Proposal Rencana Kegiatan Magang',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],

            // =========================================================================
            // 3. KELOMPOK KAMPUS - S1 (4 DOKUMEN RESMI)
            // =========================================================================
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Pengantar Magang Resmi dari Dekanat / Fakultas',
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
            [
                'kode' => 'proposal',
                'nama' => 'Proposal Rencana Kegiatan Magang',
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
