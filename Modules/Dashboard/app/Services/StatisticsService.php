<?php

namespace Modules\Dashboard\Services;

use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Modules\Product\Models\ProductInteraction;
use Modules\Product\Repositories\Contracts\ProductInteractionRepositoryInterface;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class StatisticsService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly FarmerRepositoryInterface $farmers,
        private readonly RegionRepositoryInterface $regions,
        private readonly ProductInteractionRepositoryInterface $interactions,
        private readonly AuditRepositoryInterface $audits,
    ) {}

    public function summary(): array
    {
        $viewsTotal = $this->interactions->countByType(ProductInteraction::TYPE_VIEW);
        $contactsTotal = $this->interactions->countByType(ProductInteraction::TYPE_CONTACT);

        return [
            // M-01 / M-02
            'products_active' => $this->products->countActive(),
            'farmers_complete' => $this->farmers->countActive(),

            // M-04 — overall funnel quality
            'views_total' => $viewsTotal,
            'contacts_total' => $contactsTotal,
            'contact_view_ratio' => $viewsTotal > 0 ? round($contactsTotal / $viewsTotal, 4) : 0.0,

            // M-06 — reach spread
            'products_contacted' => $this->products->countContactedActive(),
            'products_never_contacted' => $this->products->countNeverContactedActive(),

            // M-03 / M-07 — trends over the last 12 months
            'contacts_monthly' => $this->monthlyContacts(),
            'audits_monthly' => $this->monthlyAudits(),

            // M-05 — per-region spread (all regions, including zero)
            'contacts_by_region' => $this->contactsByRegion(),
        ];
    }

    public function uncontactedProducts(int $perPage): LengthAwarePaginator
    {
        return $this->products->paginateNeverContacted($perPage);
    }

    private function monthlyContacts(): array
    {
        $counts = $this->interactions->monthlyCountsByType(ProductInteraction::TYPE_CONTACT, $this->windowStart());

        return $this->zeroFilledSeries($counts);
    }

    private function monthlyAudits(): array
    {
        $counts = $this->audits->monthlyCounts($this->windowStart());

        return $this->zeroFilledSeries($counts);
    }

    private function contactsByRegion(): array
    {
        return $this->regions->contactCountsByRegion()
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
