<?php

namespace Modules\Dashboard\Services;

use Carbon\Carbon;
use Modules\Farmer\Repositories\Contracts\FarmerGroupRepositoryInterface;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Modules\Page\Repositories\Contracts\FaqRepositoryInterface;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductInteraction;
use Modules\Product\Repositories\Contracts\ProductInteractionRepositoryInterface;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class DashboardService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly FarmerRepositoryInterface $farmers,
        private readonly FarmerGroupRepositoryInterface $farmerGroups,
        private readonly RegionRepositoryInterface $regions,
        private readonly PostRepositoryInterface $posts,
        private readonly FaqRepositoryInterface $faqs,
        private readonly ProductInteractionRepositoryInterface $interactions,
    ) {}

    public function getMetrics(): array
    {
        return [
            'active_products' => $this->products->countActive(),
            'farmers_and_groups' => $this->farmers->countActive() + $this->farmerGroups->countAll(),
            'wa_clicks' => $this->interactions->countByType(ProductInteractionType::Contact),
            'integrated_regions' => $this->regions->countActive(),
            'total_posts' => $this->posts->countPublished(),
            'active_faqs' => $this->faqs->countActive(),
        ];
    }

    public function getRegionDistribution(): array
    {
        $totalActiveProducts = $this->products->countActive();
        $divisor = max($totalActiveProducts, 1);
        $counts = $this->products->activeCountByRegion();

        $distribution = $this->regions->publicFiltered()->get(['id', 'name'])
            ->map(fn ($region) => [
                'name' => $region->name,
                'count' => (int) ($counts[$region->id] ?? 0),
                'percentage' => round(((int) ($counts[$region->id] ?? 0) / $divisor) * 100, 1),
            ])
            ->sortByDesc('count')
            ->values()
            ->all();

        return $this->normalisePercentages($distribution);
    }

    public function getTrendData(): array
    {
        $counts = $this->interactions->monthlyCountsByType(ProductInteractionType::Contact, $this->windowStart());

        $trendData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');
            $trendData[] = [
                'x' => $month->locale('id')->translatedFormat('M Y'),
                'y' => (int) ($counts[$key] ?? 0),
            ];
        }

        return $trendData;
    }

    public function getRecentActivities(): array
    {
        $recentProducts = $this->products->recentWithFarmerAndRegion(5)
            ->map(fn (Product $p) => [
                'id' => 'p_'.$p->id,
                'type' => 'product',
                'title' => $p->name,
                'description' => 'Produk ditambahkan oleh '.($p->farmer?->name ?? 'Petani'),
                'status' => $p->is_active ? 'Aktif' : 'Draft',
                'timestamp' => Carbon::parse($p->created_at)->timestamp,
                'date_human' => Carbon::parse($p->created_at)->locale('id')->diffForHumans(),
            ]);

        $recentInteractions = $this->interactions->recentContactsWithProduct(5)
            ->map(fn (ProductInteraction $i) => [
                'id' => 'i_'.$i->id,
                'type' => 'interaction',
                'title' => 'Klik WhatsApp',
                'description' => 'Pengunjung menghubungi produk '.($i->product?->name ?? 'Tidak diketahui'),
                'status' => 'Contact',
                'timestamp' => Carbon::parse($i->occurred_at)->timestamp,
                'date_human' => Carbon::parse($i->occurred_at)->locale('id')->diffForHumans(),
            ]);

        return $recentProducts->concat($recentInteractions)
            ->sortByDesc('timestamp')
            ->take(6)
            ->values()
            ->all();
    }

    public function getPopularProducts(): array
    {
        return $this->products->popularByContacts(5)
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'region' => $p->region?->name ?? '-',
                'contact_count' => (int) $p->contact_count,
            ])
            ->all();
    }

    private function windowStart(): Carbon
    {
        return Carbon::now()->subMonths(11)->startOfMonth();
    }

    /**
     * Ensure the rounded percentages add up to 100 by absorbing the rounding
     * remainder into the largest slice, so the UI never shows a 99.9% total.
     *
     * @param  array<int, array{name: string, count: int, percentage: float}>  $distribution
     * @return array<int, array{name: string, count: int, percentage: float}>
     */
    private function normalisePercentages(array $distribution): array
    {
        $withValues = array_filter($distribution, fn ($item) => $item['count'] > 0);

        if (empty($withValues)) {
            return $distribution;
        }

        $sum = array_sum(array_column($withValues, 'percentage'));
        $diff = round(100 - $sum, 1);

        if (abs($diff) < 0.05) {
            return $distribution;
        }

        $largestIndex = null;
        foreach ($distribution as $index => $item) {
            if ($item['count'] <= 0) {
                continue;
            }
            if ($largestIndex === null || $item['count'] > $distribution[$largestIndex]['count']) {
                $largestIndex = $index;
            }
        }

        if ($largestIndex !== null) {
            $distribution[$largestIndex]['percentage'] = round(
                $distribution[$largestIndex]['percentage'] + $diff,
                1
            );
        }

        return $distribution;
    }
}
