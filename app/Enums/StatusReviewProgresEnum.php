<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusReviewProgresEnum: string
{
    case Menunggu = 'menunggu';
    case Diterima = 'diterima';
    case PerluRevisi = 'perlu_revisi';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu Review',
            self::Diterima => 'Diterima',
            self::PerluRevisi => 'Perlu Revisi',
        };
    }
}
