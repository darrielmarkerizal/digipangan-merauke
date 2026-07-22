<?php

namespace Modules\Region\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
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
            ->withCount(['villages', 'farmerGroups']);
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
}
