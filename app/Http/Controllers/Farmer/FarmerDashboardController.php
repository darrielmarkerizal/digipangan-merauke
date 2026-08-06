<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Dashboard\Services\FarmerDashboardService;
use Modules\Farmer\Http\Resources\FarmerResource;
use Modules\Product\Http\Resources\ProductResource;

class FarmerDashboardController extends Controller
{
    public function __construct(private readonly FarmerDashboardService $dashboardService) {}

    public function index(): Response
    {
        $farmer = auth()->user()->farmer;

        abort_if(! $farmer, 403, 'Profil petani tidak ditemukan untuk akun ini.');

        $farmer->load(['region', 'village', 'farmerGroup']);

        return Inertia::render('Farmer/Dashboard/Index', [
            'farmer' => (new FarmerResource($farmer))->resolve(),
            'metrics' => $this->dashboardService->getMetrics($farmer),
            'recent_products' => ProductResource::collection($this->dashboardService->getRecentProducts($farmer))->resolve(),
        ]);
    }
}
