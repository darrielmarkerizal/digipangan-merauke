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

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::query()->with('region');
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('region_id'),
            AllowedFilter::exact('is_active'),
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function allowedIncludes(): array
    {
        return ['region'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'created_at'];
    }

    public function countByRegion(int $regionId): int
    {
        return $this->model->newQuery()->where('region_id', $regionId)->count();
    }

    public function listByRegion(int $regionId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()
            ->where('region_id', $regionId)
            ->get(['id', 'name', 'slug', 'region_id']);
    }
}
