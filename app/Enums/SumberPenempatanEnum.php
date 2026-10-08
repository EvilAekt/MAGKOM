<?php

declare(strict_types=1);

namespace App\Enums;

enum SumberPenempatanEnum: string
{
    case Online = 'online';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Pendaftaran Online',
            self::Manual => 'Input Manual Staf',
        };
    }
}
