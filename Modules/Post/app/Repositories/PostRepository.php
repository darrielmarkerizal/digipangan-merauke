<?php

namespace Modules\Post\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Modules\Post\Models\Post;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return parent::query()->with(['media', 'category', 'author']);
    }

    protected function visibilityScope(Builder $query): Builder
    {
        return $query->published();
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('title'),
            AllowedFilter::exact('post_category_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('author_id'),
            AllowedFilter::callback('category', fn (Builder $query, $value) => $query->whereHas(
                'category', fn (Builder $categoryQuery) => $categoryQuery->where('slug', $value)
            )),
        ];
    }

    protected function searchable(): array
    {
        return ['title', 'body'];
    }

    protected function allowedIncludes(): array
    {
        return ['category', 'author'];
    }

    protected function allowedSorts(): array
    {
        return ['title', 'status', 'published_at', 'created_at'];
    }
}
