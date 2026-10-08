<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatusPenempatanEnum;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;

class TugasPolicy
{
    /**
     * Boleh melihat daftar tugas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Boleh melihat detail suatu tugas.
     */
    public function view(User $user, Tugas $tugas): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        $penempatan = $tugas->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->peserta_id === $user->id
            || $penempatan->pembimbing_id === $user->id;
    }

    /**
     * AC-08-5: SuperAdmin STRICTLY READ-ONLY.
     * Hanya pembimbing yang ditugaskan (atau Admin instansi) yang boleh membuat tugas.
     */
    public function create(User $user, Penempatan $penempatan): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        return $penempatan->pembimbing_id === $user->id || $user->isAdmin();
    }

    /**
     * Mengubah tugas / status tugas oleh pembimbing.
     */
    public function update(User $user, Tugas $tugas): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        $penempatan = $tugas->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->pembimbing_id === $user->id || $user->isAdmin();
    }

    /**
     * Peserta mengunggah progres tugas miliknya saat status magang aktif.
     */
    public function submitProgres(User $user, Tugas $tugas): bool
    {
        $penempatan = $tugas->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->peserta_id === $user->id
            && $penempatan->status === StatusPenempatanEnum::Aktif;
    }

    /**
     * Review progres tugas oleh pembimbing.
     */
    public function reviewProgres(User $user, Tugas $tugas): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        $penempatan = $tugas->penempatan;
        if (! $penempatan) {
            return false;
        }

        return $penempatan->pembimbing_id === $user->id || $user->isAdmin();
    }
}
