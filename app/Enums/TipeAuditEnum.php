<?php

declare(strict_types=1);

namespace App\Enums;

enum TipeAuditEnum: string
{
    case Auth = 'auth';
    case Aksi = 'aksi';
    case Service = 'service';
    case AksesData = 'akses_data';

    public function label(): string
    {
        return match ($this) {
            self::Auth => 'Autentikasi & Sesi',
            self::Aksi => 'Aksi Bisnis / Mutasi',
            self::Service => 'Pemanggilan Service',
            self::AksesData => 'Akses Pembacaan Data',
        };
    }
}
