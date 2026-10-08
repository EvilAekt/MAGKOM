<?php

declare(strict_types=1);

namespace App\Enums;

enum JalurEnum: string
{
    case Umum = 'umum';
    case Khusus = 'khusus';

    public function label(): string
    {
        return match ($this) {
            self::Umum => 'Jalur Umum',
            self::Khusus => 'Jalur Khusus',
        };
    }
}
