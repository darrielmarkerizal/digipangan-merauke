<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

interface BaseRepositoryInterface
{
    public function all(array $with = []): Collection;

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator;

    public function filtered(): QueryBuilder;

    public function paginateFiltered(?int $perPage = null): LengthAwarePaginator;

    public function find(int|string $id, array $with = []): ?Model;

    public function findOrFail(int|string $id, array $with = []): Model;

    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    public function create(array $attributes): Model;

    public function update(Model $model, array $attributes): Model;

    public function delete(Model $model): bool;

    public function query();
}
