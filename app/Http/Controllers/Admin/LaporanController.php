<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\JenjangEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Services\ExportReportService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(
        protected ExportReportService $exportService
    ) {}

    /**
     * Menampilkan Hub Laporan & Rekapitulasi Data (F-17, AC-17-1).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tab = $request->input('tab', 'pendaftaran');

        // Jika user bukan superadmin mencoba membuka tab nilai, batasi ke tab pendaftaran
        if ($tab === 'nilai' && ! $user->isSuperAdmin()) {
            $tab = 'pendaftaran';
        }

        // Ringkasan Statistik Eksekutif (Cards Metrik)
        $metricCards = [
            'total_pendaftaran' => Pendaftaran::count(),
            'total_diterima'    => Pendaftaran::where('status', StatusPendaftaranEnum::Diterima)->count(),
            'penempatan_aktif'  => Penempatan::where('status', StatusPenempatanEnum::Aktif)->count(),
            'penempatan_selesai'=> Penempatan::where('status', StatusPenempatanEnum::Selesai)->count(),
            'kuota_terisi'      => (int) Kuota::sum('terisi'),
            'slot_terisi'       => (int) SlotKhusus::sum('terisi'),
        ];

        $bulanMagangList = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        $jurusanList = \App\Models\Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        // Visual Analytics Keterisian Kuota per Jurusan (AC-17-1)
        $analyticsKuotaJurusan = Kuota::with('jurusan')
            ->selectRaw('jurusan_id, SUM(jumlah) as total_kuota, SUM(terisi) as total_terisi')
            ->groupBy('jurusan_id')
            ->get()
            ->map(function ($k) {
                $kuota = (int) $k->total_kuota;
                $terisi = (int) $k->total_terisi;
                $persen = $kuota > 0 ? min(100, (int) round(($terisi / $kuota) * 100)) : 0;
                return [
                    'nama_jurusan' => $k->jurusan?->nama_jurusan ?? 'Jurusan Lain',
                    'kuota' => $kuota,
                    'terisi' => $terisi,
                    'sisa' => max(0, $kuota - $terisi),
                    'persen' => $persen,
                ];
            })
            ->sortByDesc('persen')
            ->values();

        // Visual Analytics Proporsi Jenjang Pendidikan Calon Peserta
        $smkCount = PesertaProfil::where('jenjang', JenjangEnum::Smk)->count();
        $d3Count = PesertaProfil::where('jenjang', JenjangEnum::D3)->count();
        $s1Count = PesertaProfil::where('jenjang', JenjangEnum::S1)->count();
        $totalJenjang = $smkCount + $d3Count + $s1Count;
        $analyticsJenjang = [
            'smk' => [
                'label' => 'SMK / Sederajat',
                'count' => $smkCount,
                'persen' => $totalJenjang > 0 ? (int) round(($smkCount / $totalJenjang) * 100) : 0,
            ],
            'd3' => [
                'label' => 'Diploma (D3)',
                'count' => $d3Count,
                'persen' => $totalJenjang > 0 ? (int) round(($d3Count / $totalJenjang) * 100) : 0,
            ],
            's1' => [
                'label' => 'Sarjana (S1)',
                'count' => $s1Count,
                'persen' => $totalJenjang > 0 ? (int) round(($s1Count / $totalJenjang) * 100) : 0,
            ],
            'total' => $totalJenjang,
        ];

        // Data berdasarkan Tab
        $pendaftaranData = null;
        $penempatanData = null;
        $kuotaData = null;
        $slotKhususData = null;
        $nilaiData = null;

        if ($tab === 'pendaftaran') {
            $pQuery = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan'])
                ->latest('created_at');

            if ($request->filled('bulan_magang_id')) {
                $pQuery->where('bulan_magang_id', $request->input('bulan_magang_id'));
            }
            if ($request->filled('status')) {
                $pQuery->where('status', $request->input('status'));
            }
            if ($request->filled('jurusan_id')) {
                $jurusanId = $request->integer('jurusan_id');
                $pQuery->where(function ($q) use ($jurusanId) {
                    $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusanId))
                      ->orWhereHas('user.profil', fn ($qp) => $qp->where('jurusan_id', $jurusanId));
                });
            }
            if ($request->filled('dari_tanggal')) {
                $pQuery->where('created_at', '>=', $request->input('dari_tanggal') . ' 00:00:00');
            }
            if ($request->filled('sampai_tanggal')) {
                $pQuery->where('created_at', '<=', $request->input('sampai_tanggal') . ' 23:59:59');
            }

            $pendaftaranData = $pQuery->paginate(10)->withQueryString();
        } elseif ($tab === 'penempatan') {
            $penQuery = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus'])
                ->orderBy('tgl_mulai', 'desc');

            if ($request->filled('status')) {
                $penQuery->where('status', $request->input('status'));
            }
            if ($request->filled('jalur')) {
                $penQuery->where('jalur', $request->input('jalur'));
            }
            if ($request->filled('jurusan_id')) {
                $jurusanId = $request->integer('jurusan_id');
                $penQuery->where(function ($q) use ($jurusanId) {
                    $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusanId))
                      ->orWhereHas('peserta.profil', fn ($qp) => $qp->where('jurusan_id', $jurusanId));
                });
            }
            if ($request->filled('dari_tanggal')) {
                $penQuery->where('tgl_mulai', '>=', $request->input('dari_tanggal'));
            }
            if ($request->filled('sampai_tanggal')) {
                $penQuery->where('tgl_mulai', '<=', $request->input('sampai_tanggal'));
            }

            $penempatanData = $penQuery->paginate(10)->withQueryString();
        } elseif ($tab === 'kuota') {
            $kuotaData = Kuota::with(['bulanMagang', 'jurusan'])
                ->join('bulan_magang', 'kuota.bulan_magang_id', '=', 'bulan_magang.id')
                ->orderBy('bulan_magang.bulan_mulai', 'desc')
                ->select('kuota.*')
                ->paginate(15, ['*'], 'kuota_page')
                ->withQueryString();

            $slotKhususData = SlotKhusus::orderBy('bulan_mulai', 'desc')
                ->paginate(10, ['*'], 'slot_page')
                ->withQueryString();
        } elseif ($tab === 'nilai' && $user->isSuperAdmin()) {
            $nilaiData = Penilaian::with(['penempatan.peserta.profil', 'pembimbing'])
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        return view('admin.laporan.index', [
            'tab'               => $tab,
            'metricCards'       => $metricCards,
            'bulanMagangList'   => $bulanMagangList,
            'jurusanList'       => $jurusanList,
            'statusPendaftaran' => StatusPendaftaranEnum::cases(),
            'statusPenempatan'  => StatusPenempatanEnum::cases(),
            'pendaftaranData'   => $pendaftaranData,
            'penempatanData'    => $penempatanData,
            'kuotaData'         => $kuotaData,
            'slotKhususData'    => $slotKhususData,
            'nilaiData'         => $nilaiData,
            'analyticsKuotaJurusan' => $analyticsKuotaJurusan,
            'analyticsJenjang'  => $analyticsJenjang,
            'isSuperAdmin'      => $user->isSuperAdmin(),
        ]);
    }

    /**
     * Ekspor rekapitulasi data pendaftaran peserta (AC-17-1, F-17).
     */
    public function pendaftaran(Request $request): StreamedResponse
    {
        return $this->exportService->exportPendaftaran(
            $request->user(),
            $request->only(['bulan_magang_id', 'status', 'jurusan_id', 'dari_tanggal', 'sampai_tanggal'])
        );
    }

    /**
     * Ekspor rekapitulasi data penempatan peserta (AC-17-1, F-17).
     */
    public function penempatan(Request $request): StreamedResponse
    {
        return $this->exportService->exportPenempatan(
            $request->user(),
            $request->only(['status', 'jalur', 'jurusan_id', 'dari_tanggal', 'sampai_tanggal'])
        );
    }

    /**
     * Ekspor realisasi kuota umum vs slot khusus (AC-17-1, F-17).
     */
    public function kuota(Request $request): StreamedResponse
    {
        return $this->exportService->exportRealisasiKuota($request->user());
    }

    /**
     * Ekspor rekapitulasi nilai peserta (Hanya SuperAdmin, AC-17-1, A-20).
     */
    public function nilai(Request $request): StreamedResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            throw new AuthorizationException('Rekapitulasi nilai hanya dapat diakses oleh SuperAdmin (PRD 3.3, A-20).');
        }

        return $this->exportService->exportNilai($request->user());
    }
}
