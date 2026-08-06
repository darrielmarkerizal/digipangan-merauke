<?php

namespace Modules\Page\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface SiteSettingRepositoryInterface extends BaseRepositoryInterface
{
    public function allOrderedByKey(): Collection;

    public function upsert(string $key, string $value, string $type): void;
}
