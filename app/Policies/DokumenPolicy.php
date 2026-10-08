<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Dokumen;
use App\Models\Penempatan;
use App\Models\User;

class DokumenPolicy
{
    /**
     * Menentukan apakah pengguna boleh mengunduh atau melihat dokumen privat (AC-03-9).
     */
    public function view(User $user, Dokumen $dokumen): bool
    {
        // 1. Admin dan SuperAdmin memiliki hak akses audit
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        // 2. Peserta pemilik pendaftaran
        if ($dokumen->pendaftaran && $dokumen->pendaftaran->user_id === $user->id) {
            return true;
        }

        // 3. Pembimbing yang membimbing peserta pada penempatan terkait
        if ($dokumen->pendaftaran) {
            $isPembimbing = Penempatan::where('peserta_id', $dokumen->pendaftaran->user_id)
                ->where('pembimbing_id', $user->id)
                ->exists();

            if ($isPembimbing) {
                return true;
            }
        }

        return false;
    }
}
