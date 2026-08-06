<?php

namespace Modules\Page\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface FaqRepositoryInterface extends BaseRepositoryInterface
{
    public function publicActive(): Collection;

    public function countActive(): int;
}
