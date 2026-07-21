<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Support\Filters\UniversalSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    protected int $defaultPerPage = 15;

    protected int $maxPerPage = 100;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    protected function allowedFilters(): array
    {
        return [];
    }

    protected function searchable(): array
    {
        return [];
    }

    protected function resolvedFilters(): array
    {
        $filters = $this->allowedFilters();

        if ($this->searchable() !== []) {
            $filters[] = AllowedFilter::custom('search', new UniversalSearch($this->searchable()));
        }

        return $filters;
    }

    protected function allowedSorts(): array
    {
        return [];
    }

    protected function allowedIncludes(): array
    {
        return [];
    }

    protected function defaultSort(): string
    {
        return '-created_at';
    }

    protected function promoteSearchParameter(): void
    {
        $request = request();

        if ($this->searchable() === [] || ! $request->filled('search')) {
            return;
        }

        $filter = (array) $request->input('filter', []);

        if (array_key_exists('search', $filter)) {
            return;
        }

        $filter['search'] = $request->input('search');
        $request->merge(['filter' => $filter]);
    }

    public function filtered(): QueryBuilder
    {
        $this->promoteSearchParameter();

        return QueryBuilder::for($this->query())
            ->allowedFilters(...$this->resolvedFilters())
            ->allowedSorts(...$this->allowedSorts())
            ->allowedIncludes(...$this->allowedIncludes())
            ->defaultSort($this->defaultSort());
    }

    public function paginateFiltered(?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? (int) request()->integer('per_page', $this->defaultPerPage);

        return $this->filtered()
            ->paginate(max(min($perPage, $this->maxPerPage), 1))
            ->appends(request()->query());
    }

    public function all(array $with = []): Collection
    {
        return $this->query()->with($with)->get();
    }

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->query()->with($with)->paginate($perPage);
    }

    public function find(int|string $id, array $with = []): ?Model
    {
        return $this->query()->with($with)->find($id);
    }

    public function findOrFail(int|string $id, array $with = []): Model
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        return $this->query()->with($with)->where($column, $value)->first();
    }

    public function findBySlug(string $slug, array $with = []): ?Model
    {
        return $this->findBy('slug', $slug, $with);
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }
}
