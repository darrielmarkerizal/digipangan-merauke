<?php

namespace Modules\Region\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Region\Models\Region;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    public function __construct(Region $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return parent::query()
            ->with(['media'])
            ->withCount([
                'villages',
                'farmerGroups',
                'products as active_products_count' => fn (Builder $query) => $query->where('is_active', true),
            ]);
    }

    protected function visibilityScope(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('is_active'),
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'description'];
    }

    protected function allowedIncludes(): array
    {
        return ['villages', 'farmerGroups', 'products'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'name';
    }

    public function publicFindBySlugWithFeaturedProducts(string $slug): ?\Illuminate\Database\Eloquent\Model
    {
        $region = $this->publicFindBySlug($slug);
        
        if ($region) {
            $region->setRelation('regionFeaturedProducts', \Modules\Product\Models\Product::query()
                ->where('region_id', $region->id)
                ->where('is_active', true)
                ->where('is_region_featured', true)
                ->with(['media', 'category', 'farmer'])
                ->latest()
                ->get());

            $region->setRelation('regionFarmers', \Modules\Farmer\Models\Farmer::query()
                ->where('region_id', $region->id)
                ->where('is_active', true)
                ->with(['media', 'farmerGroup:id,name', 'commodities:id,name,slug'])
                ->withCount(['products as products_count' => fn (Builder $query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get());
        }

        return $region;
    }

    /**
     * Minimal {slug, updated_at} rows for active regions, for the sitemap.
     */
    public function publicSitemapEntries(): Collection
    {
        return $this->visibilityScope($this->model->newQuery())->get(['slug', 'updated_at']);
    }

    public function countActive(): int
    {
        return $this->visibilityScope($this->model->newQuery())->count();
    }

    /**
     * Every region with its contact-click count (zero included), for the
     * statistics dashboard's per-region spread.
     */
    public function contactCountsByRegion(): Collection
    {
        return $this->model->newQuery()
            ->leftJoin('product_interactions as pi', function (\Illuminate\Database\Query\JoinClause $join) {
                $join->on('pi.region_id', '=', 'regions.id')
                    ->where('pi.type', '=', \Modules\Product\Models\ProductInteraction::TYPE_CONTACT);
            })
            ->groupBy('regions.id', 'regions.name')
            ->orderBy('regions.name')
            ->selectRaw('regions.id as region_id, regions.name as region, COUNT(pi.id) as aggregate')
            ->get();
    }
}
