<?php

namespace Modules\Post\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Post\Models\PostCategory;
use Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class PostCategoryRepository extends BaseRepository implements PostCategoryRepositoryInterface
{
    public function __construct(PostCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Categories with their published-post count, for the public post
     * catalog's category filter chips.
     */
    public function withPublicPostsCount(): Collection
    {
        return $this->model->newQuery()
            ->withCount(['posts as posts_count' => fn (Builder $query) => $query->published()])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'created_at'];
    }
}
