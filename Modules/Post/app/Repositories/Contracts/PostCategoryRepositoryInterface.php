<?php

namespace Modules\Post\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PostCategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function withPublicPostsCount(): Collection;
}
