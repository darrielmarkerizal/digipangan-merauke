<?php

namespace Modules\Dashboard\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Farmer\Models\Farmer;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductInteraction;
use Modules\Region\Models\Region;

class StatisticsService
{
    public function summary(): array
    {
        $viewsTotal = ProductInteraction::where('type', ProductInteraction::TYPE_VIEW)->count();
        $contactsTotal = ProductInteraction::where('type', ProductInteraction::TYPE_CONTACT)->count();

        return [
            // M-01 / M-02
            'products_active' => Product::where('is_active', true)->count(),
            'farmers_complete' => Farmer::where('is_active', true)->count(),

            // M-04 — overall funnel quality
            'views_total' => $viewsTotal,
            'contacts_total' => $contactsTotal,
            'contact_view_ratio' => $viewsTotal > 0 ? round($contactsTotal / $viewsTotal, 4) : 0.0,

            // M-06 — reach spread
            'products_contacted' => Product::where('is_active', true)
                ->whereHas('interactions', fn ($q) => $q->where('type', ProductInteraction::TYPE_CONTACT))
                ->count(),
            'products_never_contacted' => Product::where('is_active', true)
                ->whereDoesntHave('interactions', fn ($q) => $q->where('type', ProductInteraction::TYPE_CONTACT))
                ->count(),

            // M-03 / M-07 — trends over the last 12 months
            'contacts_monthly' => $this->monthlyContacts(),
            'audits_monthly' => $this->monthlyAudits(),

            // M-05 — per-region spread (all regions, including zero)
            'contacts_by_region' => $this->contactsByRegion(),
        ];
    }

    public function uncontactedProducts(int $perPage): LengthAwarePaginator
    {
        return Product::query()
            ->where('is_active', true)
            ->whereDoesntHave('interactions', fn ($q) => $q->where('type', ProductInteraction::TYPE_CONTACT))
            ->with('region')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    private function monthlyContacts(): array
    {
        $counts = ProductInteraction::query()
            ->where('type', ProductInteraction::TYPE_CONTACT)
            ->where('occurred_at', '>=', $this->windowStart())
            ->selectRaw("DATE_FORMAT(occurred_at, '%Y-%m') as ym, COUNT(*) as aggregate")
            ->groupBy('ym')
            ->pluck('aggregate', 'ym');

        return $this->zeroFilledSeries($counts);
    }

    private function monthlyAudits(): array
    {
        $counts = DB::table('audits')
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $this->windowStart())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as aggregate")
            ->groupBy('ym')
            ->pluck('aggregate', 'ym');

        return $this->zeroFilledSeries($counts);
    }

    private function contactsByRegion(): array
    {
        return Region::query()
            ->leftJoin('product_interactions as pi', function ($join) {
                $join->on('pi.region_id', '=', 'regions.id')
                    ->where('pi.type', '=', ProductInteraction::TYPE_CONTACT);
            })
            ->groupBy('regions.id', 'regions.name')
            ->orderBy('regions.name')
            ->selectRaw('regions.id as region_id, regions.name as region, COUNT(pi.id) as aggregate')
            ->get()
            ->map(fn ($row) => [
                'region_id' => (int) $row->region_id,
                'region' => $row->region,
                'count' => (int) $row->aggregate,
            ])
            ->all();
    }

    private function windowStart(): Carbon
    {
        return now()->startOfMonth()->subMonths(11);
    }

    private function zeroFilledSeries(Collection $countsByMonth): array
    {
        $series = [];

        for ($i = 11; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $series[] = ['month' => $ym, 'count' => (int) ($countsByMonth[$ym] ?? 0)];
        }

        return $series;
    }
}
