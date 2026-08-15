<?php

namespace Modules\Farmer\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

use Illuminate\Database\Eloquent\Collection;

interface FarmerGroupRepositoryInterface extends BaseRepositoryInterface
{
    public function countAll(?int $regionId = null): int;

    public function listByRegion(int $regionId): Collection;
}
