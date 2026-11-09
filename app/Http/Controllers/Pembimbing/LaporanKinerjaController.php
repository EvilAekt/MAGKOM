<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->whereHas('peserta', function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhereHas('profil', function ($p) use ($keyword) {
                        $p->where('institusi', 'like', "%{$keyword}%")
                            ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('penempatan_id')) {
            $query->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'tidak_aktif') {
                $query->whereIn('status', [StatusPenempatanEnum::Selesai, StatusPenempatanEnum::Nonaktif]);
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', (string) $request->input('bulan'));
            if (count($parts) === 2) {
                [$tahun, $bln] = $parts;
                $query->whereYear('tgl_mulai', (int) $tahun)
                    ->whereMonth('tgl_mulai', (int) $bln);
            }
        } elseif ($request->filled('tahun')) {
            $tahun = (int) $request->input('tahun');
            $query->whereYear('tgl_mulai', $tahun);
        }

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

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: ['aksi' => 'lihat_laporan_kinerja_pembimbing', 'total_mentee' => $totalMentee]
        );

        $penempatanOptions = Penempatan::with(['peserta.profil'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->latest('id')
            ->get();

        return view('pembimbing.laporan-kinerja.index', [
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
            'daftarBulan' => $this->getDaftarBulan(),
        ]);
    }

    /**
     * Ekspor rekapitulasi laporan peserta bimbingan (Excel .xlsx, CSV .csv, atau redirect PDF).
     */
    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user();

        $query = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'tugas',
            'logbooks',
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->whereHas('peserta', function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhereHas('profil', function ($p) use ($keyword) {
                        $p->where('institusi', 'like', "%{$keyword}%")
                            ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('penempatan_id')) {
            $query->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'tidak_aktif') {
                $query->whereIn('status', [StatusPenempatanEnum::Selesai, StatusPenempatanEnum::Nonaktif]);
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', (string) $request->input('bulan'));
            if (count($parts) === 2) {
                [$tahun, $bln] = $parts;
                $query->whereYear('tgl_mulai', (int) $tahun)
                    ->whereMonth('tgl_mulai', (int) $bln);
            }
        } elseif ($request->filled('tahun')) {
            $tahun = (int) $request->input('tahun');
            $query->whereYear('tgl_mulai', $tahun);
        }

        $format = strtolower((string) $request->input('format', 'xlsx'));

        if ($format === 'pdf') {
            return redirect()->route('pembimbing.laporan-kinerja.cetak', $request->all());
        }

        $records = $query->latest('tgl_mulai')->get();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => match ($format) {
                    'xlsx' => 'Ekspor rekapitulasi laporan peserta XLSX',
                    default => 'Ekspor rekapitulasi laporan peserta CSV',
                },
                'format' => $format,
                'jumlah_baris' => $records->count(),
            ]
        );

        $filename = 'Laporan_Peserta_Bimbingan_'.Carbon::now()->format('Ymd_His');

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($records) {
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['No', 'Nama Mentee', 'NIM/NISN', 'Institusi', 'Jurusan', 'Bidang Magang', 'Tugas Selesai', 'Total Tugas', 'Logbook Direview', 'Total Logbook', 'Status']);

                $no = 1;
                foreach ($records as $p) {
                    $totalTugas = $p->tugas->count();
                    $tugasSelesai = $p->tugas->where('status', StatusTugasEnum::Selesai)->count();
                    $totalLogbook = $p->logbooks->count();
                    $logbookReviewed = $p->logbooks->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])->count();
                    $statusVal = is_object($p->status) ? $p->status->value : $p->status;

                    fputcsv($handle, [
                        $no++,
                        $p->peserta?->nama ?? '-',
                        $p->peserta?->profil?->nim_nisn ?? '-',
                        $p->peserta?->profil?->institusi ?? '-',
                        $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-',
                        $p->bidang?->nama_bidang ?? 'Umum',
                        $tugasSelesai,
                        $totalTugas,
                        $logbookReviewed,
                        $totalLogbook,
                        ucfirst((string) $statusVal),
                    ]);
                }
                fclose($handle);
            }, $filename.'.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ]);
        }

        // Default Excel XML Spreadsheet (.xlsx)
        return response()->streamDownload(function () use ($records) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
            echo "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
            echo " xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n";
            echo " xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
            echo " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
            echo " xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
            echo " <Styles>\n";
            echo "  <Style ss:ID=\"Default\" ss:Name=\"Normal\">\n";
            echo "   <Alignment ss:Vertical=\"Center\"/>\n";
            echo "   <Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Color=\"#000000\"/>\n";
            echo "  </Style>\n";
            echo "  <Style ss:ID=\"Header\">\n";
            echo "   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>\n";
            echo "   <Borders>\n";
            echo "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>\n";
            echo "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>\n";
            echo "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>\n";
            echo "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>\n";
            echo "   </Borders>\n";
            echo "   <Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Color=\"#FFFFFF\" ss:Bold=\"1\"/>\n";
            echo "   <Interior ss:Color=\"#0641CC\" ss:Pattern=\"Solid\"/>\n";
            echo "  </Style>\n";
            echo "  <Style ss:ID=\"Cell\">\n";
            echo "   <Borders>\n";
            echo "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "   </Borders>\n";
            echo "  </Style>\n";
            echo "  <Style ss:ID=\"CellCenter\">\n";
            echo "   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>\n";
            echo "   <Borders>\n";
            echo "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#E2E8F0\"/>\n";
            echo "   </Borders>\n";
            echo "  </Style>\n";
            echo " </Styles>\n";
            echo " <Worksheet ss:Name=\"Laporan Peserta\">\n";
            echo "  <Table>\n";
            echo "   <Column ss:Width=\"40\"/>\n";
            echo "   <Column ss:Width=\"180\"/>\n";
            echo "   <Column ss:Width=\"120\"/>\n";
            echo "   <Column ss:Width=\"180\"/>\n";
            echo "   <Column ss:Width=\"150\"/>\n";
            echo "   <Column ss:Width=\"180\"/>\n";
            echo "   <Column ss:Width=\"100\"/>\n";
            echo "   <Column ss:Width=\"100\"/>\n";
            echo "   <Column ss:Width=\"90\"/>\n";
            echo "   <Row ss:Height=\"24\">\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">No</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Nama Mentee</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">NIM/NISN</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Institusi</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Jurusan</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Bidang Magang</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Tugas Selesai</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Logbook</Data></Cell>\n";
            echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">Status</Data></Cell>\n";
            echo "   </Row>\n";

            $no = 1;
            foreach ($records as $p) {
                $totalTugas = $p->tugas->count();
                $tugasSelesai = $p->tugas->where('status', StatusTugasEnum::Selesai)->count();
                $totalLogbook = $p->logbooks->count();
                $logbookReviewed = $p->logbooks->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])->count();
                $statusVal = is_object($p->status) ? $p->status->value : $p->status;

                $cleanNama = htmlspecialchars($p->peserta?->nama ?? '-', ENT_XML1);
                $nim = htmlspecialchars($p->peserta?->profil?->nim_nisn ?? '-', ENT_XML1);
                $inst = htmlspecialchars($p->peserta?->profil?->institusi ?? '-', ENT_XML1);
                $jur = htmlspecialchars($p->peserta?->profil?->jurusan?->nama_jurusan ?? '-', ENT_XML1);
                $bid = htmlspecialchars($p->bidang?->nama_bidang ?? 'Umum', ENT_XML1);
                $statusLabel = htmlspecialchars(ucfirst((string) $statusVal), ENT_XML1);

                echo "   <Row ss:Height=\"20\">\n";
                echo "    <Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"Number\">{$no}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"Cell\"><Data ss:Type=\"String\">{$cleanNama}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"Cell\"><Data ss:Type=\"String\">{$nim}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"Cell\"><Data ss:Type=\"String\">{$inst}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"Cell\"><Data ss:Type=\"String\">{$jur}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"Cell\"><Data ss:Type=\"String\">{$bid}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"String\">{$tugasSelesai} / {$totalTugas}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"String\">{$logbookReviewed} / {$totalLogbook}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"String\">{$statusLabel}</Data></Cell>\n";
                echo "   </Row>\n";
                $no++;
            }

            echo "  </Table>\n";
            echo " </Worksheet>\n";
            echo "</Workbook>\n";
        }, $filename.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
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
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->whereHas('peserta', function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhereHas('profil', function ($p) use ($keyword) {
                        $p->where('institusi', 'like', "%{$keyword}%")
                            ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('penempatan_id')) {
            $query->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'tidak_aktif') {
                $query->whereIn('status', [StatusPenempatanEnum::Selesai, StatusPenempatanEnum::Nonaktif]);
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', (string) $request->input('bulan'));
            if (count($parts) === 2) {
                [$tahun, $bln] = $parts;
                $query->whereYear('tgl_mulai', (int) $tahun)
                    ->whereMonth('tgl_mulai', (int) $bln);
            }
        } elseif ($request->filled('tahun')) {
            $query->whereYear('tgl_mulai', (int) $request->input('tahun'));
        }

        $allPenempatans = $query->latest('tgl_mulai')->get();

        return view('pembimbing.laporan-kinerja.cetak', [
            'allPenempatans' => $allPenempatans,
            'pembimbingUser' => $user,
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * Dapatkan daftar bulan magang untuk filter pencarian.
     *
     * @return array<string, string>
     */
    private function getDaftarBulan(): array
    {
        $daftar = [];
        $bulanMagangs = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        foreach ($bulanMagangs as $bm) {
            if ($bm->bulan_mulai) {
                $key = Carbon::parse($bm->bulan_mulai)->setTimezone(config('app.timezone', 'Asia/Jakarta'))->format('Y-m');
                $daftar[$key] = $bm->nama_bulan;
            }
        }
        for ($i = 0; $i < 12; $i++) {
            $dt = Carbon::now()->subMonths($i);
            $key = $dt->format('Y-m');
            if (! isset($daftar[$key])) {
                $daftar[$key] = $dt->locale('id')->translatedFormat('F Y');
            }
        }
        krsort($daftar);

        return $daftar;
    }
}
