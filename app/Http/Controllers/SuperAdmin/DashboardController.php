<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusKetersediaanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Services\AuditService;
use App\Services\BulanStatusService;
use App\Services\SuperAdminExecutiveMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SuperAdminExecutiveMetricsService $metricsService
    ) {}

    /**
     * Menampilkan dashboard eksekutif SuperAdmin MagKom (PRD 9.2, 4.3 & Unified Hub).
     */
    public function index(Request $request): View
    {
        $selectedBulanId = $request->query('bulan_id');
        $dashboardData = $this->metricsService->buildDashboardData($selectedBulanId);

        return view('superadmin.dashboard', $dashboardData);
    }

    /**
     * Mengubah kran pendaftaran (buka paksa / tutup paksa / otomatis) secara real-time.
     */
    public function toggleKran(
        Request $request,
        BulanStatusService $bulanStatusService,
        AuditService $auditService
    ): JsonResponse {
        $validated = $request->validate([
            'bulan_id' => ['nullable', 'integer', 'exists:bulan_magang,id'],
            'target_status' => ['nullable', 'string', 'in:tersedia,tutup,otomatis'],
        ]);

        $bulanId = isset($validated['bulan_id']) ? (int) $validated['bulan_id'] : null;
        /** @var BulanMagang|null $bulan */
        $bulan = $bulanId
            ? BulanMagang::where('id', $bulanId)->first()
            : BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'desc')->first();

        if (! $bulan instanceof BulanMagang) {
            return response()->json([
                'success' => false,
                'message' => 'Bulan magang tidak ditemukan.',
            ], 404);
        }

        $targetStatus = $validated['target_status'] ?? null;
        $nilaiLama = $bulan->toArray();

        if ($targetStatus === 'otomatis') {
            $bulan->override_status = null;
        } elseif ($targetStatus === 'tersedia') {
            $bulan->override_status = StatusKetersediaanEnum::Tersedia;
        } elseif ($targetStatus === 'tutup') {
            $bulan->override_status = StatusKetersediaanEnum::Tutup;
        } else {
            // Toggle berdasarkan status ketersediaan saat ini
            $currentStatus = $bulanStatusService->status($bulan);
            if ($currentStatus === StatusKetersediaanEnum::Tersedia) {
                $bulan->override_status = StatusKetersediaanEnum::Tutup;
            } else {
                $bulan->override_status = StatusKetersediaanEnum::Tersedia;
            }
        }

        $bulan->save();

        $this->metricsService->invalidateDashboardCache();

        $newStatus = $bulanStatusService->status($bulan);

        $auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bulan_magang',
            idData: $bulan->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $bulan->toArray(),
            detail: [
                'aksi' => 'Mengubah Kran Pendaftaran',
                'override_status' => $bulan->override_status?->value ?? 'otomatis',
                'status_hasil' => $newStatus->value,
                'bulan_magang' => $bulan->nama_bulan,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Kran pendaftaran bulan '.$bulan->nama_bulan.' berhasil diubah menjadi '.$newStatus->label().'.',
            'data' => [
                'bulan_id' => $bulan->id,
                'status' => $newStatus->value,
                'status_label' => $newStatus->label(),
                'is_open' => $newStatus === StatusKetersediaanEnum::Tersedia,
                'is_override' => $bulan->override_status !== null,
                'mode_label' => $bulan->override_status !== null ? 'Manual' : 'Otomatis',
            ],
        ]);
    }
}
