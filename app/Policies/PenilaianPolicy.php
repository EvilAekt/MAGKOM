<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatusPenilaianEnum;
use App\Models\Penempatan;
use App\Models\User;

class PenilaianPolicy
{
    /**
     * Melihat nilai magang.
     */
    public function view(User $user, Penempatan $penempatan): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        if ($user->id === $penempatan->pembimbing_id) {
            return true;
        }

        // Peserta hanya dapat melihat jika nilai sudah final
        if ($user->id === $penempatan->peserta_id) {
            return $penempatan->penilaian?->status === StatusPenilaianEnum::Final;
        }

        return false;
    }

    /**
     * AC-10-7: SuperAdmin STRICTLY READ-ONLY.
     * Hanya pembimbing yang ditugaskan (atau Admin instansi) yang berhak memberi / merevisi nilai.
     */
    public function manage(User $user, Penempatan $penempatan): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        return $user->id === $penempatan->pembimbing_id || $user->isAdmin();
    }

    /**
     * Mengunduh lembar nilai resmi PDF (AC-12).
     */
    public function downloadPdf(User $user, Penempatan $penempatan): bool
    {
        $penilaian = $penempatan->penilaian;
        if (! $penilaian || $penilaian->status !== StatusPenilaianEnum::Final) {
            return false;
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        return $user->id === $penempatan->peserta_id
            || $user->id === $penempatan->pembimbing_id;
    }
}
