<?php

namespace Modules\Farmer\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class FarmerRepository extends BaseRepository implements FarmerRepositoryInterface
{
    public function __construct(Farmer $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return parent::query()->with(['media', 'region', 'village', 'farmerGroup', 'commodities', 'products.unit']);
    }

    protected function visibilityScope(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('region_id'),
            AllowedFilter::exact('village_id'),
            AllowedFilter::exact('farmer_group_id'),
            AllowedFilter::exact('is_active'),
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function allowedIncludes(): array
    {
        return ['region', 'village', 'farmerGroup', 'commodities', 'products'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'created_at'];
    }

    /**
     * Paginated list of active farmers for the public directory (/petani).
     * Lightweight eager loads: enough for a card, no full product payloads.
     */
    public function publicListForDirectory(?string $regionSlug = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return $this->visibilityScope(
            $this->model->newQuery()
                ->with([
                    'media',
                    'region:id,name,slug',
                    'farmerGroup:id,name',
                    'commodities:id,name,slug',
                ])
                ->withCount(['products as products_count' => fn (Builder $query) => $query->where('is_active', true)])
        )
            ->when($regionSlug, fn (Builder $query) => $query->whereHas('region', fn (Builder $region) => $region->where('slug', $regionSlug)))
            ->when($search, fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    /**
     * Active farmer resolved by slug, with the relations the public profile
     * page renders (location, group, commodities, and active products).
     */
    public function publicFindBySlugWithProducts(string $slug): ?Model
    {
        $farmer = $this->visibilityScope($this->model->newQuery())
            ->with(['media', 'region', 'village', 'farmerGroup', 'commodities'])
            ->where('slug', $slug)
            ->first();

        $farmer?->load(['products' => fn ($query) => $query
            ->where('is_active', true)
            ->with(['media', 'category', 'region'])
            ->latest()]);

        return $farmer;
    }

    /**
     * Farmers in a region not yet assigned to any group — candidates for the
     * admin "attach farmer" picker on a farmer group's edit page.
     */
    public function availableForGroup(int $regionId): Collection
    {
        return $this->model->newQuery()
            ->where('region_id', $regionId)
            ->whereNull('farmer_group_id')
            ->get();
    }

    /**
     * Minimal {slug, updated_at} rows for active farmers, for the sitemap.
     */
    public function publicSitemapEntries(): Collection
    {
        return $this->visibilityScope($this->model->newQuery())->get(['slug', 'updated_at']);
    }

    public function countActive(): int
    {
        return $this->visibilityScope($this->model->newQuery())->count();
    }
}
