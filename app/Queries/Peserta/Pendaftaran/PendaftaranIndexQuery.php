<?php

declare(strict_types=1);

namespace App\Queries\Peserta\Pendaftaran;

use App\Enums\JenjangEnum;
use App\Enums\StatusKetersediaanEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\BulanStatusService;
use Illuminate\Support\Collection;

class PendaftaranIndexQuery
{
    public function __construct(
        protected BulanStatusService $bulanStatusService
    ) {}

    /**
     * Mengambil seluruh data yang dibutuhkan untuk halaman indeks pendaftaran peserta.
     *
     * @return array{
     *     profilIncomplete: bool,
     *     persentaseProfil: int,
     *     pendaftaranList: Collection<int, Pendaftaran>,
     *     pendaftaranAktif: ?Pendaftaran,
     *     bulanAktifList: Collection<int, BulanMagang>,
     *     profil: mixed,
     *     kuotaList: Collection<int, Kuota>
     * }
     */
    public function getIndexData(User $user): array
    {
        $profil = $user->profil;
        $profil?->loadMissing(['jurusan.bidang']);

        // Proteksi Alur: Wajib Biodata 100% Lengkap sebelum Memilih Periode & Kuota Magang
        $hasPendaftaran = Pendaftaran::where('user_id', $user->id)->exists();
        if (! $hasPendaftaran && (! $profil || ! $profil->isLengkap())) {
            $persentase = $profil ? $profil->hitungKelengkapan()['persentase'] : 0;

            return [
                'profilIncomplete' => true,
                'persentaseProfil' => $persentase,
                'pendaftaranList' => collect(),
                'pendaftaranAktif' => null,
                'bulanAktifList' => collect(),
                'profil' => $profil,
                'kuotaList' => collect(),
            ];
        }

        $pendaftaranList = Pendaftaran::with(['bulanMagang', 'kuota.jurusan', 'penempatan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pendaftaranAktif = $pendaftaranList->first(function ($p) {
            return in_array($p->status->value, [
                StatusPendaftaranEnum::BerkasBelumLengkap->value,
                StatusPendaftaranEnum::Diajukan->value,
                StatusPendaftaranEnum::PerluPerbaikan->value,
                StatusPendaftaranEnum::WaitingList->value,
                StatusPendaftaranEnum::Diterima->value,
            ], true);
        });

        $kategoriJurusan = match ($profil?->jenjang) {
            JenjangEnum::Smk => 'smk',
            JenjangEnum::D3, JenjangEnum::S1 => 'mahasiswa',
            default => null,
        };

        $bulanAktifList = BulanMagang::with(['kuota' => function ($q) use ($kategoriJurusan) {
            $q->where('aktif', true)->with('jurusan');
            if ($kategoriJurusan) {
                $q->whereHas('jurusan', function ($jq) use ($kategoriJurusan) {
                    $jq->where('kategori', $kategoriJurusan);
                });
            }
        }])
            ->where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get()
            ->filter(function ($bulan) {
                return $this->bulanStatusService->status($bulan) === StatusKetersediaanEnum::Tersedia;
            });

        $kuotaList = collect();
        if ($profil && $profil->jurusan_id) {
            $kuotaList = Kuota::with(['bulanMagang', 'jurusan.bidang'])
                ->where('jurusan_id', $profil->jurusan_id)
                ->where('aktif', true)
                ->whereHas('bulanMagang', function ($q) {
                    $q->where('aktif', true);
                })
                ->get()
                ->filter(function ($kuota) {
                    return $kuota->bulanMagang && $this->bulanStatusService->status($kuota->bulanMagang, $kuota) === StatusKetersediaanEnum::Tersedia;
                })
                ->values();
        }

        return [
            'profilIncomplete' => false,
            'persentaseProfil' => 100,
            'pendaftaranList' => $pendaftaranList,
            'pendaftaranAktif' => $pendaftaranAktif,
            'bulanAktifList' => $bulanAktifList,
            'profil' => $profil,
            'kuotaList' => $kuotaList,
        ];
    }
}
