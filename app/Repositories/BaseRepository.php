<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Support\Filters\UniversalSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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

    protected function promoteSearchParameter(): \Illuminate\Http\Request
    {
        $request = request();

        $filter = (array) $request->input('filter', []);

        $searchVal = $request->input('search') ?? $request->input('q');
        if ($this->searchable() !== [] && $searchVal && ! array_key_exists('search', $filter)) {
            $filter['search'] = $searchVal;
        }

        $allowedFilterNames = array_map(function ($allowedFilter) {
            return $allowedFilter instanceof AllowedFilter 
                ? $allowedFilter->getName() 
                : $allowedFilter;
        }, $this->allowedFilters());

        if ($request->filled('kategori') && ! array_key_exists('category', $filter) && in_array('category', $allowedFilterNames, true)) {
            $filter['category'] = $request->input('kategori');
        }

        if ($request->filled('region') && ! array_key_exists('region', $filter) && in_array('region', $allowedFilterNames, true)) {
            $filter['region'] = $request->input('region');
        }

        $customRequest = $request->duplicate();
        $customRequest->merge(['filter' => $filter]);

        return $customRequest;
    }

    public function filtered(): QueryBuilder
    {
        return $this->buildFiltered($this->query());
    }

    public function publicFiltered(): QueryBuilder
    {
        return $this->buildFiltered($this->visibilityScope($this->query()));
    }

    protected function buildFiltered(Builder $base): QueryBuilder
    {
        $customRequest = $this->promoteSearchParameter();

        return QueryBuilder::for($base, $customRequest)
            ->allowedFilters(...$this->resolvedFilters())
            ->allowedSorts(...$this->allowedSorts())
            ->allowedIncludes(...$this->allowedIncludes())
            ->defaultSort($this->defaultSort());
    }

    /**
     * Visibility constraint for public (guest) reads. No-op by default; entity
     * repositories override it to expose only active/published records.
     */
    protected function visibilityScope(Builder $query): Builder
    {
        return $query;
    }

    public function paginateFiltered(?int $perPage = null): LengthAwarePaginator
    {
        return $this->paginateQuery($this->filtered(), $perPage);
    }

    public function paginateFilteredForDistrict(int $regionId, ?int $perPage = null, string $column = 'region_id'): LengthAwarePaginator
    {
        $baseQuery = $this->query();
        if ($this->model instanceof \Modules\Region\Models\Region) {
            $baseQuery->where('id', $regionId);
        } else {
            $baseQuery->where($this->model->qualifyColumn($column), $regionId);
        }

        return $this->paginateQuery(
            $this->buildFiltered($baseQuery),
            $perPage
        );
    }

    public function publicPaginateFiltered(?int $perPage = null): LengthAwarePaginator
    {
        return $this->paginateQuery($this->publicFiltered(), $perPage);
    }

    protected function paginateQuery(QueryBuilder $query, ?int $perPage): LengthAwarePaginator
    {
        $perPage = $perPage ?? (int) request()->integer('per_page', $this->defaultPerPage);

        return $query
            ->paginate(max(min($perPage, $this->maxPerPage), 1))
            ->appends(request()->query());
    }

    public function publicFindBySlug(string $slug, array $with = []): ?Model
    {
        return $this->visibilityScope($this->query())->with($with)->where('slug', $slug)->first();
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
