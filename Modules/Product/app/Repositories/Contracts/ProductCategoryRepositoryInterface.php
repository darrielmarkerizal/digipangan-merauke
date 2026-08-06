<?php

namespace Modules\Product\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface ProductCategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function orderedList(): Collection;
}
