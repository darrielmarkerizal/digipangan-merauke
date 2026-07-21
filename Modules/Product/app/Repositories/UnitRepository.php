<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Modules\Product\Models\Unit;
use Modules\Product\Repositories\Contracts\UnitRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class UnitRepository extends BaseRepository implements UnitRepositoryInterface
{
    public function __construct(Unit $model)
    {
        parent::__construct($model);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial("name"),
        ];
    }

    protected function allowedSorts(): array
    {
        return ["name", "created_at"];
    }
}
