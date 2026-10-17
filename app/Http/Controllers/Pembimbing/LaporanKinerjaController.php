<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanKinerjaController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan rekapitulasi portofolio bimbingan dan kinerja mentoring ASN (PRD F-09, F-10).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'tugas',
            'logbooks',
            'penilaian.details.rubrik',
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        if ($request->filled('tahun')) {
            $tahun = (int) $request->input('tahun');
            $query->whereYear('tgl_mulai', $tahun);
        }

        $allPenempatans = $query->latest('tgl_mulai')->get();

        $penempatanIds = $allPenempatans->pluck('id')->toArray();

        // 1. Statistik Kinerja Mentoring ASN
        $totalMentee = $allPenempatans->count();
        $totalMenteeAktif = $allPenempatans->where('status', StatusPenempatanEnum::Aktif)->count();
        $totalMenteeSelesai = $allPenempatans->where('status', StatusPenempatanEnum::Selesai)->count();

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

        // Rata-rata nilai kompetensi mentee yang sudah dinilai
        $penilaianScores = $allPenempatans
            ->pluck('penilaian.nilai_akhir')
            ->filter()
            ->map(fn ($score) => (float) $score);

        $rataRataNilai = $penilaianScores->isNotEmpty()
            ? round($penilaianScores->avg(), 2)
            : 0.0;

        // 2. Format rekapitulasi per mentee
        $rekapMentees = $allPenempatans->map(function (Penempatan $p) {
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
                'nilai_akhir' => $p->penilaian?->nilai_akhir,
                'predikat' => $p->penilaian?->predikat,
                'status' => $p->status,
            ];
        });

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: ['aksi' => 'lihat_laporan_kinerja_pembimbing', 'total_mentee' => $totalMentee]
        );

        return view('pembimbing.laporan-kinerja.index', [
            'totalMentee' => $totalMentee,
            'totalMenteeAktif' => $totalMenteeAktif,
            'totalMenteeSelesai' => $totalMenteeSelesai,
            'totalLogbookReviewed' => $totalLogbookReviewed,
            'totalLogbookSubmitted' => $totalLogbookSubmitted,
            'totalTugasDiberikan' => $totalTugasDiberikan,
            'totalTugasSelesai' => $totalTugasSelesai,
            'rasioTugasSelesai' => $rasioTugasSelesai,
            'rataRataNilai' => $rataRataNilai,
            'rekapMentees' => $rekapMentees,
            'pembimbingUser' => $user,
        ]);
    }

    /**
     * Tampilan cetak resmi LPJ mentoring ASN ramah printer (Print / PDF).
     */
    public function cetak(Request $request): View
    {
        $user = $request->user();

        $query = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'tugas',
            'logbooks',
            'penilaian',
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('tahun')) {
            $query->whereYear('tgl_mulai', (int) $request->input('tahun'));
        }

        $allPenempatans = $query->latest('tgl_mulai')->get();

        return view('pembimbing.laporan-kinerja.cetak', [
            'allPenempatans' => $allPenempatans,
            'pembimbingUser' => $user,
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }
}
