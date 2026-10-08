<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPendaftaranEnum: string
{
    case BerkasBelumLengkap = 'berkas_belum_lengkap';
    case Diajukan = 'diajukan';
    case PerluPerbaikan = 'perlu_perbaikan';
    case WaitingList = 'waiting_list';
    case Diterima = 'diterima';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::BerkasBelumLengkap => 'Berkas Belum Lengkap',
            self::Diajukan => 'Diajukan',
            self::PerluPerbaikan => 'Perlu Perbaikan',
            self::WaitingList => 'Waiting List',
            self::Diterima => 'Diterima',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
        };
    }
}
