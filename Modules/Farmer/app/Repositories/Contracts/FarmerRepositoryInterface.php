<?php

namespace Modules\Farmer\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface FarmerRepositoryInterface extends BaseRepositoryInterface
{
    public function publicListForDirectory(?string $regionSlug = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator;

    public function publicFindBySlugWithProducts(string $slug): ?Model;

    public function availableForGroup(int $regionId): Collection;

    public function publicSitemapEntries(): Collection;

    public function countActive(): int;
}
