<?php

namespace Modules\Region\Repositories;

use App\Repositories\BaseRepository;
use Modules\Region\Models\Village;
use Modules\Region\Repositories\Contracts\VillageRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class VillageRepository extends BaseRepository implements VillageRepositoryInterface
{
    public function __construct(Village $model)
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
