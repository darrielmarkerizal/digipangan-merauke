<?php

namespace Modules\Farmer\Repositories;

use App\Repositories\BaseRepository;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Farmer\Repositories\Contracts\FarmerGroupRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class FarmerGroupRepository extends BaseRepository implements FarmerGroupRepositoryInterface
{
    public function __construct(FarmerGroup $model)
    {
        parent::__construct($model);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('region_id'),
            AllowedFilter::exact('village_id'),
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function allowedIncludes(): array
    {
        return ['region', 'village'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'created_at'];
    }

    public function countAll(): int
    {
        return $this->model->newQuery()->count();
    }
}
