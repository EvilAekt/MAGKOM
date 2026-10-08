<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusKetersediaanEnum: string
{
    case Tersedia = 'tersedia';
    case Tutup = 'tutup';
    case Segera = 'segera';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Tutup => 'Tutup',
            self::Segera => 'Segera Dibuka',
        };
    }
}
