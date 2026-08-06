<?php

namespace Modules\Post\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function publicRelated(int $excludeId, ?int $categoryId, int $limit = 3): Collection;

    public function publicSitemapEntries(): Collection;

    public function countPublished(): int;
}
