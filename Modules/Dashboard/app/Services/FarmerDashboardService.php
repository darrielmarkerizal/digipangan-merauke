<?php

namespace Modules\Dashboard\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Farmer\Models\Farmer;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;

class FarmerDashboardService
{
    public function __construct(private readonly ProductRepositoryInterface $products) {}

    public function getMetrics(Farmer $farmer): array
    {
        return $this->products->metricsForFarmer($farmer->id);
    }

    public function getRecentProducts(Farmer $farmer, int $limit = 5): Collection
    {
        return $this->products->recentForFarmer($farmer->id, $limit);
    }
}
