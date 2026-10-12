<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Services\AuditService;
use App\Services\ExportReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalitikTahunanController extends Controller
{
    /**
     * Dashboard analitik rekapitulasi tahunan eksekutif (PRD 4.3 Poin 5, Bab 2.3, A-20).
     */
    public function index(Request $request): View
    {
        $tahun = (int) ($request->input('tahun') ?? date('Y'));
        $data = $this->hitungMetrikTahunan($tahun, $request->user());

        return view('admin.laporan.tahunan', [
            'tahun' => $tahun,
            'daftarTahun' => $this->getDaftarTahun(),
            'canViewNilai' => $request->user()->role === RoleEnum::SuperAdmin,
            ...$data,
        ]);
    }

    /**
     * Ekspor CSV sanitized laporan rekapitulasi tahunan eksekutif.
     */
    public function export(Request $request, ExportReportService $exportService, AuditService $auditService): StreamedResponse
    {
        $tahun = (int) ($request->input('tahun') ?? date('Y'));
        $user = $request->user();
        $canViewNilai = $user->role === RoleEnum::SuperAdmin;
        $data = $this->hitungMetrikTahunan($tahun, $user);

        $headers = ['Indikator / Bulan', 'Jumlah Pendaftar', 'Peserta Diterima', 'Peserta Lulus', 'Tingkat Penerimaan (%)'];
        if ($canViewNilai) {
            $headers[] = 'Rata-rata Nilai';
        }

        $rows = [];
        foreach ($data['trenBulanan'] as $b) {
            $row = [
                $b['nama_bulan'],
                $b['pendaftar'],
                $b['diterima'],
                $b['selesai'],
                $b['rasio'] . '%',
            ];
            if ($canViewNilai) {
                $row[] = $b['rata_nilai'] !== null ? number_format((float) $b['rata_nilai'], 2) : '-';
            }
            $rows[] = $row;
        }

        // Baris ringkasan total
        $summaryRow = [
            "TOTAL TAHUN {$tahun}",
            $data['totalPendaftaran'],
            $data['totalDiterima'],
            $data['totalSelesai'],
            number_format($data['acceptanceRate'], 1) . '%',
        ];
        if ($canViewNilai) {
            $summaryRow[] = $data['rataRataNilai'] !== null ? number_format((float) $data['rataRataNilai'], 2) : '-';
        }
        $rows[] = $summaryRow;

        $auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'analitik_tahunan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'ekspor_rekap_tahunan_csv',
                'tahun' => $tahun,
                'baris' => count($rows),
            ]
        );

        return $exportService->downloadCsvStream(
            filename: "rekap-tahunan-magkom-{$tahun}.csv",
            headers: $headers,
            rows: $rows
        );
    }

    /**
     * Hitung agregasi metrik tahunan.
     */
    protected function hitungMetrikTahunan(int $tahun, $user): array
    {
        $canViewNilai = $user->role === RoleEnum::SuperAdmin;

        // 1. Pendaftaran di tahun tersebut
        $pendaftaranQuery = Pendaftaran::whereYear('created_at', $tahun);
        $totalPendaftaran = (clone $pendaftaranQuery)->count();
        $totalDiterima = (clone $pendaftaranQuery)->where('status', StatusPendaftaranEnum::Diterima)->count();

        // 2. Penempatan di tahun tersebut
        $penempatanQuery = Penempatan::whereYear('tgl_mulai', $tahun);
        $totalPenempatan = (clone $penempatanQuery)->count();
        $totalSelesai = (clone $penempatanQuery)->where('status', StatusPenempatanEnum::Selesai)->count();

        $acceptanceRate = $totalPendaftaran > 0 ? round(($totalDiterima / $totalPendaftaran) * 100, 1) : 0.0;
        $completionRate = $totalDiterima > 0 ? round(($totalSelesai / $totalDiterima) * 100, 1) : 0.0;

        // Rata-rata durasi magang (bulan)
        $avgDurasi = (clone $pendaftaranQuery)->where('status', StatusPendaftaranEnum::Diterima)->avg('durasi_bulan');
        $rataRataDurasi = $avgDurasi ? round((float) $avgDurasi, 1) : 3.0;

        // 3. Tren Bulanan (12 Bulan)
        $trenBulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthPad = str_pad((string) $m, 2, '0', STR_PAD_LEFT);
            $namaBulan = Carbon::create($tahun, $m, 1)->translatedFormat('F');

            $pendaftarBulan = Pendaftaran::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $m)
                ->count();

            $diterimaBulan = Penempatan::whereYear('tgl_mulai', $tahun)
                ->whereMonth('tgl_mulai', $m)
                ->count();

            $selesaiBulan = Penempatan::whereYear('tgl_selesai', $tahun)
                ->whereMonth('tgl_selesai', $m)
                ->where('status', StatusPenempatanEnum::Selesai)
                ->count();

            $rasioBulan = $pendaftarBulan > 0 ? round(($diterimaBulan / $pendaftarBulan) * 100, 1) : 0.0;

            $rataNilaiBulan = null;
            if ($canViewNilai) {
                $rataNilaiBulan = Penilaian::whereHas('penempatan', function ($q) use ($tahun, $m) {
                    $q->whereYear('tgl_selesai', $tahun)->whereMonth('tgl_selesai', $m);
                })->where('status', StatusPenilaianEnum::Final)->avg('nilai_akhir');
            }

            $trenBulanan[] = [
                'bulan' => $m,
                'nama_bulan' => $namaBulan,
                'pendaftar' => $pendaftarBulan,
                'diterima' => $diterimaBulan,
                'selesai' => $selesaiBulan,
                'rasio' => $rasioBulan,
                'rata_nilai' => $rataNilaiBulan ? round((float) $rataNilaiBulan, 1) : null,
            ];
        }

        // 4. Top 10 Institusi Pengirim
        $topInstitusi = DB::table('peserta_profil')
            ->join('pendaftaran', 'peserta_profil.user_id', '=', 'pendaftaran.user_id')
            ->whereYear('pendaftaran.created_at', $tahun)
            ->select(
                'peserta_profil.institusi',
                DB::raw('count(pendaftaran.id) as total_pelamar'),
                DB::raw("sum(case when pendaftaran.status = 'diterima' then 1 else 0 end) as total_diterima")
            )
            ->groupBy('peserta_profil.institusi')
            ->orderByDesc('total_pelamar')
            ->limit(10)
            ->get();

        // 5. Distribusi Jenjang
        $jenjangDistribusi = DB::table('peserta_profil')
            ->join('pendaftaran', 'peserta_profil.user_id', '=', 'pendaftaran.user_id')
            ->whereYear('pendaftaran.created_at', $tahun)
            ->select('peserta_profil.jenjang', DB::raw('count(pendaftaran.id) as total'))
            ->groupBy('peserta_profil.jenjang')
            ->pluck('total', 'jenjang')
            ->toArray();

        // 6. Nilai & Predikat (Hanya jika SuperAdmin - Penegakan A-20)
        $rataRataNilai = null;
        $sebaranPredikat = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

        if ($canViewNilai) {
            $penilaianQuery = Penilaian::whereHas('penempatan', function ($q) use ($tahun) {
                $q->whereYear('tgl_selesai', $tahun);
            })->where('status', StatusPenilaianEnum::Final);

            $avgScore = (clone $penilaianQuery)->avg('nilai_akhir');
            $rataRataNilai = $avgScore ? round((float) $avgScore, 2) : null;

            $predikatCounts = (clone $penilaianQuery)
                ->select('predikat', DB::raw('count(*) as count'))
                ->groupBy('predikat')
                ->pluck('count', 'predikat')
                ->toArray();

            foreach ($predikatCounts as $p => $c) {
                if (isset($sebaranPredikat[$p])) {
                    $sebaranPredikat[$p] = $c;
                }
            }
        }

        return [
            'totalPendaftaran' => $totalPendaftaran,
            'totalDiterima' => $totalDiterima,
            'totalSelesai' => $totalSelesai,
            'totalPenempatan' => $totalPenempatan,
            'acceptanceRate' => $acceptanceRate,
            'completionRate' => $completionRate,
            'rataRataDurasi' => $rataRataDurasi,
            'trenBulanan' => $trenBulanan,
            'topInstitusi' => $topInstitusi,
            'jenjangDistribusi' => $jenjangDistribusi,
            'rataRataNilai' => $rataRataNilai,
            'sebaranPredikat' => $sebaranPredikat,
        ];
    }

    /**
     * Daftar tahun yang tersedia.
     */
    protected function getDaftarTahun(): array
    {
        $currentYear = (int) date('Y');
        $years = BulanMagang::selectRaw('substr(bulan_mulai, 1, 4) as tahun')
            ->distinct()
            ->pluck('tahun')
            ->map(fn ($y) => (int) $y)
            ->toArray();

        $years[] = $currentYear;
        $years[] = $currentYear - 1;
        $years = array_unique($years);
        rsort($years);

        return $years;
    }
}
