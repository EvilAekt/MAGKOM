<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardMetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AdminDashboardMetricsService $metricsService
    ) {}

    /**
     * Menampilkan dashboard operasional administrasi MagKom (PRD 9.2).
     */
    public function index(Request $request): View
    {
        $bulanId = $request->query('bulan_id');
        $dashboardData = $this->metricsService->buildDashboardData($bulanId);

        return view('admin.dashboard', $dashboardData);
    }
}
