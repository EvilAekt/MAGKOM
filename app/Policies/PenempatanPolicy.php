<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Penempatan;
use App\Models\User;

class PenempatanPolicy
{
    /**
     * Menentukan apakah pengguna boleh melihat rincian penempatan.
     */
    public function view(User $user, Penempatan $penempatan): bool
    {
        return $user->isAdmin()
            || $user->isSuperAdmin()
            || $user->id === $penempatan->peserta_id
            || $user->id === $penempatan->pembimbing_id;
    }

    /**
     * Menentukan apakah pengguna berhak mengelola penempatan (Admin / SuperAdmin).
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
