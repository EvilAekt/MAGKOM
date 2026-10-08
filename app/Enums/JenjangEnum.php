<?php

declare(strict_types=1);

namespace App\Enums;

enum JenjangEnum: string
{
    case Smk = 'smk';
    case D3 = 'd3';
    case S1 = 's1';

    public function label(): string
    {
        return match ($this) {
            self::Smk => 'SMK',
            self::D3 => 'Diploma 3 (D3)',
            self::S1 => 'Sarjana (S1)',
        };
    }
}
