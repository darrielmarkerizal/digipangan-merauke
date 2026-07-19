<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $with);
    }

    public function paginateFiltered(?int $perPage = null): LengthAwarePaginator
    {
        return $this->repository->paginateFiltered($perPage);
    }

    public function findOrFail(int|string $id, array $with = []): Model
    {
        return $this->repository->findOrFail($id, $with);
    }

    public function findBySlug(string $slug, array $with = []): Model
    {
        $model = $this->repository->findBy('slug', $slug, $with);

        abort_if(is_null($model), 404, 'Resource not found.');

        return $model;
    }

    public function create(array $data): Model
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(fn () => $this->repository->update($model, $data));
    }

    public function delete(Model $model): bool
    {
        return DB::transaction(fn () => $this->repository->delete($model));
    }
}
