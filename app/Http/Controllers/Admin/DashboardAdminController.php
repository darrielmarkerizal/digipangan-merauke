<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\Dashboard\Services\DashboardService;

class DashboardAdminController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'metrics' => $this->dashboardService->getMetrics(),
            'region_distribution' => $this->dashboardService->getRegionDistribution(),
            'trend_data' => $this->dashboardService->getTrendData(),
            'recent_activities' => $this->dashboardService->getRecentActivities(),
            'popular_products' => $this->dashboardService->getPopularProducts(),
        ]);
    }
}
