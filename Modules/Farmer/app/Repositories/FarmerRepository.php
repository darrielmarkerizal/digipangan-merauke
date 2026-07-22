<?php

namespace Modules\Farmer\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
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
        return parent::query()->with(['media', 'region', 'village', 'farmerGroup', 'commodities']);
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
}
