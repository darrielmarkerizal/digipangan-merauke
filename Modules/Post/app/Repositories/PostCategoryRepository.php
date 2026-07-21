<?php

namespace Modules\Post\Repositories;

use App\Repositories\BaseRepository;
use Modules\Post\Models\PostCategory;
use Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class PostCategoryRepository extends BaseRepository implements PostCategoryRepositoryInterface
{
    public function __construct(PostCategory $model)
    {
        parent::__construct($model);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial("name"),
        ];
    }

    protected function allowedSorts(): array
    {
        return ["name", "created_at"];
    }
}
