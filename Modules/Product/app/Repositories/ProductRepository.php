<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
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
}
