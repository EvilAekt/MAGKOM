<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatusPenempatanEnum;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\User;

class LogbookPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Logbook $logbook): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        $penempatan = $logbook->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->peserta_id === $user->id
            || $penempatan->pembimbing_id === $user->id;
    }

    /**
     * Peserta mengisi logbook saat penempatan berstatus aktif.
     */
    public function create(User $user, Penempatan $penempatan): bool
    {
        return $penempatan->peserta_id === $user->id
            && $penempatan->status === StatusPenempatanEnum::Aktif;
    }

    /**
     * Peserta memperbarui logbook miliknya jika penempatan aktif.
     */
    public function update(User $user, Logbook $logbook): bool
    {
        $penempatan = $logbook->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->peserta_id === $user->id
            && $penempatan->status === StatusPenempatanEnum::Aktif;
    }

    /**
     * Pembimbing mereview logbook anak bimbingannya.
     * SuperAdmin strictly read-only!
     */
    public function review(User $user, Logbook $logbook): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        $penempatan = $logbook->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->pembimbing_id === $user->id || $user->isAdmin();
    }
}
