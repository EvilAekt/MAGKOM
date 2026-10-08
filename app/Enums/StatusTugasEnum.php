<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusTugasEnum: string
{
    case Belum = 'belum';
    case Proses = 'proses';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Belum => 'Belum Dikerjakan',
            self::Proses => 'Sedang Dikerjakan',
            self::Selesai => 'Selesai',
        };
    }
}
