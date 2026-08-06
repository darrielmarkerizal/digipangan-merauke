<?php

namespace Modules\Page\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PartnerRepositoryInterface extends BaseRepositoryInterface
{
    public function publicActive(): Collection;
}
