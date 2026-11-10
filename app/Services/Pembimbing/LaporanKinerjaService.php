<?php

declare(strict_types=1);

namespace App\Services\Pembimbing;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;
use App\Queries\Pembimbing\LaporanKinerjaQuery;
use App\Services\Shared\BulanMagangService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanKinerjaService
{
    public function __construct(
        protected LaporanKinerjaQuery $laporanQuery,
        protected BulanMagangService $bulanMagangService
    ) {}

    /**
     * Mengumpulkan metrik, statistik Gentelella tile, rekapitulasi mentee terpaginasi, dan opsi filter.
     *
     * @return array<string, mixed>
     */
    public function getLaporanData(Request $request, User $user): array
    {
        $query = $this->laporanQuery->build($request, $user);
        $allPenempatans = $query->latest('tgl_mulai')->get();
        $penempatanIds = $allPenempatans->pluck('id')->toArray();
        $today = Carbon::today();

        // 1. Evaluasi 4 Metrik Utama Gentelella Tile Stats
        $allAktifMentees = Penempatan::with(['tugas', 'logbooks'])
            ->where('status', StatusPenempatanEnum::Aktif)
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->get();

        $perhatianIds = [];
        foreach ($allAktifMentees as $mentee) {
            $hasOverdueTask = $mentee->tugas->contains(function ($t) {
                return $t->deadline && Carbon::parse($t->deadline)->isPast() && $t->status !== StatusTugasEnum::Selesai;
            });

            $logbookBolong = false;
            if ($mentee->tgl_mulai && $mentee->tgl_mulai->isPast()) {
                $lastLogbook = $mentee->logbooks->sortByDesc('tanggal')->first();
                if ($lastLogbook && $lastLogbook->tanggal) {
                    $daysSinceLogbook = Carbon::parse($lastLogbook->tanggal)->diffInDays($today, false);
                    if ($daysSinceLogbook >= 3) {
                        $logbookBolong = true;
                    }
                } else {
                    $daysSinceStart = $mentee->tgl_mulai->diffInDays($today, false);
                    if ($daysSinceStart >= 3) {
                        $logbookBolong = true;
                    }
                }
            }

            if ($hasOverdueTask || $logbookBolong) {
                $perhatianIds[] = $mentee->id;
            }
        }

        $countAktif = $allPenempatans->where('status', StatusPenempatanEnum::Aktif)->count();
        $countSelesai = $allPenempatans->where('status', StatusPenempatanEnum::Selesai)->count();
        $countPerhatian = count($perhatianIds);
        $totalMentee = $countAktif + $countSelesai;

        $persenAktif = $totalMentee > 0 ? (int) round(($countAktif / $totalMentee) * 100) : 0;
        $persenPerhatian = $countAktif > 0 ? (int) round(($countPerhatian / $countAktif) * 100) : 0;
        $persenSelesai = $totalMentee > 0 ? (int) round(($countSelesai / $totalMentee) * 100) : 0;

        // Logbook stats
        $totalLogbookReviewed = Logbook::whereIn('penempatan_id', $penempatanIds)
            ->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])
            ->count();
        $totalLogbookSubmitted = Logbook::whereIn('penempatan_id', $penempatanIds)->count();

        // Tugas stats
        $totalTugasDiberikan = Tugas::whereIn('penempatan_id', $penempatanIds)->count();
        $totalTugasSelesai = Tugas::whereIn('penempatan_id', $penempatanIds)
            ->where('status', StatusTugasEnum::Selesai)
            ->count();

        $rasioTugasSelesai = $totalTugasDiberikan > 0
            ? round(($totalTugasSelesai / $totalTugasDiberikan) * 100, 1)
            : 0.0;

        $rataRataNilai = 0.0;

        // 2. Format rekapitulasi per mentee dengan paginasi server-side
        $perPage = $request->integer('per_page', 5);
        if (! in_array($perPage, [5, 10, 25], true)) {
            $perPage = 5;
        }

        $rekapMentees = (clone $query)->latest('tgl_mulai')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Penempatan $p) {
                $totalTugas = $p->tugas->count();
                $tugasSelesai = $p->tugas->where('status', StatusTugasEnum::Selesai)->count();
                $totalLogbook = $p->logbooks->count();
                $logbookReviewed = $p->logbooks->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])->count();

                $tglMulai = $p->tgl_mulai;
                $tglSelesai = $p->tgl_selesai;
                $durasiHari = $tglMulai && $tglSelesai ? max(1, $tglMulai->diffInDays($tglSelesai)) : 0;

                return [
                    'penempatan' => $p,
                    'total_tugas' => $totalTugas,
                    'tugas_selesai' => $tugasSelesai,
                    'total_logbook' => $totalLogbook,
                    'logbook_reviewed' => $logbookReviewed,
                    'durasi_hari' => $durasiHari,
                    'nilai_akhir' => null,
                    'predikat' => null,
                    'status' => $p->status,
                ];
            });

        $penempatanOptions = Penempatan::with(['peserta.profil'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->latest('id')
            ->get();

        return [
            'totalMentee' => $totalMentee,
            'countAktif' => $countAktif,
            'countSelesai' => $countSelesai,
            'countPerhatian' => $countPerhatian,
            'persenAktif' => $persenAktif,
            'persenPerhatian' => $persenPerhatian,
            'persenSelesai' => $persenSelesai,
            'totalMenteeAktif' => $countAktif,
            'totalMenteeSelesai' => $countSelesai,
            'totalLogbookReviewed' => $totalLogbookReviewed,
            'totalLogbookSubmitted' => $totalLogbookSubmitted,
            'totalTugasDiberikan' => $totalTugasDiberikan,
            'totalTugasSelesai' => $totalTugasSelesai,
            'rasioTugasSelesai' => $rasioTugasSelesai,
            'rataRataNilai' => $rataRataNilai,
            'rekapMentees' => $rekapMentees,
            'pembimbingUser' => $user,
            'penempatanOptions' => $penempatanOptions,
            'daftarBulan' => $this->bulanMagangService->getDaftarBulan(),
        ];
    }
}
