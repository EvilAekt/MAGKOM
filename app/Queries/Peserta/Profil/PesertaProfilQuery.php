<?php

declare(strict_types=1);

namespace App\Queries\Peserta\Profil;

use App\Enums\StatusPenempatanEnum;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PesertaProfilQuery
{
    /**
     * Mengambil dan menyusun seluruh data pendukung untuk halaman profil peserta.
     *
     * @return array{
     *     user: User,
     *     profil: mixed,
     *     jurusanList: Collection<int, Jurusan>,
     *     penempatan: ?Penempatan,
     *     persentaseLengkap: int,
     *     checklist: array<string, bool>,
     *     kelompokMagang: mixed,
     *     isAkademikLocked: bool
     * }
     */
    public function getShowData(User $user): array
    {
        $user->loadMissing(['kelompokMagang.anggota']);
        $profil = $user->profil;
        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        $kelompokMagang = $user->kelompokMagang;
        if (! $kelompokMagang) {
            $kelompokMagang = $user->pendaftaran()
                ->whereNotNull('kelompok_magang_id')
                ->with('kelompokMagang.anggota')
                ->latest()
                ->first()?->kelompokMagang;
        }

        $penempatan = Penempatan::with(['bidang', 'pembimbing'])
            ->where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();

        // Data registrasi akademik (Institusi, NIM/NISN, Jenjang, Jurusan) terkunci permanen jika profil sudah ada
        $isAkademikLocked = (bool) ($profil && $profil->exists && ! empty($profil->institusi));

        $kelengkapan = $profil?->hitungKelengkapan() ?? [
            'checklist' => [
                'data_diri' => ! empty($user->nama || $user->name) && ! empty($user->email),
                'foto' => false,
                'akademik' => false,
                'kontak' => false,
                'darurat' => false,
            ],
            'persentase' => 0,
            'is_lengkap' => false,
        ];

        return [
            'user' => $user,
            'profil' => $profil,
            'jurusanList' => $jurusanList,
            'penempatan' => $penempatan,
            'persentaseLengkap' => $kelengkapan['persentase'],
            'checklist' => $kelengkapan['checklist'],
            'kelompokMagang' => $kelompokMagang,
            'isAkademikLocked' => $isAkademikLocked,
        ];
    }
}
