<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return parent::query()->with(['media', 'category', 'unit', 'farmer', 'region']);
    }

    protected function visibilityScope(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('product_category_id'),
            AllowedFilter::exact('unit_id'),
            AllowedFilter::exact('farmer_id'),
            AllowedFilter::exact('region_id'),
            AllowedFilter::exact('is_featured'),
            AllowedFilter::exact('is_region_featured'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::exact('stock_available'),
            AllowedFilter::callback('category', function (Builder $query, $value) {
                $slugs = is_array($value) ? $value : explode(',', (string) $value);
                $slugs = array_filter(array_map('trim', $slugs));
                if (! empty($slugs)) {
                    $query->whereHas('category', fn (Builder $categoryQuery) => $categoryQuery->whereIn('slug', $slugs));
                }
            }),
            AllowedFilter::callback('region', function (Builder $query, $value) {
                $slugs = is_array($value) ? $value : explode(',', (string) $value);
                $slugs = array_filter(array_map('trim', $slugs));
                if (! empty($slugs)) {
                    $query->whereHas('region', fn (Builder $regionQuery) => $regionQuery->whereIn('slug', $slugs));
                }
            }),
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'description'];
    }

    protected function allowedIncludes(): array
    {
        return ['category', 'unit', 'farmer', 'region'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'price', 'created_at'];
    }

    /**
     * Paginated, filtered listing constrained to a single farmer's own
     * products — used by the farmer self-service product area.
     */
    public function paginateFilteredForFarmer(int $farmerId, ?int $perPage = null): LengthAwarePaginator
    {
        return $this->paginateQuery(
            $this->buildFiltered($this->query()->where('farmer_id', $farmerId)),
            $perPage
        );
    }

    /**
     * Own-product counters for the farmer self-service dashboard.
     */
    public function metricsForFarmer(int $farmerId): array
    {
        $base = $this->model->newQuery()->where('farmer_id', $farmerId);

        return [
            'total_products' => (clone $base)->count(),
            'active_products' => (clone $base)->where('is_active', true)->count(),
            'out_of_stock_products' => (clone $base)->where('stock_available', false)->count(),
        ];
    }

    /**
     * Most recent own products for the farmer self-service dashboard.
     */
    public function recentForFarmer(int $farmerId, int $limit = 5): Collection
    {
        return $this->model->newQuery()
            ->where('farmer_id', $farmerId)
            ->with(['media', 'category', 'unit'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Active products flagged as featured, for the homepage highlight section.
     */
    public function publicFeatured(int $limit = 8): Collection
    {
        return $this->visibilityScope($this->query())
            ->where('is_featured', true)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Most recently published active products, for the homepage "latest" section.
     */
    public function publicLatest(int $limit = 8): Collection
    {
        return $this->visibilityScope($this->query())
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Active products sharing a category or region with a given product,
     * for the "related products" section on a product detail page.
     */
    public function publicRelated(int $excludeId, ?int $categoryId, ?int $regionId, int $limit = 4): Collection
    {
        return $this->visibilityScope($this->query())
            ->where('id', '!=', $excludeId)
            ->where(function (Builder $query) use ($categoryId, $regionId) {
                if ($categoryId) {
                    $query->where('product_category_id', $categoryId);
                }
                if ($regionId) {
                    $query->orWhere('region_id', $regionId);
                }
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Minimal {slug, updated_at} rows for active products, for the sitemap.
     */
    public function publicSitemapEntries(): Collection
    {
        return $this->visibilityScope($this->model->newQuery())->get(['slug', 'updated_at']);
    }

    /**
     * Dashboard/statistics counters — active product totals and reach.
     */
    public function countActive(?int $regionId = null): int
    {
        $query = $this->visibilityScope($this->model->newQuery());
        if ($regionId !== null) {
            $query->where('region_id', $regionId);
        }

        return $query->count();
    }

    public function countContactedActive(): int
    {
        return $this->visibilityScope($this->model->newQuery())
            ->whereHas('interactions', fn (Builder $query) => $query->where('type', ProductInteractionType::Contact))
            ->count();
    }

    public function countNeverContactedActive(): int
    {
        return $this->visibilityScope($this->model->newQuery())
            ->whereDoesntHave('interactions', fn (Builder $query) => $query->where('type', ProductInteractionType::Contact))
            ->count();
    }

    public function paginateNeverContacted(int $perPage): LengthAwarePaginator
    {
        return $this->visibilityScope($this->model->newQuery())
            ->whereDoesntHave('interactions', fn (Builder $query) => $query->where('type', ProductInteractionType::Contact))
            ->with('region')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Active product counts keyed by region_id, for the dashboard's
     * region-distribution chart.
     */
    public function activeCountByRegion(): SupportCollection
    {
        return $this->visibilityScope($this->model->newQuery())
            ->whereNotNull('region_id')
            ->groupBy('region_id')
            ->selectRaw('region_id, COUNT(*) as aggregate')
            ->pluck('aggregate', 'region_id');
    }

    /**
     * Active product counts keyed by village_id (via farmer), for the district
     * dashboard's village-distribution chart.
     */
    public function activeCountByVillage(int $regionId): SupportCollection
    {
        return $this->model->newQuery()
            ->where('products.is_active', true)
            ->where('products.region_id', $regionId)
            ->whereHas('farmer', fn (Builder $query) => $query->whereNotNull('village_id'))
            ->join('farmers', 'products.farmer_id', '=', 'farmers.id')
            ->groupBy('farmers.village_id')
            ->selectRaw('farmers.village_id, COUNT(products.id) as aggregate')
            ->pluck('aggregate', 'farmers.village_id');
    }

    /**
     * Most recently created products with just enough relations for the
     * dashboard's recent-activity feed.
     */
    public function recentWithFarmerAndRegion(int $limit = 5, ?int $regionId = null): Collection
    {
        $query = $this->model->newQuery()
            ->with(['farmer:id,name', 'region:id,name']);

        if ($regionId !== null) {
            $query->where('region_id', $regionId);
        }

        return $query->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Active products ranked by contact-click count, for the dashboard's
     * popular-products widget.
     */
    public function popularByContacts(int $limit = 5, ?int $regionId = null): Collection
    {
        $query = $this->visibilityScope($this->model->newQuery())
            ->with(['region:id,name'])
            ->withCount(['interactions as contact_count' => fn (Builder $query) => $query->where('type', ProductInteractionType::Contact)]);

        if ($regionId !== null) {
            $query->where('region_id', $regionId);
        }

        return $query->orderByDesc('contact_count')
            ->take($limit)
            ->get();
    }
}
