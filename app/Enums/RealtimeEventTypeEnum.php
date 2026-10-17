<?php

declare(strict_types=1);

namespace App\Enums;

enum RealtimeEventTypeEnum: string
{
    case PendaftaranBaru = 'pendaftaran_baru';
    case StatusPendaftaran = 'status_pendaftaran';
    case KuotaBerubah = 'kuota_berubah';
    case LogbookBaru = 'logbook_baru';
    case LogbookDireviu = 'logbook_direviu';
    case TugasBaru = 'tugas_baru';
    case TugasDikumpulkan = 'tugas_dikumpulkan';
    case AncamanKeamanan = 'ancaman_keamanan';

    public function label(): string
    {
        return match ($this) {
            self::PendaftaranBaru => 'Pendaftaran Baru Masuk',
            self::StatusPendaftaran => 'Pembaruan Status Pendaftaran',
            self::KuotaBerubah => 'Pembaruan Ketersediaan Kuota',
            self::LogbookBaru => 'Setoran Logbook Harian Mentee',
            self::LogbookDireviu => 'Reviu Logbook Harian Selesai',
            self::TugasBaru => 'Tugas Magang Baru Diterbitkan',
            self::TugasDikumpulkan => 'Pengumpulan Progres Tugas Mentee',
            self::AncamanKeamanan => 'Peringatan Keamanan Sistem',
        };
    }
}
