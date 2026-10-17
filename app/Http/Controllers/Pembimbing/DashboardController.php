<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Services\PembimbingDashboardMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected PembimbingDashboardMetricsService $metricsService
    ) {}

    /**
     * Tampilan utama Dashboard Operasional Pembimbing (F-08, F-09, F-10, F-12, BR-13, TDD Bab 4).
     * Mendukung 4-Tab Eksekutif:
     * - Tab 1: Ringkasan Utama (Figma Default Node 560-41611)
     * - Tab 2: Pusat Antrean Reviu Cepat (Action Hub)
     * - Tab 3: Monitoring Mentee 360° & Progres Magang
     * - Tab 4: Evaluasi Rubrik & Capaian Magang (Masa Tenggang BR-11)
     */
    public function index(Request $request): View|JsonResponse
    {
        $data = $this->metricsService->buildDashboardData($request->user());

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('pembimbing.dashboard', $data);
    }
}
