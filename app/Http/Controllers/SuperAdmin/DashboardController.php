<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdminExecutiveMetricsService;
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
}
