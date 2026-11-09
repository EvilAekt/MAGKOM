<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengaturanList = [
            ['kunci' => 'kuota_umum_maks_per_bulan', 'nilai' => '12'],
            ['kunci' => 'slot_khusus_default', 'nilai' => '3'],
            ['kunci' => 'maks_peserta_aktif_per_pembimbing', 'nilai' => '20'],
            ['kunci' => 'maks_anggota_kelompok', 'nilai' => '4'],
            ['kunci' => 'durasi_magang_maks_bulan', 'nilai' => '6'],
            ['kunci' => 'logbook_maks_mundur_hari', 'nilai' => '3'],
            ['kunci' => 'berkas_maks_mb', 'nilai' => '10'],
            ['kunci' => 'deadline_mendekati_hari', 'nilai' => '3'],
            ['kunci' => 'sla_verifikasi_hari', 'nilai' => '5'],
            ['kunci' => 'masa_tenggang_bulan', 'nilai' => '2'],
            ['kunci' => 'retensi_audit_akses_data_hari', 'nilai' => '365'],
            ['kunci' => 'fitur_pendaftaran_kelompok_aktif', 'nilai' => '1'],
        ];

        foreach ($pengaturanList as $pengaturan) {
            Pengaturan::updateOrCreate(
                ['kunci' => $pengaturan['kunci']],
                $pengaturan
            );
        }

        // Bersihkan dead keys jika masih ada
        Pengaturan::deleteKunci('predikat_batas');
        Pengaturan::deleteKunci('format_nomor_sertifikat');
        Pengaturan::deleteKunci('fitur_sertifikat_aktif');
        Pengaturan::deleteKunci('fitur_survei_aktif');
    }
}
