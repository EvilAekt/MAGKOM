<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
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

        // 2. Bulan Magang Aktif & Rekap Keterisian Kuota
        $bulanAktif = BulanMagang::where('aktif', true)
            ->whereDate('bulan_mulai', '>=', now()->startOfMonth())
            ->orderBy('bulan_mulai', 'asc')
            ->first() ?? BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'desc')->first();

        $kuotaList = collect();
        if ($bulanAktif) {
            $kuotaList = Kuota::with('jurusan')
                ->where('bulan_magang_id', $bulanAktif->id)
                ->where('aktif', true)
                ->get();
        }

        // 3. Lima Pengajuan Terkini yang Perlu Tindak Lanjut Verifikator
        $pendaftaranTerbaru = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan'])
            ->whereIn('status', [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::PerluPerbaikan])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'pengajuanMenunggu' => $pengajuanMenunggu,
            'perluPerbaikan' => $perluPerbaikan,
            'waitingListCount' => $waitingListCount,
            'pesertaAktifCount' => $pesertaAktifCount,
            'bulanAktif' => $bulanAktif,
            'kuotaList' => $kuotaList,
            'pendaftaranTerbaru' => $pendaftaranTerbaru,
        ]);
    }
}
