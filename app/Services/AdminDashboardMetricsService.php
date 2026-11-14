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
use Illuminate\Support\Facades\Cache;

class AdminDashboardMetricsService
{
    /**
     * Membangun seluruh data metrik operasional untuk Dashboard Admin (PRD 9.2).
     *
     * @return array<string, mixed>
     */
    public function buildDashboardData(?string $bulanId = null): array
    {
        if (app()->environment('testing')) {
            return $this->computeFreshDashboardData($bulanId);
        }

        $cacheKey = 'admin_metrics_dashboard_'.($bulanId ?? 'default');

        return Cache::remember($cacheKey, 30, function () use ($bulanId) {
            return $this->computeFreshDashboardData($bulanId);
        });
    }

    /**
     * Komputasi segar seluruh data metrik operasional untuk Dashboard Admin (PRD 9.2).
     *
     * @return array<string, mixed>
     */
    public function computeFreshDashboardData(?string $bulanId = null): array
    {
        // 1. Metrik Operasional Pendaftaran & Penempatan
        $pengajuanMenunggu = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count();
        $perluPerbaikan = Pendaftaran::where('status', StatusPendaftaranEnum::PerluPerbaikan)->count();
        $waitingListCount = Pendaftaran::where('status', StatusPendaftaranEnum::WaitingList)->count();
        $pendaftaranDiterimaCount = Pendaftaran::where('status', StatusPendaftaranEnum::Diterima)->count();
        $pendaftaranDitolakCount = Pendaftaran::where('status', StatusPendaftaranEnum::Ditolak)->count();
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
            ->where('status', StatusPendaftaranEnum::Diajukan)
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

        // 10. Data Khusus Tampilan Dashboard Administrasi Rebranded (Figma Design Parity)
        $quotaByMajor = $this->resolveQuotaByMajor($bulanAktif, $kuotaData['kuotaList']);
        $pendingVerification = $pengajuanMenunggu;
        $quotaFilled = $kuotaData['totalKuotaTerisi'];
        $quotaTotal = $kuotaData['totalKuotaUmum'];
        $quotaPercentage = $quotaTotal > 0 ? (int) round(($quotaFilled / $quotaTotal) * 100) : 0;
        $activeInterns = $pesertaAktifCount;
        $avgVerificationDays = $quickStats['avgVerifikasiHari'];
        $applicantsLast7Days = $quickStats['pendaftaranMingguIni'];

        return [
            'pengajuanMenunggu' => $pengajuanMenunggu,
            'perluPerbaikan' => $perluPerbaikan,
            'waitingListCount' => $waitingListCount,
            'pendaftaranDiterimaCount' => $pendaftaranDiterimaCount,
            'pendaftaranDitolakCount' => $pendaftaranDitolakCount,
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
            // Kontrak Data Tampilan Dashboard Baru (Figma Alignment)
            'pendingVerification' => $pendingVerification,
            'quotaFilled' => $quotaFilled,
            'quotaTotal' => $quotaTotal,
            'quotaRemaining' => max(0, $quotaTotal - $quotaFilled),
            'quotaPercentage' => $quotaPercentage,
            'activeInterns' => $activeInterns,
            'avgVerificationDays' => $avgVerificationDays,
            'applicantsLast7Days' => $applicantsLast7Days,
            'quotaByMajor' => $quotaByMajor,
            'activeMonthName' => $bulanAktif?->bulan_mulai ? $bulanAktif->bulan_mulai->translatedFormat('F Y') : now()->translatedFormat('F Y'),
            'activeYear' => (int) ($bulanAktif?->bulan_mulai?->format('Y') ?? now()->format('Y')),
            'activeMonthNum' => (int) ($bulanAktif?->bulan_mulai?->format('n') ?? now()->format('n')),
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
            $avgVerifikasiHari = 0.0;
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

    /**
     * Memetakan data 10 jurusan resmi MagKom untuk visualisasi stacked bar chart.
     *
     * @return array<int, array{name: string, shortName: string, filled: int, total: int, remaining: int, is_full: bool, filled_pct: float, remaining_pct: float}>
     */
    protected function resolveQuotaByMajor(?BulanMagang $bulanAktif, Collection $kuotaList): array
    {
        $definisiJurusan = [
            ['name' => 'Teknik Komputer dan Jaringan', 'shortName' => 'TKJ', 'default_filled' => 5, 'default_total' => 5],
            ['name' => 'Desain Grafis', 'shortName' => 'Desain Grafis', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Administrasi Perkantoran', 'shortName' => 'Adm. Perkantoran', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Rekayasa Perangkat Lunak (RPL)', 'shortName' => 'RPL', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Informatika', 'shortName' => 'Informatika', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Manajemen Informatika', 'shortName' => 'Manajemen Informatika', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Sistem Informasi', 'shortName' => 'Sistem Informasi', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Teknologi Informasi', 'shortName' => 'Teknologi Informasi', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Statistik', 'shortName' => 'Statistik', 'default_filled' => 2, 'default_total' => 5],
            ['name' => 'Ilmu Komunikasi', 'shortName' => 'Ilmu Komunikasi', 'default_filled' => 2, 'default_total' => 5],
        ];

        $chartData = [];
        $maxScale = 5;

        foreach ($definisiJurusan as $def) {
            // Cari data kuota aktif yang cocok dengan nama jurusan
            $kuotaItem = $kuotaList->first(function ($k) use ($def) {
                $nama = strtolower($k->jurusan?->nama_jurusan ?? '');
                $target = strtolower($def['name']);

                return str_contains($nama, strtolower($def['shortName'])) || str_contains($nama, $target);
            });

            if ($kuotaItem) {
                $total = (int) $kuotaItem->jumlah;
                $filled = min($total, (int) $kuotaItem->terisi);
            } else {
                $total = 0;
                $filled = 0;
            }

            $remaining = max(0, $total - $filled);
            $isFull = ($filled >= $total && $total > 0);
            $scale = max($maxScale, $total);
            $filledPct = $scale > 0 ? min(100.0, round(($filled / $scale) * 100, 1)) : 0.0;
            $remainingPct = $scale > 0 ? min(100.0 - $filledPct, round(($remaining / $scale) * 100, 1)) : 0.0;

            $chartData[] = [
                'name' => $def['name'],
                'shortName' => $def['shortName'],
                'filled' => $filled,
                'total' => $total,
                'remaining' => $remaining,
                'is_full' => $isFull,
                'filled_pct' => $filledPct,
                'remaining_pct' => $remainingPct,
            ];
        }

        return $chartData;
    }
}
