<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard operasional administrasi MagKom (PRD 9.2).
     */
    public function index(Request $request): View
    {
        // 1. Metrik Operasional Pendaftaran & Penempatan
        $pengajuanMenunggu = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count();
        $perluPerbaikan = Pendaftaran::where('status', StatusPendaftaranEnum::PerluPerbaikan)->count();
        $waitingListCount = Pendaftaran::where('status', StatusPendaftaranEnum::WaitingList)->count();
        $pesertaAktifCount = Penempatan::where('status', StatusPenempatanEnum::Aktif)->count();

        // Indikator SLA Kritis (Pengajuan 'Diajukan' yang berumur lebih dari 3 hari kalender sejak diajukan)
        $batasSlaKritis = now()->subDays(3);
        $pengajuanSlaKritisCount = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
            ->where(function ($q) use ($batasSlaKritis) {
                $q->where('diajukan_at', '<=', $batasSlaKritis)
                  ->orWhere(function ($q2) use ($batasSlaKritis) {
                      $q2->whereNull('diajukan_at')
                         ->where('created_at', '<=', $batasSlaKritis);
                  });
            })
            ->count();

        // 2. Daftar Periode Bulan & Seleksi Bulan Aktif / Terpilih
        $daftarBulan = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        $bulanId = $request->query('bulan_id');

        if ($bulanId) {
            $bulanAktif = BulanMagang::find($bulanId);
        } else {
            $bulanAktif = BulanMagang::where('aktif', true)
                ->whereDate('bulan_mulai', '>=', now()->startOfMonth())
                ->orderBy('bulan_mulai', 'asc')
                ->first() ?? BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'desc')->first();
        }

        // 3. Rekap Keterisian Kuota Umum & Slot Khusus Periode Terpilih
        $kuotaList = collect();
        $totalKuotaUmum = 0;
        $totalKuotaTerisi = 0;
        $slotKhususTerisi = 0;

        if ($bulanAktif) {
            $kuotaList = Kuota::with('jurusan')
                ->where('bulan_magang_id', $bulanAktif->id)
                ->where('aktif', true)
                ->get();

            $totalKuotaUmum = (int) $kuotaList->sum('jumlah');
            $totalKuotaTerisi = (int) $kuotaList->sum('terisi');

            $slotKhusus = SlotKhusus::whereDate('bulan_mulai', $bulanAktif->bulan_mulai)->first();
            $slotKhususTerisi = $slotKhusus ? (int) $slotKhusus->terisi : 0;
        }

        // 4. Action Hub: Pendaftaran Terkini yang Membutuhkan Tindakan (Urutkan dari yang tertua/paling mendesak)
        $pendaftaranTerbaru = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan'])
            ->whereIn('status', [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::PerluPerbaikan])
            ->orderBy('created_at', 'asc')
            ->take(8)
            ->get();

        // 5. Monitoring Beban Pembimbing Lapangan (Workload Sesuai BR-13)
        $pembimbingWorkload = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->with('bidang')
            ->withCount(['penempatanBimbingan as aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->get();

        // 6. Arsip Penempatan Terbaru
        $penempatanTerbaru = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang'])
            ->where('status', StatusPenempatanEnum::Aktif)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'pengajuanMenunggu' => $pengajuanMenunggu,
            'perluPerbaikan' => $perluPerbaikan,
            'waitingListCount' => $waitingListCount,
            'pesertaAktifCount' => $pesertaAktifCount,
            'pengajuanSlaKritisCount' => $pengajuanSlaKritisCount,
            'daftarBulan' => $daftarBulan,
            'bulanAktif' => $bulanAktif,
            'kuotaList' => $kuotaList,
            'totalKuotaUmum' => $totalKuotaUmum,
            'totalKuotaTerisi' => $totalKuotaTerisi,
            'slotKhususTerisi' => $slotKhususTerisi,
            'pendaftaranTerbaru' => $pendaftaranTerbaru,
            'pembimbingWorkload' => $pembimbingWorkload,
            'penempatanTerbaru' => $penempatanTerbaru,
        ]);
    }
}
