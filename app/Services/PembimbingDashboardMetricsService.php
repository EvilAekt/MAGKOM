<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Models\AgendaBimbingan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PenilaianDetail;
use App\Models\PenilaianRiwayat;
use App\Models\ProgresLog;
use App\Models\Rubrik;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PembimbingDashboardMetricsService
{
    /**
     * Membangun seluruh data metrik untuk 4 Tab Dashboard Pembimbing (PRD F-08, F-09, F-10, F-12).
     *
     * @return array<string, mixed>
     */
    public function buildDashboardData(User $user): array
    {
        // 1. Kapasitas & Beban Mentee Aktif (BR-13)
        $menteeAktifQuery = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'penilaian',
            'tugas',
            'logbooks',
        ])->where('status', StatusPenempatanEnum::Aktif);

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $menteeAktifQuery->where('pembimbing_id', $user->id);
        }

        $menteeAktifList = $menteeAktifQuery->latest('tgl_mulai')->get();
        $totalMenteeAktif = $menteeAktifList->count();

        $maxBebanSetting = Pengaturan::where('kunci', 'maks_peserta_aktif_per_pembimbing')->value('nilai');
        $maxBebanKapasitas = $maxBebanSetting ? (int) $maxBebanSetting : 10;
        $persentaseKapasitas = $maxBebanKapasitas > 0 ? min(100, (int) round(($totalMenteeAktif / $maxBebanKapasitas) * 100)) : 0;

        $penempatanIds = $menteeAktifList->pluck('id')->toArray();

        // Seluruh penempatan untuk Tab 4 Evaluasi Kelulusan
        $allPenempatanQuery = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'penilaian.riwayat.pengubah',
        ]);

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $allPenempatanQuery->where('pembimbing_id', $user->id);
        }
        $allPenempatanList = $allPenempatanQuery->latest('updated_at')->get();

        // 2. Metrik Logbook Menunggu Reviu
        $allPendingLogbooks = Logbook::with(['penempatan.peserta.profil'])
            ->whereIn('penempatan_id', $penempatanIds)
            ->where('status', StatusLogbookEnum::Terkirim)
            ->latest('tanggal')
            ->get();
        $logbookPendingCount = $allPendingLogbooks->count();

        // 3. Metrik Laporan Progres Menunggu Evaluasi
        $allPendingProgresLogs = ProgresLog::with(['tugas.penempatan.peserta.profil'])
            ->whereHas('tugas', function ($q) use ($penempatanIds) {
                $q->whereIn('penempatan_id', $penempatanIds);
            })
            ->where('status_review', StatusReviewProgresEnum::Menunggu)
            ->latest('created_at')
            ->get();
        $progresPendingCount = $allPendingProgresLogs->count();

        // 4. Metrik Tugas Aktif vs Overdue & Upcoming Deadlines (BR-16)
        $tugasMetrics = $this->resolveTugasMetrics($penempatanIds);

        // 5. Deteksi Kedisiplinan Logbook Mentee (BR-09 Alert)
        $inactiveLogbookMentees = $this->resolveInactiveLogbookMentees($menteeAktifList);

        // 6. Distribusi Jenjang Mentee Aktif
        $jenjangCounts = $this->resolveJenjangCounts($menteeAktifList);

        // 7. Pending Action Center untuk Tab 1
        $pendingProgresLogs = $allPendingProgresLogs->take(5);
        $pendingLogbooks = $allPendingLogbooks->take(5);

        // 8. Off-boarding Tracker
        $offboardingMentees = $menteeAktifList->filter(function (Penempatan $p) {
            if (! $p->tgl_selesai) {
                return false;
            }
            $diffDays = Carbon::today()->diffInDays($p->tgl_selesai, false);

            return $diffDays <= 14 && $diffDays >= -7;
        })->sortBy('tgl_selesai')->values();

        // 9. Riwayat Revisi Nilai Final Terbaru untuk Tab 4 (AC-10-3)
        $penilaianIds = $allPenempatanList->pluck('penilaian.id')->filter()->toArray();
        $recentNilaiRevisions = PenilaianRiwayat::with(['penilaian.penempatan.peserta', 'pengubah'])
            ->whereIn('penilaian_id', $penilaianIds)
            ->latest('waktu')
            ->take(10)
            ->get();

        // 10. Radar Capaian Kompetensi Mentee Bidang
        $competencyRadar = $this->resolveCompetencyRadar($penempatanIds);

        // 11. Deteksi Mentee Berisiko (Early Warning)
        $menteeAtRiskList = $this->resolveMenteeAtRiskList($menteeAktifList);

        // 12. Agenda Penjadwalan Mentoring Mendatang
        $agendaQuery = AgendaBimbingan::with(['penempatan.peserta.profil'])
            ->where('tgl_bimbingan', '>=', Carbon::today()->toDateString())
            ->where('status', '!=', 'dibatalkan');
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $agendaQuery->where('pembimbing_id', $user->id);
        }
        $upcomingAgendaBimbingan = $agendaQuery->orderBy('tgl_bimbingan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();

        // 13. Aktivitas Mentoring Minggu Ini
        $aktivitasMingguIni = $this->resolveAktivitasMingguIni($allPendingLogbooks, $allPendingProgresLogs, $upcomingAgendaBimbingan);

        return [
            'totalMenteeAktif' => $totalMenteeAktif,
            'maxBebanKapasitas' => $maxBebanKapasitas,
            'persentaseKapasitas' => $persentaseKapasitas,
            'logbookPendingCount' => $logbookPendingCount,
            'logbookPerluReviewCount' => $logbookPendingCount,
            'progresPendingCount' => $progresPendingCount,
            'progresBaruCount' => $progresPendingCount,
            'tugasAktifCount' => $tugasMetrics['tugasAktifCount'],
            'tugasOverdueCount' => $tugasMetrics['tugasOverdueCount'],
            'deadlineMendekatiHari' => $tugasMetrics['deadlineMendekatiHari'],
            'upcomingDeadlines' => $tugasMetrics['upcomingDeadlines'],
            'inactiveLogbookMentees' => $inactiveLogbookMentees,
            'jenjangCounts' => $jenjangCounts,
            'pendingProgresLogs' => $pendingProgresLogs,
            'allPendingProgresLogs' => $allPendingProgresLogs,
            'pendingLogbooks' => $pendingLogbooks,
            'allPendingLogbooks' => $allPendingLogbooks,
            'offboardingMentees' => $offboardingMentees,
            'menteeAktifList' => $menteeAktifList,
            'penempatanList' => $menteeAktifList,
            'allPenempatanList' => $allPenempatanList,
            'recentNilaiRevisions' => $recentNilaiRevisions,
            'pembimbingBidang' => $user->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman',
            'competencyRadar' => $competencyRadar,
            'menteeAtRiskList' => $menteeAtRiskList,
            'upcomingAgendaBimbingan' => $upcomingAgendaBimbingan,
            'aktivitasMingguIni' => $aktivitasMingguIni,
        ];
    }

    /**
     * @param array<int> $penempatanIds
     * @return array{tugasAktifCount: int, tugasOverdueCount: int, deadlineMendekatiHari: int, upcomingDeadlines: Collection}
     */
    protected function resolveTugasMetrics(array $penempatanIds): array
    {
        $tugasAktifList = Tugas::with(['penempatan.peserta.profil'])
            ->whereIn('penempatan_id', $penempatanIds)
            ->whereIn('status', [StatusTugasEnum::Belum, StatusTugasEnum::Proses])
            ->get();

        $tugasAktifCount = $tugasAktifList->count();
        $tugasOverdueCount = $tugasAktifList
            ->filter(fn (Tugas $t) => $t->deadline && Carbon::parse($t->deadline)->isPast() && $t->status !== StatusTugasEnum::Selesai)
            ->count();

        $deadlineSetting = Pengaturan::where('kunci', 'deadline_mendekati_hari')->value('nilai');
        $deadlineMendekatiHari = $deadlineSetting ? (int) $deadlineSetting : 3;
        $today = Carbon::today();
        $maxUpcoming = Carbon::today()->addDays($deadlineMendekatiHari);

        $upcomingDeadlines = $tugasAktifList
            ->filter(function (Tugas $t) use ($today, $maxUpcoming) {
                if (! $t->deadline) {
                    return false;
                }
                $d = Carbon::parse($t->deadline);

                return $d->gte($today) && $d->lte($maxUpcoming);
            })
            ->sortBy('deadline')
            ->values();

        return [
            'tugasAktifCount' => $tugasAktifCount,
            'tugasOverdueCount' => $tugasOverdueCount,
            'deadlineMendekatiHari' => $deadlineMendekatiHari,
            'upcomingDeadlines' => $upcomingDeadlines,
        ];
    }

    protected function resolveInactiveLogbookMentees(Collection $menteeAktifList): Collection
    {
        $today = Carbon::today();
        $inactive = collect();

        foreach ($menteeAktifList as $mentee) {
            $tglMulai = $mentee->tgl_mulai;
            if (! $tglMulai || $tglMulai->isFuture()) {
                continue;
            }

            $lastLogbook = $mentee->logbooks->sortByDesc('tanggal')->first();
            if ($lastLogbook && $lastLogbook->tanggal) {
                $lastDate = Carbon::parse($lastLogbook->tanggal);
                $daysInactive = $lastDate->diffInDays($today, false);
                if ($daysInactive >= 2) {
                    $inactive->push([
                        'mentee' => $mentee,
                        'hari_pasif' => $daysInactive,
                        'terakhir_isi' => $lastDate->format('d M Y'),
                    ]);
                }
            } else {
                $daysSinceStart = $tglMulai->diffInDays($today, false);
                if ($daysSinceStart >= 2) {
                    $inactive->push([
                        'mentee' => $mentee,
                        'hari_pasif' => $daysSinceStart,
                        'terakhir_isi' => 'Belum pernah mengisi',
                    ]);
                }
            }
        }

        return $inactive->sortByDesc('hari_pasif')->values();
    }

    /**
     * @return array<string, int>
     */
    protected function resolveJenjangCounts(Collection $menteeAktifList): array
    {
        $counts = ['smk' => 0, 'd3' => 0, 's1' => 0];
        foreach ($menteeAktifList as $mentee) {
            $jenjangVal = $mentee->peserta?->profil?->jenjang?->value ?? ($mentee->peserta?->profil?->jenjang ?? null);
            if ($jenjangVal && isset($counts[$jenjangVal])) {
                $counts[$jenjangVal]++;
            }
        }

        return $counts;
    }

    /**
     * @param array<int> $penempatanIds
     */
    protected function resolveCompetencyRadar(array $penempatanIds): Collection
    {
        $rubrikList = Rubrik::where('aktif', true)->get();

        return $rubrikList->map(function ($r) use ($penempatanIds) {
            $avgScore = PenilaianDetail::where('rubrik_id', $r->id)
                ->whereHas('penilaian', fn ($q) => $q->whereIn('penempatan_id', $penempatanIds))
                ->avg('skor');

            return [
                'kriteria' => $r->kriteria,
                'bobot' => $r->bobot,
                'skor_rata' => $avgScore ? round((float) $avgScore, 1) : 85.0,
            ];
        });
    }

    protected function resolveMenteeAtRiskList(Collection $menteeAktifList): Collection
    {
        $atRisk = collect();

        foreach ($menteeAktifList as $mentee) {
            $overdueCount = $mentee->tugas->filter(fn (Tugas $t) => $t->deadline && Carbon::parse($t->deadline)->isPast() && $t->status !== StatusTugasEnum::Selesai)->count();
            $lastLogbook = $mentee->logbooks->sortByDesc('tanggal')->first();
            $daysNoLogbook = 0;
            if ($lastLogbook && $lastLogbook->tanggal) {
                $daysNoLogbook = Carbon::parse($lastLogbook->tanggal)->diffInDays(Carbon::today(), false);
            } elseif ($mentee->tgl_mulai) {
                $daysNoLogbook = $mentee->tgl_mulai->diffInDays(Carbon::today(), false);
            }

            if ($overdueCount > 0 || $daysNoLogbook >= 2) {
                $riskLevel = ($overdueCount > 0 && $daysNoLogbook >= 3) ? 'Tinggi' : 'Sedang';
                $atRisk->push([
                    'mentee' => $mentee,
                    'overdue_count' => $overdueCount,
                    'days_no_logbook' => max(0, (int) $daysNoLogbook),
                    'risk_level' => $riskLevel,
                ]);
            }
        }

        return $atRisk->sortByDesc(fn ($item) => $item['risk_level'] === 'Tinggi' ? 2 : 1)->values();
    }

    protected function resolveAktivitasMingguIni(Collection $allPendingLogbooks, Collection $allPendingProgresLogs, Collection $upcomingAgendaBimbingan): Collection
    {
        $aktivitas = collect();

        foreach ($allPendingLogbooks->take(3) as $lb) {
            $aktivitas->push([
                'tipe' => 'logbook',
                'judul' => 'Pengajuan Logbook: ' . ($lb->penempatan?->peserta?->nama ?? 'Peserta'),
                'keterangan' => 'Minggu ke-' . ($lb->minggu_ke ?? 1) . ' • ' . ($lb->ringkasan_aktivitas ?? 'Aktivitas magang harian'),
                'waktu' => $lb->created_at ?? now(),
                'badge' => 'Logbook',
                'badge_color' => '#D97706',
                'badge_bg' => '#FEF3C7',
            ]);
        }

        foreach ($allPendingProgresLogs->take(3) as $pr) {
            $aktivitas->push([
                'tipe' => 'progres',
                'judul' => 'Unggahan Progres: ' . ($pr->tugas?->penempatan?->peserta?->nama ?? 'Peserta'),
                'keterangan' => ($pr->tugas?->judul ?? 'Tugas') . ' • ' . ($pr->catatan ?? 'Menunggu reviu evaluasi'),
                'waktu' => $pr->created_at ?? now(),
                'badge' => 'Tugas',
                'badge_color' => '#2563EB',
                'badge_bg' => '#EFF6FF',
            ]);
        }

        foreach ($upcomingAgendaBimbingan->take(3) as $ag) {
            $aktivitas->push([
                'tipe' => 'agenda',
                'judul' => 'Agenda Mentoring: ' . ($ag->topik ?? 'Bimbingan Lapangan'),
                'keterangan' => ($ag->penempatan?->peserta?->nama ?? 'Peserta') . ' • Jam ' . ($ag->jam_mulai ?? '09:00') . ' WIB',
                'waktu' => $ag->created_at ?? now(),
                'badge' => 'Agenda',
                'badge_color' => '#059669',
                'badge_bg' => '#ECFDF5',
            ]);
        }

        return $aktivitas->sortByDesc('waktu')->values()->take(6);
    }
}
