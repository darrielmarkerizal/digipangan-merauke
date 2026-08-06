<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Repositories\Contracts\ProductCategoryRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    public function __construct(ProductCategory $model)
    {
        parent::__construct($model);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'name';
    }

    /**
     * Categories ordered for public display (homepage, catalog filters).
     */
    public function orderedList(): Collection
    {
        return $this->model->newQuery()->orderBy('sort_order')->get();
    }
}
