<?php

namespace Modules\Region\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface RegionRepositoryInterface extends BaseRepositoryInterface
{
    public function publicFindBySlugWithFeaturedProducts(string $slug): ?\Illuminate\Database\Eloquent\Model;

    public function publicSitemapEntries(): Collection;

    public function countActive(): int;

    public function contactCountsByRegion(): Collection;
}
