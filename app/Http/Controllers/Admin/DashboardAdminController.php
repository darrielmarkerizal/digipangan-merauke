<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Inertia\Inertia;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductInteraction;
use Modules\Region\Models\Region;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalActiveProducts = Product::where('is_active', true)->count();
        $totalFarmersAndGroups = Farmer::count() + FarmerGroup::count();
        $totalWaClicks = ProductInteraction::where('type', 'contact')->count();
        $totalRegions = Region::where('is_active', true)->count();

        $regions = Region::where('is_active', true)->get();
        $totalProducts = max($totalActiveProducts, 1);
        
        $regionDistribution = $regions->map(function ($region) use ($totalProducts) {
            $count = Product::where('is_active', true)->where('region_id', $region->id)->count();
            return [
                'name' => $region->name,
                'count' => $count,
                'percentage' => round(($count / $totalProducts) * 100, 1)
            ];
        })->sortByDesc('percentage')->values()->toArray();

        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();
        
        $interactions = ProductInteraction::where('type', 'contact')
            ->where('occurred_at', '>=', $twelveMonthsAgo)
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->occurred_at)->format('Y-m');
            });

        $trendData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');
            $trendData[] = [
                'x' => $month->translatedFormat('M Y'),
                'y' => isset($interactions[$key]) ? $interactions[$key]->count() : 0
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'active_products' => $totalActiveProducts,
                'farmers_and_groups' => $totalFarmersAndGroups,
                'wa_clicks' => $totalWaClicks,
                'integrated_regions' => $totalRegions,
            ],
            'region_distribution' => $regionDistribution,
            'trend_data' => $trendData,
        ]);
    }
}
