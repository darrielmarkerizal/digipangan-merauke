<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Dashboard\Services\DashboardService;

class DashboardAdminController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;
        $districtName = $isDistrictAdmin ? $user?->region?->name : null;

        return Inertia::render('Admin/Dashboard', [
            'is_district_admin' => $isDistrictAdmin,
            'district_name' => $districtName,
            'metrics' => $this->dashboardService->getMetrics($regionId),
            'region_distribution' => $regionId
                ? $this->dashboardService->getVillageDistribution($regionId)
                : $this->dashboardService->getRegionDistribution(),
            'trend_data' => $this->dashboardService->getTrendData($regionId),
            'recent_activities' => $this->dashboardService->getRecentActivities($regionId),
            'popular_products' => $this->dashboardService->getPopularProducts($regionId),
        ]);
    }
}
