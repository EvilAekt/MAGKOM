<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\SurveiKepuasan;
use App\Services\AuditService;
use App\Services\ExportReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveiAdminController extends Controller
{
    /**
     * Menampilkan dasbor rekapitulasi Indeks Kepuasan Magang (IKM) dan NPS (PRD 4.3 Poin 4).
     */
    public function index(Request $request): View
    {
        $totalResponden = SurveiKepuasan::count();

        $avgMentoring = $totalResponden > 0 ? round((float) SurveiKepuasan::avg('skor_mentoring'), 2) : 0.0;
        $avgTugas = $totalResponden > 0 ? round((float) SurveiKepuasan::avg('skor_tugas'), 2) : 0.0;
        $avgFasilitas = $totalResponden > 0 ? round((float) SurveiKepuasan::avg('skor_fasilitas'), 2) : 0.0;

        $kompositIkm = round(($avgMentoring + $avgTugas + $avgFasilitas) / 3, 2);
        $ikmSkala100 = round(($kompositIkm / 5) * 100, 1);

        // Perhitungan Net Promoter Score (NPS)
        $promoters = SurveiKepuasan::where('skor_nps', '>=', 9)->count();
        $passives = SurveiKepuasan::whereBetween('skor_nps', [7, 8])->count();
        $detractors = SurveiKepuasan::where('skor_nps', '<=', 6)->count();

        $npsScore = $totalResponden > 0
            ? round((($promoters - $detractors) / $totalResponden) * 100, 1)
            : 0.0;

        $daftarSurvei = SurveiKepuasan::with(['penempatan.peserta.profil', 'penempatan.bidang', 'penempatan.pembimbing'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.survei.index', [
            'totalResponden' => $totalResponden,
            'avgMentoring' => $avgMentoring,
            'avgTugas' => $avgTugas,
            'avgFasilitas' => $avgFasilitas,
            'kompositIkm' => $kompositIkm,
            'ikmSkala100' => $ikmSkala100,
            'npsScore' => $npsScore,
            'promoters' => $promoters,
            'passives' => $passives,
            'detractors' => $detractors,
            'daftarSurvei' => $daftarSurvei,
        ]);
    }

    /**
     * Ekspor rekapitulasi survei kepuasan magang ke format CSV ber-sanitasi formula injection.
     */
    public function export(ExportReportService $exportService, AuditService $auditService): StreamedResponse
    {
        $surveiList = SurveiKepuasan::with(['penempatan.peserta.profil', 'penempatan.bidang'])->latest()->get();

        $headers = [
            'Nama Peserta',
            'Institusi',
            'Bidang Magang',
            'Skor Mentoring (1-5)',
            'Skor Tugas (1-5)',
            'Skor Fasilitas (1-5)',
            'Skor NPS (0-10)',
            'Umpan Balik Berkesan',
            'Saran Perbaikan',
            'Tanggal Isi',
        ];

        $rows = [];
        foreach ($surveiList as $s) {
            $penempatan = $s->penempatan;
            $peserta = $penempatan?->peserta;

            $rows[] = [
                $peserta?->nama ?? $peserta?->name ?? 'Peserta',
                $peserta?->profil?->institusi ?? '-',
                $penempatan?->bidang?->nama_bidang ?? 'Umum',
                (string) $s->skor_mentoring,
                (string) $s->skor_tugas,
                (string) $s->skor_fasilitas,
                (string) $s->skor_nps,
                $s->umpan_balik ?? '-',
                $s->saran ?? '-',
                $s->created_at->format('Y-m-d H:i'),
            ];
        }

        $auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'survei_kepuasan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'ekspor_survei_kepuasan_csv',
                'total_baris' => count($rows),
            ]
        );

        return $exportService->downloadCsvStream(
            filename: 'rekap-survei-kepuasan-magkom-' . date('Ymd') . '.csv',
            headers: $headers,
            rows: $rows
        );
    }
}
