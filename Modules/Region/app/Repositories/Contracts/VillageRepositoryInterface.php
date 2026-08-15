<?php

namespace Modules\Region\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

use Illuminate\Database\Eloquent\Collection;

interface VillageRepositoryInterface extends BaseRepositoryInterface
{
    public function countByRegion(int $regionId): int;

    public function listByRegion(int $regionId): Collection;
}
