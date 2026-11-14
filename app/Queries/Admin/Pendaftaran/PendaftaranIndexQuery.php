<?php

declare(strict_types=1);

namespace App\Queries\Admin\Pendaftaran;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PendaftaranIndexQuery
{
    /**
     * Mengambil daftar pendaftaran terpaginasi dengan seluruh filter, sorting, dan pencarian.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan', 'penempatan', 'kelompokMagang.anggota']);

        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        } else {
            // Default: Paling baru dibuat (terbaru)
            $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        }

        if ($request->filled('tipe')) {
            $tipe = strtolower(trim((string) $request->input('tipe')));
            if ($tipe === 'individu' || $tipe === 'mandiri') {
                $query->where(function ($q) {
                    $q->where('tipe', 'mandiri')
                        ->orWhere('tipe', 'individu')
                        ->orWhereNull('tipe');
                });
            } elseif ($tipe === 'kelompok') {
                $query->where('tipe', 'kelompok');
            }
        }

        if ($request->filled('q')) {
            $rawSearch = trim((string) $request->input('q'));
            $cleanId = ltrim(str_ireplace(['#', 'REG-'], '', $rawSearch), '0');
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $rawSearch);
            $search = '%'.$escapedSearch.'%';
            $query->where(function ($q) use ($search, $cleanId) {
                if ($cleanId !== '' && is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }
                $q->orWhereHas('user', function ($qu) use ($search) {
                    $qu->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('profil', function ($qp) use ($search) {
                            $qp->where('institusi', 'like', $search)
                                ->orWhere('nim_nisn', 'like', $search);
                        });
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        }

        if ($request->filled('jurusan_id')) {
            $jurusanId = $request->integer('jurusan_id');
            $query->where(function ($q) use ($jurusanId) {
                $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusanId))
                    ->orWhereHas('user.profil', fn ($qp) => $qp->where('jurusan_id', $jurusanId));
            });
        }

        if ($request->filled('jalur')) {
            $jalur = $request->input('jalur');
            $query->whereHas('penempatan', fn ($qp) => $qp->where('jalur', $jalur));
        }

        // SLA Watchdog Filter (PRD 2.3 & F-04: target verifikasi <= 5 hari kerja)
        if ($request->filled('sla')) {
            $sla = $request->input('sla');
            if ($sla === 'breach' || $sla === 'terlambat') {
                $query->where('status', StatusPendaftaranEnum::Diajukan)
                    ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(5)]);
            } elseif ($sla === 'mendekati') {
                $query->where('status', StatusPendaftaranEnum::Diajukan)
                    ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(3)])
                    ->whereRaw('COALESCE(diajukan_at, created_at) > ?', [now()->subDays(5)]);
            } elseif ($sla === 'aman') {
                $query->where('status', StatusPendaftaranEnum::Diajukan)
                    ->whereRaw('COALESCE(diajukan_at, created_at) > ?', [now()->subDays(3)]);
            }
        }

        $perPage = (int) $request->input('per_page', 5);
        if (! in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 5;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Menghitung statistik antrean pendaftaran untuk dashboard admin (F-04).
     *
     * @return array<string, int>
     */
    public function getQueueStats(): array
    {
        return [
            'total' => Pendaftaran::count(),
            'total_individu' => Pendaftaran::where(function ($q) {
                $q->where('tipe', 'mandiri')
                    ->orWhere('tipe', 'individu')
                    ->orWhereNull('tipe');
            })->count(),
            'total_kelompok' => Pendaftaran::where('tipe', 'kelompok')->count(),
            'diajukan' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count(),
            'perlu_perbaikan' => Pendaftaran::where('status', StatusPendaftaranEnum::PerluPerbaikan)->count(),
            'diterima' => Pendaftaran::where('status', StatusPendaftaranEnum::Diterima)->count(),
            'ditolak' => Pendaftaran::where('status', StatusPendaftaranEnum::Ditolak)->count(),
            'sla_warning' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
                ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(3)])
                ->count(),
            'sla_breach' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
                ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(5)])
                ->count(),
            'sla_mendekati' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
                ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(3)])
                ->whereRaw('COALESCE(diajukan_at, created_at) > ?', [now()->subDays(5)])
                ->count(),
        ];
    }

    /**
     * Mengambil daftar periode bulan magang untuk filter.
     *
     * @return Collection<int, BulanMagang>
     */
    public function getBulanList(): Collection
    {
        return BulanMagang::orderBy('bulan_mulai', 'desc')->get();
    }

    /**
     * Mengambil daftar jurusan aktif untuk filter.
     *
     * @return Collection<int, Jurusan>
     */
    public function getJurusanList(): Collection
    {
        return Jurusan::cachedAktif();
    }

    /**
     * Mengambil seluruh data pendukung untuk tampilan detail verifikasi admin (show).
     *
     * @return array{
     *     pembimbingList: Collection<int, User>,
     *     bidangList: Collection<int, Bidang>,
     *     sisaKuota: int,
     *     duplikasiNimCount: int
     * }
     */
    public function getShowData(Pendaftaran $pendaftaran): array
    {
        $pendaftaran->load([
            'user.profil.jurusan',
            'bulanMagang',
            'kuota.jurusan',
            'dokumen.jenisDokumen',
            'dokumen.kelompokAnggota',
            'kelompokMagang.anggota',
        ]);

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $bidangList = Bidang::cachedAktif();

        // Hitung sisa kuota jurusan untuk bulan magang pendaftaran terkait (AC-04-4)
        $kuota = $pendaftaran->kuota;
        $sisaKuota = $kuota ? max(0, $kuota->jumlah - $kuota->terisi) : 0;

        // Deteksi duplikasi NIM/NISN lintas akun (SEC-15)
        $profil = $pendaftaran->user?->profil;
        $duplikasiNimCount = 0;
        if ($profil && ! empty($profil->nim_nisn)) {
            $duplikasiNimCount = PesertaProfil::where('nim_nisn', $profil->nim_nisn)
                ->where('user_id', '!=', $pendaftaran->user_id)
                ->count();
        }

        return [
            'pembimbingList' => $pembimbingList,
            'bidangList' => $bidangList,
            'sisaKuota' => $sisaKuota,
            'duplikasiNimCount' => $duplikasiNimCount,
        ];
    }
}
