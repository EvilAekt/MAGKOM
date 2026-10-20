<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminDashboardMetricsService
{
    /**
     * Membangun seluruh data metrik operasional untuk Dashboard Admin (PRD 9.2).
     *
     * @return array<string, mixed>
     */
    public function buildDashboardData(?string $bulanId = null): array
    {
        // 1. Metrik Operasional Pendaftaran & Penempatan
        $pengajuanMenunggu = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count();
        $perluPerbaikan = Pendaftaran::where('status', StatusPendaftaranEnum::PerluPerbaikan)->count();
        $waitingListCount = Pendaftaran::where('status', StatusPendaftaranEnum::WaitingList)->count();
        $pesertaAktifCount = Penempatan::where('status', StatusPenempatanEnum::Aktif)->count();

        // Indikator SLA Kritis (> 3 hari kalender belum diverifikasi)
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

        // 2. Daftar Periode Bulan & Seleksi Bulan Aktif
        $daftarBulan = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        $bulanAktif = $this->resolveBulanAktif($bulanId);

        // 3. Rekap Keterisian Kuota Umum & Slot Khusus
        $kuotaData = $this->resolveKuotaSummary($bulanAktif);

        // 4. Action Hub: Pendaftaran Terkini yang Membutuhkan Tindakan (FIFO)
        $pendaftaranTerbaru = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan'])
            ->whereIn('status', [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::PerluPerbaikan])
            ->orderBy('created_at', 'asc')
            ->take(8)
            ->get();

        // 5. Monitoring Beban Pembimbing Lapangan (BR-13)
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

        // 7. Upcoming Offboarding Tracker
        $upcomingOffboardingList = $this->getUpcomingOffboardingList();

        // 8. Timeline Gelombang Magang Terpadu
        $timelineGelombangMagang = $this->getTimelineGelombangMagang();

        // 9. Quick Stats Tambahan
        $quickStats = $this->getQuickStats();

        return [
            'pengajuanMenunggu' => $pengajuanMenunggu,
            'perluPerbaikan' => $perluPerbaikan,
            'waitingListCount' => $waitingListCount,
            'pesertaAktifCount' => $pesertaAktifCount,
            'pengajuanSlaKritisCount' => $pengajuanSlaKritisCount,
            'daftarBulan' => $daftarBulan,
            'bulanAktif' => $bulanAktif,
            'kuotaList' => $kuotaData['kuotaList'],
            'totalKuotaUmum' => $kuotaData['totalKuotaUmum'],
            'totalKuotaTerisi' => $kuotaData['totalKuotaTerisi'],
            'slotKhususTerisi' => $kuotaData['slotKhususTerisi'],
            'pendaftaranTerbaru' => $pendaftaranTerbaru,
            'pembimbingWorkload' => $pembimbingWorkload,
            'penempatanTerbaru' => $penempatanTerbaru,
            'upcomingOffboardingList' => $upcomingOffboardingList,
            'timelineGelombangMagang' => $timelineGelombangMagang,
            'avgVerifikasiHari' => $quickStats['avgVerifikasiHari'],
            'realisasiKuotaPersen' => $quickStats['realisasiKuotaPersen'],
            'pendaftaranMingguIni' => $quickStats['pendaftaranMingguIni'],
        ];
    }

    /**
     * Menentukan bulan magang aktif berdasarkan parameter atau default saat ini.
     */
    protected function resolveBulanAktif(?string $bulanId): ?BulanMagang
    {
        if ($bulanId) {
            return BulanMagang::find($bulanId);
        }

        return BulanMagang::where('aktif', true)
            ->whereDate('bulan_mulai', '>=', now()->startOfMonth())
            ->orderBy('bulan_mulai', 'asc')
            ->first() ?? BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'desc')->first();
    }

    /**
     * Menghitung ringkasan kuota umum dan slot khusus periode aktif.
     *
     * @return array{kuotaList: Collection, totalKuotaUmum: int, totalKuotaTerisi: int, slotKhususTerisi: int}
     */
    protected function resolveKuotaSummary(?BulanMagang $bulanAktif): array
    {
        if (! $bulanAktif) {
            return [
                'kuotaList' => collect(),
                'totalKuotaUmum' => 0,
                'totalKuotaTerisi' => 0,
                'slotKhususTerisi' => 0,
            ];
        }

        $kuotaList = Kuota::with('jurusan')
            ->where('bulan_magang_id', $bulanAktif->id)
            ->where('aktif', true)
            ->get();

        $slotKhusus = SlotKhusus::whereDate('bulan_mulai', $bulanAktif->bulan_mulai)->first();

        return [
            'kuotaList' => $kuotaList,
            'totalKuotaUmum' => (int) $kuotaList->sum('jumlah'),
            'totalKuotaTerisi' => (int) $kuotaList->sum('terisi'),
            'slotKhususTerisi' => $slotKhusus ? (int) $slotKhusus->terisi : 0,
        ];
    }

    /**
     * Mengambil daftar peserta yang mendekati masa offboarding (14 hari ke depan).
     */
    protected function getUpcomingOffboardingList(): Collection
    {
        return Penempatan::with(['peserta.profil', 'pembimbing', 'bidang'])
            ->where('status', StatusPenempatanEnum::Aktif)
            ->where('tgl_selesai', '<=', now()->addDays(14)->endOfDay())
            ->orderBy('tgl_selesai', 'asc')
            ->take(6)
            ->get()
            ->map(function ($item) {
                $daysRemaining = (int) now()->startOfDay()->diffInDays($item->tgl_selesai->startOfDay(), false);

                return [
                    'penempatan' => $item,
                    'days_remaining' => $daysRemaining,
                    'is_overdue' => $daysRemaining < 0,
                ];
            });
    }

    /**
     * Membangun data timeline gelombang pendaftaran magang.
     */
    protected function getTimelineGelombangMagang(): Collection
    {
        return BulanMagang::with(['kuota' => fn ($q) => $q->where('aktif', true)])
            ->whereDate('bulan_mulai', '>=', now()->subMonths(2)->startOfMonth())
            ->whereDate('bulan_mulai', '<=', now()->addMonths(3)->endOfMonth())
            ->orderBy('bulan_mulai', 'asc')
            ->get()
            ->map(function ($b) {
                $today = now()->startOfDay();
                $buka = $b->tgl_buka ? $b->tgl_buka->startOfDay() : null;
                $tutup = $b->tgl_tutup ? $b->tgl_tutup->endOfDay() : null;

                $statusPendaftaran = 'Mendatang';
                if ($buka && $tutup) {
                    if ($today->between($buka, $tutup)) {
                        $statusPendaftaran = 'Pendaftaran Dibuka';
                    } elseif ($today->gt($tutup)) {
                        $statusPendaftaran = 'Pendaftaran Ditutup';
                    }
                }

                $kuotaTotal = (int) $b->kuota->sum('jumlah');
                $kuotaTerisi = (int) $b->kuota->sum('terisi');

                return [
                    'id' => $b->id,
                    'periode' => $b->bulan_mulai?->format('F Y') ?? '-',
                    'tgl_buka' => $b->tgl_buka?->format('d M Y') ?? '-',
                    'tgl_tutup' => $b->tgl_tutup?->format('d M Y') ?? '-',
                    'tgl_mulai' => $b->bulan_mulai?->format('d M Y') ?? '-',
                    'status_pendaftaran' => $statusPendaftaran,
                    'kuota_total' => $kuotaTotal,
                    'kuota_terisi' => $kuotaTerisi,
                    'persen_terisi' => $kuotaTotal > 0 ? min(100, (int) round(($kuotaTerisi / $kuotaTotal) * 100)) : 0,
                    'aktif' => (bool) $b->aktif,
                ];
            });
    }

    /**
     * Mengkalkulasi quick statistics operasional.
     *
     * @return array{avgVerifikasiHari: float, realisasiKuotaPersen: int, pendaftaranMingguIni: int}
     */
    protected function getQuickStats(): array
    {
        $processedRegistrations = Pendaftaran::whereNotNull('diajukan_at')
            ->whereNotNull('diputuskan_at')
            ->get(['diajukan_at', 'diputuskan_at']);

        if ($processedRegistrations->isNotEmpty()) {
            $totalDays = $processedRegistrations->sum(function ($p) {
                return max(0.5, $p->diajukan_at->diffInHours($p->diputuskan_at) / 24);
            });
            $avgVerifikasiHari = round($totalDays / $processedRegistrations->count(), 1);
        } else {
            $avgVerifikasiHari = 1.5;
        }

        $kapasitasKuotaAktif = (int) Kuota::where('aktif', true)->sum('jumlah');
        $terisiKuotaAktif = (int) Kuota::where('aktif', true)->sum('terisi');
        $realisasiKuotaPersen = $kapasitasKuotaAktif > 0
            ? min(100, (int) round(($terisiKuotaAktif / $kapasitasKuotaAktif) * 100))
            : 0;

        $pendaftaranMingguIni = Pendaftaran::where('created_at', '>=', now()->subDays(7))->count();

        return [
            'avgVerifikasiHari' => $avgVerifikasiHari,
            'realisasiKuotaPersen' => $realisasiKuotaPersen,
            'pendaftaranMingguIni' => $pendaftaranMingguIni,
        ];
    }
}
