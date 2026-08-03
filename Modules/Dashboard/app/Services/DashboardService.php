<?php

namespace Modules\Dashboard\Services;

use Carbon\Carbon;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Page\Models\Faq;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductInteraction;
use Modules\Region\Models\Region;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getMetrics(): array
    {
        return [
            'active_products' => Product::where('is_active', true)->count(),
            'farmers_and_groups' => Farmer::count() + FarmerGroup::count(),
            'wa_clicks' => ProductInteraction::where('type', 'contact')->count(),
            'integrated_regions' => Region::where('is_active', true)->count(),
            'total_posts' => Post::where('status', 'published')->count(),
            'active_faqs' => Faq::where('is_active', true)->count(),
        ];
    }

    public function getRegionDistribution(): array
    {
        $regions = Region::where('is_active', true)->get();
        $totalActiveProducts = Product::where('is_active', true)->count();
        $totalProducts = max($totalActiveProducts, 1);
        
        return $regions->map(function ($region) use ($totalProducts) {
            $count = Product::where('is_active', true)->where('region_id', $region->id)->count();
            return [
                'name' => $region->name,
                'count' => $count,
                'percentage' => round(($count / $totalProducts) * 100, 1)
            ];
        })->sortByDesc('percentage')->values()->toArray();
    }

    public function getTrendData(): array
    {
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
        
        return $trendData;
    }

    public function getRecentActivities(): array
    {
        $recentProducts = Product::with(['farmer', 'region'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => 'p_'.$p->id,
                    'type' => 'product',
                    'title' => $p->name,
                    'description' => 'Produk ditambahkan oleh ' . ($p->farmer?->name ?? 'Petani'),
                    'status' => $p->is_active ? 'Aktif' : 'Draft',
                    'timestamp' => Carbon::parse($p->created_at)->timestamp,
                    'date_human' => Carbon::parse($p->created_at)->diffForHumans(),
                ];
            });

        $recentInteractions = ProductInteraction::with(['product'])
            ->where('type', 'contact')
            ->orderByDesc('occurred_at')
            ->take(5)
            ->get()
            ->map(function($i) {
                return [
                    'id' => 'i_'.$i->id,
                    'type' => 'interaction',
                    'title' => 'Klik WhatsApp',
                    'description' => 'Pengunjung menghubungi produk ' . ($i->product?->name ?? 'Tidak diketahui'),
                    'status' => 'Contact',
                    'timestamp' => Carbon::parse($i->occurred_at)->timestamp,
                    'date_human' => Carbon::parse($i->occurred_at)->diffForHumans(),
                ];
            });

        $activities = $recentProducts->concat($recentInteractions)
            ->sortByDesc('timestamp')
            ->take(6)
            ->values()
            ->toArray();

        return $activities;
    }

    public function getPopularProducts(): array
    {
        return Product::with(['region'])
            ->where('is_active', true)
            ->withCount(['interactions as contact_count' => function ($query) {
                $query->where('type', 'contact');
            }])
            ->orderByDesc('contact_count')
            ->take(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'region' => $p->region?->name ?? '-',
                    'contact_count' => $p->contact_count,
                ];
            })
            ->toArray();
    }
}
