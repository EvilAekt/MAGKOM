<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\BulanMagang;
use App\Models\User;
use App\Services\AdminDashboardMetricsService;
use App\Services\PembimbingDashboardMetricsService;
use App\Services\SuperAdminExecutiveMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsServicesArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_metrics_service_builds_complete_data_structure(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->startOfMonth(),
            'tgl_tutup' => now()->endOfMonth(),
            'aktif' => true,
        ]);

        $service = app(AdminDashboardMetricsService::class);
        $data = $service->buildDashboardData((string) $bulan->id);

        $this->assertArrayHasKey('pengajuanMenunggu', $data);
        $this->assertArrayHasKey('perluPerbaikan', $data);
        $this->assertArrayHasKey('waitingListCount', $data);
        $this->assertArrayHasKey('pesertaAktifCount', $data);
        $this->assertArrayHasKey('pengajuanSlaKritisCount', $data);
        $this->assertArrayHasKey('daftarBulan', $data);
        $this->assertArrayHasKey('bulanAktif', $data);
        $this->assertArrayHasKey('kuotaList', $data);
        $this->assertArrayHasKey('totalKuotaUmum', $data);
        $this->assertArrayHasKey('totalKuotaTerisi', $data);
        $this->assertArrayHasKey('slotKhususTerisi', $data);
        $this->assertArrayHasKey('pendaftaranTerbaru', $data);
        $this->assertArrayHasKey('pembimbingWorkload', $data);
        $this->assertArrayHasKey('penempatanTerbaru', $data);
        $this->assertArrayHasKey('upcomingOffboardingList', $data);
        $this->assertArrayHasKey('timelineGelombangMagang', $data);
        $this->assertArrayHasKey('avgVerifikasiHari', $data);
        $this->assertArrayHasKey('realisasiKuotaPersen', $data);
        $this->assertArrayHasKey('pendaftaranMingguIni', $data);

        $this->assertEquals($bulan->id, $data['bulanAktif']->id);
    }

    public function test_pembimbing_dashboard_metrics_service_builds_complete_data_structure(): void
    {
        $pembimbing = User::create([
            'nama' => 'Pembimbing Test',
            'email' => 'pembimbing.test@magkom.go.id',
            'password' => bcrypt('Password123!'),
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $service = app(PembimbingDashboardMetricsService::class);
        $data = $service->buildDashboardData($pembimbing);

        $this->assertArrayHasKey('totalMenteeAktif', $data);
        $this->assertArrayHasKey('maxBebanKapasitas', $data);
        $this->assertArrayHasKey('persentaseKapasitas', $data);
        $this->assertArrayHasKey('logbookPendingCount', $data);
        $this->assertArrayHasKey('progresPendingCount', $data);
        $this->assertArrayHasKey('tugasAktifCount', $data);
        $this->assertArrayHasKey('tugasOverdueCount', $data);
        $this->assertArrayHasKey('deadlineMendekatiHari', $data);
        $this->assertArrayHasKey('upcomingDeadlines', $data);
        $this->assertArrayHasKey('inactiveLogbookMentees', $data);
        $this->assertArrayHasKey('jenjangCounts', $data);
        $this->assertArrayHasKey('pendingProgresLogs', $data);
        $this->assertArrayHasKey('pendingLogbooks', $data);
        $this->assertArrayHasKey('offboardingMentees', $data);
        $this->assertArrayHasKey('menteeAktifList', $data);
        $this->assertArrayHasKey('recentNilaiRevisions', $data);
        $this->assertArrayHasKey('competencyRadar', $data);
        $this->assertArrayHasKey('menteeAtRiskList', $data);
        $this->assertArrayHasKey('upcomingAgendaBimbingan', $data);
        $this->assertArrayHasKey('aktivitasMingguIni', $data);
    }

    public function test_superadmin_executive_metrics_service_builds_complete_data_structure(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->startOfMonth(),
            'tgl_tutup' => now()->endOfMonth(),
            'aktif' => true,
        ]);

        $service = app(SuperAdminExecutiveMetricsService::class);
        $data = $service->buildDashboardData((string) $bulan->id);

        $this->assertArrayHasKey('totalPengguna', $data);
        $this->assertArrayHasKey('menungguVerifikasi', $data);
        $this->assertArrayHasKey('pesertaAktif', $data);
        $this->assertArrayHasKey('akunNonaktif', $data);
        $this->assertArrayHasKey('penggunaTerbaru', $data);
        $this->assertArrayHasKey('daftarBulan', $data);
        $this->assertArrayHasKey('bulanAktif', $data);
        $this->assertArrayHasKey('totalKuotaUmum', $data);
        $this->assertArrayHasKey('totalKuotaTerisi', $data);
        $this->assertArrayHasKey('pembimbingList', $data);
        $this->assertArrayHasKey('systemHealth', $data);
        $this->assertArrayHasKey('securityPosture', $data);
        $this->assertArrayHasKey('systemSummary', $data);
        $this->assertArrayHasKey('trenPendaftar', $data);

        $this->assertEquals($bulan->id, $data['bulanAktif']->id);
    }
}
