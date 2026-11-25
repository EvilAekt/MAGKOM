<?php

declare(strict_types=1);

namespace App\Services\Pembimbing;

use App\Enums\StatusTugasEnum;
use App\Models\KelompokMagang;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;
use App\Queries\Pembimbing\PenempatanAktifQuery;
use App\Queries\Pembimbing\TugasIndexQuery;
use App\Services\Shared\BulanMagangService;
use Illuminate\Http\Request;

class TugasDashboardService
{
    public function __construct(
        protected TugasIndexQuery $tugasIndexQuery,
        protected PenempatanAktifQuery $penempatanAktifQuery,
        protected BulanMagangService $bulanMagangService
    ) {}

    /**
     * Mengumpulkan dan menyusun seluruh data penugasan, paginasi mentee, dan metrik agregat.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(Request $request, User $user): array
    {
        // 1. Paginasi daftar tugas (10 per halaman)
        $tugasList = $this->tugasIndexQuery->build($request, $user)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 2. Daftar seluruh penempatan mentee individu aktif untuk dropdown & filter
        $penempatanBase = $this->penempatanAktifQuery->baseQuery($user);
        $allPenempatan = $this->penempatanAktifQuery
            ->scopeIndividuOnly(clone $penempatanBase)
            ->latest('id')
            ->get();

        // 3. Paginasi kartu mentee (10 per halaman)
        $penempatanList = $this->penempatanAktifQuery->buildFiltered($request, $user)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // 4. Hitung metrik agregat status tugas untuk banner dan tab classroom
        $baseMetricsQuery = Tugas::where(function ($root) use ($user) {
            $root->whereHas('penempatan', function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })->orWhere(function ($q) use ($user) {
                $q->where('tipe', 'kelompok')
                    ->whereHas('kelompokMagang', function ($qk) use ($user) {
                        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                            $qk->whereHas('pendaftaran.penempatan', function ($qp) use ($user) {
                                $qp->where('pembimbing_id', $user->id);
                            });
                        }
                    });
            });
        });

        if ($request->filled('penempatan_id')) {
            $penId = (int) $request->input('penempatan_id');
            $baseMetricsQuery->where(function ($q) use ($penId) {
                $q->where('penempatan_id', $penId)
                    ->orWhereHas('tugasPeserta', fn ($tp) => $tp->where('penempatan_id', $penId));
            });
        }

        $counts = [
            'total' => (clone $baseMetricsQuery)->count(),
            'belum' => (clone $baseMetricsQuery)->where('status', StatusTugasEnum::Belum)->count(),
            'proses' => (clone $baseMetricsQuery)->where('status', StatusTugasEnum::Proses)->count(),
            'selesai' => (clone $baseMetricsQuery)->where('status', StatusTugasEnum::Selesai)->count(),
            'perlu_review' => (clone $baseMetricsQuery)->whereHas('progresLogs', function ($qp) {
                $qp->where('status_review', 'menunggu');
            })->count(),
            'individu' => (clone $baseMetricsQuery)->where('tipe', 'individu')->count(),
            'kelompok' => (clone $baseMetricsQuery)->where('tipe', 'kelompok')->count(),
        ];

        // 4b. Ambil daftar kelompok bimbingan unik binaan pembimbing ini
        $kelompokIds = (clone $penempatanBase)
            ->whereHas('pendaftaran', fn ($qp) => $qp->whereNotNull('kelompok_magang_id'))
            ->with('pendaftaran')
            ->get()
            ->pluck('pendaftaran.kelompok_magang_id')
            ->filter()
            ->unique();

        $kelompokQuery = KelompokMagang::with(['ketua', 'anggota.user'])
            ->whereIn('id', $kelompokIds)
            ->withCount(['anggota']);

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $kelompokQuery->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', $search)
                    ->orWhereHas('ketua', fn ($qk) => $qk->where('nama', 'like', $search));
            });
        }

        $kelompokList = $request->filled('penempatan_id')
            ? collect()
            : $kelompokQuery->get();

        $individuCount = $allPenempatan->count();
        $kelompokTotalCount = ($request->filled('q') || $request->filled('penempatan_id'))
            ? KelompokMagang::whereIn('id', $kelompokIds)->count()
            : $kelompokList->count();

        $filterCounts = [
            'semua' => $individuCount + $kelompokTotalCount,
            'individu' => $individuCount,
            'kelompok' => $kelompokTotalCount,
        ];

        // 5. Penentuan entitas penempatan terpilih (filter aktif)
        $selectedPenempatanId = $request->integer('penempatan_id') ?: null;
        $selectedPenempatan = null;
        if ($selectedPenempatanId) {
            $selectedPenempatan = $penempatanList->firstWhere('id', $selectedPenempatanId)
                ?? $allPenempatan->firstWhere('id', $selectedPenempatanId);
        }

        // 6. Penentuan entitas penempatan masuk (mode detail satu mentee)
        $masukPenempatanId = $request->integer('masuk') ?: null;
        $masukPenempatan = null;
        if ($masukPenempatanId) {
            $masukPenempatan = $penempatanList->firstWhere('id', $masukPenempatanId)
                ?? $allPenempatan->firstWhere('id', $masukPenempatanId);

            if (! $masukPenempatan) {
                $masukPenempatan = Penempatan::with([
                    'peserta.profil.jurusan',
                    'bidang',
                    'pendaftaran',
                    'tugas' => function ($q) {
                        $q->with('progresLogs')->latest();
                    },
                    'projectBersama' => function ($q) {
                        $q->with('progresLogs')->latest();
                    },
                ])
                    ->where(function ($q) use ($user) {
                        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                            $q->where('pembimbing_id', $user->id);
                        }
                    })
                    ->firstWhere('id', $masukPenempatanId);
            }
        }

        // 6b. Penentuan entitas kelompok masuk (mode detail satu kelompok)
        $masukKelompokId = $request->integer('masuk_kelompok') ?: null;
        $masukKelompok = null;
        $masukKelompokMembers = collect();
        $masukKelompokPenempatan = collect();
        if ($masukKelompokId && $kelompokIds->contains($masukKelompokId)) {
            $masukKelompok = KelompokMagang::with([
                'ketua.profil.jurusan',
                'anggota.user.profil.jurusan',
            ])->find($masukKelompokId);

            if ($masukKelompok) {
                $masukKelompokMembers = collect([$masukKelompok->ketua])
                    ->merge($masukKelompok->anggota->pluck('user')->filter())
                    ->unique('id');

                $masukKelompokMemberIds = $masukKelompokMembers->pluck('id')->all();

                $masukKelompokPenempatan = (clone $penempatanBase)
                    ->whereIn('peserta_id', $masukKelompokMemberIds)
                    ->get();
            }
        }

        // 7. Resolusi tab jenis tugas aktif dengan deteksi kontekstual cerdas
        $jenisTugas = $request->input('jenis_tugas');
        if (! $jenisTugas) {
            if ($masukKelompokId || $request->filled('dari_kelompok')) {
                $jenisTugas = 'kelompok';
            } elseif ($masukPenempatan) {
                $jenisTugas = $masukPenempatan->isKelompok() ? 'kelompok' : 'individu';
            } else {
                $jenisTugas = 'semua';
            }
        }

        return [
            'tugasList' => $tugasList,
            'penempatanList' => $penempatanList,
            'allPenempatan' => $allPenempatan,
            'counts' => $counts,
            'filterCounts' => $filterCounts,
            'selectedPenempatanId' => $selectedPenempatanId,
            'selectedPenempatan' => $selectedPenempatan,
            'masukPenempatanId' => $masukPenempatanId,
            'masukPenempatan' => $masukPenempatan,
            'masukKelompokId' => $masukKelompokId,
            'masukKelompok' => $masukKelompok,
            'masukKelompokMembers' => $masukKelompokMembers,
            'masukKelompokPenempatan' => $masukKelompokPenempatan,
            'daftarBulan' => $this->bulanMagangService->getDaftarBulan(),
            'kelompokList' => $kelompokList,
            'jenisTugas' => $jenisTugas,
        ];
    }
}
