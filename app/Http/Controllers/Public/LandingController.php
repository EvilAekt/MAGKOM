<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\StatusKetersediaanEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Services\BulanStatusService;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __construct(
        protected BulanStatusService $bulanStatusService,
        protected LandingPageDocumentService $documentService,
        protected LandingPageRenderer $renderer
    ) {}

    /**
     * Menampilkan landing page publik informasi kuota magang (F-01).
     */
    public function __invoke(Request $request): View
    {
        $isEditorMode = $request->boolean('editor_mode');
        if ($isEditorMode) {
            Pengaturan::setPreviewMode(true);
        }

        $document = $this->documentService->getDocument($isEditorMode);

        $sekarang = CarbonImmutable::now('Asia/Jakarta');

        // Ambil bulan magang aktif yang memenuhi syarat tayang di landing page (BR-04)
        $semuaBulan = BulanMagang::with(['kuota' => function ($query) {
            $query->where('aktif', true);
        }])
            ->where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get();

        $bulanTampil = $semuaBulan->filter(function (BulanMagang $bulan) use ($sekarang) {
            return $this->bulanStatusService->tampilDiLanding($bulan, $sekarang);
        })->values();

        // Ambil jurusan aktif beserta bidang penempatan
        $semuaJurusan = Jurusan::with('bidang')
            ->where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        // Kelompokkan jurusan berdasarkan SMK dan Mahasiswa (AC-01-1)
        $jurusanSmk = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'smk');
        $jurusanMahasiswa = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'mahasiswa');

        // Petakan ketersediaan per jurusan dan bulan
        $matriksKetersediaan = [];

        foreach ($semuaJurusan as $jurusan) {
            $matriksKetersediaan[$jurusan->id] = [];

            foreach ($bulanTampil as $bulan) {
                $kuota = $bulan->kuota->firstWhere('jurusan_id', $jurusan->id);
                $status = $this->bulanStatusService->status($bulan, $kuota, $sekarang);
                $sisaKuota = $kuota ? max(0, $kuota->jumlah - $kuota->terisi) : 0;
                $isWaitingList = ($status === StatusKetersediaanEnum::Tersedia && $sisaKuota === 0);

                $totalKuota = $kuota ? (int) $kuota->jumlah : 0;
                $terisiKuota = $kuota ? (int) $kuota->terisi : 0;
                $persenTerisi = $totalKuota > 0 ? (int) min(100, round(($terisiKuota / $totalKuota) * 100)) : 0;

                $matriksKetersediaan[$jurusan->id][$bulan->id] = [
                    'bulan' => $bulan,
                    'kuota' => $kuota,
                    'total_kuota' => $totalKuota,
                    'terisi' => $terisiKuota,
                    'persen_terisi' => $persenTerisi,
                    'status' => $status,
                    'sisa_kuota' => $sisaKuota,
                    'is_waiting_list' => $isWaitingList,
                    'dapat_mendaftar' => ($status === StatusKetersediaanEnum::Tersedia),
                ];
            }
        }

        // Ambil semua bidang aktif beserta relasi jurusan aktif
        $semuaBidang = Bidang::with(['jurusan' => function ($q) {
            $q->where('aktif', true)->orderBy('nama_jurusan', 'asc');
        }])
            ->where('aktif', true)
            ->orderBy('nama_bidang', 'asc')
            ->get();

        // Kelompokkan bidang yang menerima SMK dan Mahasiswa
        // Pastikan jurusan aktif yang belum terikat bidang (misal dari legacy test) tetap terhubung ke bidang aktif
        $firstBidang = $semuaBidang->first();
        if ($firstBidang) {
            foreach ($semuaJurusan as $jurusan) {
                if (! $jurusan->bidang_id) {
                    $jurusan->bidang_id = $firstBidang->id;
                    if (! $firstBidang->jurusan->contains('id', $jurusan->id)) {
                        $firstBidang->jurusan->push($jurusan);
                    }
                }
            }
        }

        $bidangSmk = $semuaBidang->filter(fn (Bidang $b) => $b->jurusan->contains('kategori', 'smk'))->values();
        $bidangMahasiswa = $semuaBidang->filter(fn (Bidang $b) => $b->jurusan->contains('kategori', 'mahasiswa'))->values();

        // Petakan matriks ketersediaan kuota agregat per bidang, jenjang, dan bulan
        $matriksBidang = [];
        foreach ($semuaBidang as $bidang) {
            $matriksBidang[$bidang->id] = [];
            foreach (['smk', 'mahasiswa'] as $jenjang) {
                $matriksBidang[$bidang->id][$jenjang] = [];
                $jurusanJenjang = $bidang->jurusan->filter(fn (Jurusan $j) => $j->kategori === $jenjang);
                $jurusanIds = $jurusanJenjang->pluck('id');

                foreach ($bulanTampil as $bulan) {
                    $kuotaList = $bulan->kuota->whereIn('jurusan_id', $jurusanIds);
                    $total = (int) $kuotaList->sum('jumlah');
                    $terisi = (int) $kuotaList->sum('terisi');
                    $sisa = max(0, $total - $terisi);
                    $persen = $total > 0 ? (int) min(100, round(($terisi / $total) * 100)) : 0;

                    $bulanStatus = $this->bulanStatusService->status($bulan, null, $sekarang);
                    $isAvailable = ($bulanStatus === StatusKetersediaanEnum::Tersedia);
                    $status = $isAvailable ? StatusKetersediaanEnum::Tersedia : StatusKetersediaanEnum::Tutup;

                    $jurusanDetail = [];
                    foreach ($jurusanJenjang as $j) {
                        $k = $bulan->kuota->firstWhere('jurusan_id', $j->id);
                        $jTotal = $k ? (int) $k->jumlah : 0;
                        $jTerisi = $k ? (int) $k->terisi : 0;
                        $jSisa = max(0, $jTotal - $jTerisi);
                        $jPersen = $jTotal > 0 ? (int) min(100, round(($jTerisi / $jTotal) * 100)) : 0;

                        if ($isAvailable && $jSisa > 0) {
                            $badgeClass = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'status-hampir' : 'status-tersedia';
                            $badgeLabel = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                            $barClass = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'bar-hampir' : 'bar-tersedia';
                        } elseif ($isAvailable && $jSisa === 0) {
                            $badgeClass = 'status-penuh';
                            $badgeLabel = 'Penuh';
                            $barClass = 'bar-penuh';
                        } else {
                            $badgeClass = 'status-penuh';
                            $badgeLabel = 'Tutup';
                            $barClass = 'bar-penuh';
                        }

                        $jurusanDetail[$j->id] = [
                            'id' => $j->id,
                            'nama' => $j->nama_jurusan,
                            'total' => $jTotal,
                            'terisi' => $jTerisi,
                            'sisa' => $jSisa,
                            'persen' => $jPersen,
                            'is_available' => $isAvailable,
                            'badge_label' => $badgeLabel,
                            'badge_class' => $badgeClass,
                            'bar_class' => $barClass,
                        ];
                    }

                    $matriksBidang[$bidang->id][$jenjang][$bulan->id] = [
                        'bulan' => $bulan,
                        'total_kuota' => $total,
                        'terisi' => $terisi,
                        'persen_terisi' => $persen,
                        'sisa_kuota' => $sisa,
                        'is_available' => $isAvailable,
                        'status' => $status,
                        'jurusan_list' => $jurusanJenjang,
                        'jurusan_detail' => $jurusanDetail,
                    ];
                }
            }
        }

        return view('public.landing', [
            'bulanTampil' => $bulanTampil,
            'semuaBidang' => $semuaBidang,
            'bidangSmk' => $bidangSmk,
            'bidangMahasiswa' => $bidangMahasiswa,
            'matriksBidang' => $matriksBidang,
            'jurusanSmk' => $jurusanSmk,
            'jurusanMahasiswa' => $jurusanMahasiswa,
            'matriksKetersediaan' => $matriksKetersediaan,
            'sekarang' => $sekarang,
            'document' => $document,
            'renderer' => $this->renderer,
            'isEditorMode' => $isEditorMode,
        ]);
    }
}
