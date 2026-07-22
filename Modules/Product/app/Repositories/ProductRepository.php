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
