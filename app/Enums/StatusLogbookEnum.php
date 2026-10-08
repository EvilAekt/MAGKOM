<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusLogbookEnum: string
{
    case Terkirim = 'terkirim';
    case PerluRevisi = 'perlu_revisi';

    public function label(): string
    {
        return match ($this) {
            self::Terkirim => 'Terkirim',
            self::PerluRevisi => 'Perlu Revisi',
        };
    }
}
