<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case Pembimbing = 'pembimbing';
    case Peserta = 'peserta';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'SuperAdmin',
            self::Admin => 'Administrasi Kominfo',
            self::Pembimbing => 'Pembimbing',
            self::Peserta => 'Peserta Magang',
        };
    }
}
