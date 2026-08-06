<?php

namespace Modules\Product\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateFilteredForFarmer(int $farmerId, ?int $perPage = null): LengthAwarePaginator;

    public function metricsForFarmer(int $farmerId): array;

    public function recentForFarmer(int $farmerId, int $limit = 5): Collection;

    public function publicFeatured(int $limit = 8): Collection;

    public function publicLatest(int $limit = 8): Collection;

    public function publicRelated(int $excludeId, ?int $categoryId, ?int $regionId, int $limit = 4): Collection;

    public function publicSitemapEntries(): Collection;

    public function countActive(): int;

    public function countContactedActive(): int;

    public function countNeverContactedActive(): int;

    public function paginateNeverContacted(int $perPage): LengthAwarePaginator;

    public function activeCountByRegion(): SupportCollection;

    public function recentWithFarmerAndRegion(int $limit = 5): Collection;

    public function popularByContacts(int $limit = 5): Collection;
}
