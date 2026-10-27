<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\StatusKetersediaanEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Services\BulanStatusService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __construct(
        protected BulanStatusService $bulanStatusService
    ) {}

    /**
     * Menampilkan landing page publik informasi kuota magang (F-01).
     */
    public function __invoke(Request $request): View
    {
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

        return view('public.landing', [
            'bulanTampil' => $bulanTampil,
            'jurusanSmk' => $jurusanSmk,
            'jurusanMahasiswa' => $jurusanMahasiswa,
            'matriksKetersediaan' => $matriksKetersediaan,
            'semuaBidang' => Bidang::with(['jurusan' => function ($q) {
                $q->where('aktif', true);
            }])->where('aktif', true)->get(),
            'sekarang' => $sekarang,
        ]);
    }
}
