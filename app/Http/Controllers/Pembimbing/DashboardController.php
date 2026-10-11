<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Http\Controllers\Controller;
use App\Models\AgendaBimbingan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PenilaianDetail;
use App\Models\PenilaianRiwayat;
use App\Models\ProgresLog;
use App\Models\Rubrik;
use App\Models\Tugas;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilan utama Dashboard Operasional Pembimbing (F-08, F-09, F-10, F-12, BR-13, TDD Bab 4).
     * Mendukung 4-Tab Eksekutif:
     * - Tab 1: Ringkasan Utama (Figma Default Node 560-41611)
     * - Tab 2: Pusat Antrean Reviu Cepat (Action Hub)
     * - Tab 3: Monitoring Mentee 360° & Progres Magang
     * - Tab 4: Evaluasi Rubrik & Capaian Magang (Masa Tenggang BR-11)
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        // 1. Kapasitas & Beban Mentee Aktif (BR-13)
        $menteeAktifQuery = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'penilaian',
            'tugas',
            'logbooks',
        ])
            ->where('status', StatusPenempatanEnum::Aktif);

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $menteeAktifQuery->where('pembimbing_id', $user->id);
        }

        $menteeAktifList = $menteeAktifQuery->latest('tgl_mulai')->get();
        $totalMenteeAktif = $menteeAktifList->count();

        $maxBebanSetting = Pengaturan::where('kunci', 'maks_peserta_aktif_per_pembimbing')->value('nilai');
        $maxBebanKapasitas = $maxBebanSetting ? (int) $maxBebanSetting : 10;
        $persentaseKapasitas = $maxBebanKapasitas > 0 ? min(100, (int) round(($totalMenteeAktif / $maxBebanKapasitas) * 100)) : 0;

        $penempatanIds = $menteeAktifList->pluck('id')->toArray();

        // Seluruh penempatan (aktif + selesai) untuk Tab 4 Evaluasi Kelulusan
        $allPenempatanQuery = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'penilaian.riwayat.pengubah',
        ]);

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $allPenempatanQuery->where('pembimbing_id', $user->id);
        }
        $allPenempatanList = $allPenempatanQuery->latest('updated_at')->get();

        // 2. Metrik Logbook Menunggu Reviu (status = 'terkirim')
        $allPendingLogbooks = Logbook::with(['penempatan.peserta.profil'])
            ->whereIn('penempatan_id', $penempatanIds)
            ->where('status', StatusLogbookEnum::Terkirim)
            ->latest('tanggal')
            ->get();
        $logbookPendingCount = $allPendingLogbooks->count();

        // 3. Metrik Laporan Progres Menunggu Evaluasi (status_review = 'menunggu')
        $allPendingProgresLogs = ProgresLog::with(['tugas.penempatan.peserta.profil'])
            ->whereHas('tugas', function ($q) use ($penempatanIds) {
                $q->whereIn('penempatan_id', $penempatanIds);
            })
            ->where('status_review', StatusReviewProgresEnum::Menunggu)
            ->latest('created_at')
            ->get();
        $progresPendingCount = $allPendingProgresLogs->count();

        // 4. Metrik Tugas Aktif vs Overdue & Upcoming Deadlines (BR-16)
        $tugasAktifQuery = Tugas::with(['penempatan.peserta.profil'])
            ->whereIn('penempatan_id', $penempatanIds)
            ->whereIn('status', [StatusTugasEnum::Belum, StatusTugasEnum::Proses]);

        $tugasAktifList = (clone $tugasAktifQuery)->get();
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

        // 5. Deteksi Kedisiplinan Logbook Mentee (BR-09 Alert)
        // Mendeteksi mentee aktif yang belum mengisi logbook >= 2 hari terakhir
        $inactiveLogbookMentees = collect();
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
                    $inactiveLogbookMentees->push([
                        'mentee' => $mentee,
                        'hari_pasif' => $daysInactive,
                        'terakhir_isi' => $lastDate->format('d M Y'),
                    ]);
                }
            } else {
                $daysSinceStart = $tglMulai->diffInDays($today, false);
                if ($daysSinceStart >= 2) {
                    $inactiveLogbookMentees->push([
                        'mentee' => $mentee,
                        'hari_pasif' => $daysSinceStart,
                        'terakhir_isi' => 'Belum pernah mengisi',
                    ]);
                }
            }
        }
        $inactiveLogbookMentees = $inactiveLogbookMentees->sortByDesc('hari_pasif')->values();

        // 6. Distribusi Jenjang Mentee Aktif
        $jenjangCounts = ['smk' => 0, 'd3' => 0, 's1' => 0];
        foreach ($menteeAktifList as $mentee) {
            $jenjangVal = $mentee->peserta?->profil?->jenjang?->value ?? ($mentee->peserta?->profil?->jenjang ?? null);
            if ($jenjangVal && isset($jenjangCounts[$jenjangVal])) {
                $jenjangCounts[$jenjangVal]++;
            }
        }

        // 7. Pending Action Center untuk Tab 1 (5 Progres Tugas & 5 Logbook Terbaru)
        $pendingProgresLogs = $allPendingProgresLogs->take(5);
        $pendingLogbooks = $allPendingLogbooks->take(5);

        // 8. Off-boarding Tracker (Mentee aktif yang mendekati tgl_selesai <= 14 hari)
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
        $rubrikList = Rubrik::where('aktif', true)->get();
        $competencyRadar = $rubrikList->map(function ($r) use ($penempatanIds) {
            $avgScore = PenilaianDetail::where('rubrik_id', $r->id)
                ->whereHas('penilaian', fn ($q) => $q->whereIn('penempatan_id', $penempatanIds))
                ->avg('skor');

            return [
                'kriteria' => $r->kriteria,
                'bobot' => $r->bobot,
                'skor_rata' => $avgScore ? round((float) $avgScore, 1) : 85.0,
            ];
        });

        // 11. Deteksi Mentee Berisiko / Butuh Perhatian Khusus (Early Warning At-Risk)
        $menteeAtRiskList = collect();
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
                $menteeAtRiskList->push([
                    'mentee' => $mentee,
                    'overdue_count' => $overdueCount,
                    'days_no_logbook' => max(0, (int) $daysNoLogbook),
                    'risk_level' => $riskLevel,
                ]);
            }
        }
        $menteeAtRiskList = $menteeAtRiskList->sortByDesc(fn ($item) => $item['risk_level'] === 'Tinggi' ? 2 : 1)->values();

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

        $data = [
            'totalMenteeAktif' => $totalMenteeAktif,
            'maxBebanKapasitas' => $maxBebanKapasitas,
            'persentaseKapasitas' => $persentaseKapasitas,
            'logbookPendingCount' => $logbookPendingCount,
            'progresPendingCount' => $progresPendingCount,
            'tugasAktifCount' => $tugasAktifCount,
            'tugasOverdueCount' => $tugasOverdueCount,
            'deadlineMendekatiHari' => $deadlineMendekatiHari,
            'upcomingDeadlines' => $upcomingDeadlines,
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
        ];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('pembimbing.dashboard', $data);
    }
}
