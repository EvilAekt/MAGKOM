<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPenempatanEnum: string
{
    case Aktif = 'aktif';
    case Selesai = 'selesai';
    case MengundurkanDiri = 'mengundurkan_diri';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Selesai => 'Selesai Magang',
            self::MengundurkanDiri => 'Mengundurkan Diri',
            self::Nonaktif => 'Nonaktif',
        };
    }
}
