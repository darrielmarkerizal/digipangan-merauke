<?php

namespace Modules\Region\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RegionRepositoryInterface extends BaseRepositoryInterface
{
    public function publicFindBySlugWithFeaturedProducts(string $slug): ?Model;

    public function publicSitemapEntries(): Collection;

    public function countActive(): int;

    public function contactCountsByRegion(): Collection;
}
