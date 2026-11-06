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

        // 2. Peserta pemilik pendaftaran atau bagian dari kelompok magang terkait
        if ($dokumen->pendaftaran) {
            if ($dokumen->pendaftaran->user_id === $user->id) {
                return true;
            }

            if ($dokumen->pendaftaran->kelompok_magang_id && $dokumen->pendaftaran->kelompokMagang) {
                $kelompok = $dokumen->pendaftaran->kelompokMagang;
                if ($kelompok->ketua_user_id === $user->id) {
                    return true;
                }

                $isAnggota = $kelompok->anggota()
                    ->where(function ($q) use ($user) {
                        $q->where('email', $user->email);
                        if (! empty($user->profil?->nim_nisn)) {
                            $q->orWhere('nim_nisn', $user->profil->nim_nisn);
                        }
                    })->exists();

                if ($isAnggota) {
                    return true;
                }
            }
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
