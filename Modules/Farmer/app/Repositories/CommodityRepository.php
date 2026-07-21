<?php

namespace Modules\Farmer\Repositories;

use App\Repositories\BaseRepository;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Repositories\Contracts\CommodityRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class CommodityRepository extends BaseRepository implements CommodityRepositoryInterface
{
    public function __construct(Commodity $model)
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
