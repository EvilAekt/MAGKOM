<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatusPendaftaranEnum;
use App\Models\Pendaftaran;
use App\Models\User;

class PendaftaranPolicy
{
    /**
     * Menentukan apakah pengguna dapat melihat rincian pendaftaran.
     */
    public function view(User $user, Pendaftaran $pendaftaran): bool
    {
        return $user->isAdmin()
            || $user->isSuperAdmin()
            || $user->id === $pendaftaran->user_id;
    }

    /**
     * Menentukan apakah peserta dapat mengubah/mengunggah ulang berkas.
     */
    public function update(User $user, Pendaftaran $pendaftaran): bool
    {
        if ($user->id !== $pendaftaran->user_id) {
            return false;
        }

        return in_array($pendaftaran->status, [
            StatusPendaftaranEnum::BerkasBelumLengkap,
            StatusPendaftaranEnum::PerluPerbaikan,
        ], true);
    }

    /**
     * Menentukan apakah peserta dapat membatalkan pendaftaran mandiri (AC-03-9).
     */
    public function cancel(User $user, Pendaftaran $pendaftaran): bool
    {
        if ($user->id !== $pendaftaran->user_id) {
            return false;
        }

        return in_array($pendaftaran->status, [
            StatusPendaftaranEnum::BerkasBelumLengkap,
            StatusPendaftaranEnum::Diajukan,
            StatusPendaftaranEnum::PerluPerbaikan,
            StatusPendaftaranEnum::WaitingList,
        ], true);
    }

    /**
     * Menentukan apakah admin/superadmin dapat mereview dan memutuskan pendaftaran.
     */
    public function review(User $user, Pendaftaran $pendaftaran): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
