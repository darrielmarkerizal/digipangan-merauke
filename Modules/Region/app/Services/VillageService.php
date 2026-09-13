<?php

namespace Modules\Region\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Modules\Region\Repositories\Contracts\VillageRepositoryInterface;

class VillageService extends BaseService
{
    public function __construct(VillageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function listByRegion(int $regionId): Collection
    {
        return $this->repository->listByRegion($regionId);
    }

    public function delete(Model $model): bool
    {
        try {
            return parent::delete($model);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                abort(409, 'Tidak dapat menghapus data karena masih memiliki relasi (sedang digunakan).');
            }
            throw $e;
        }
    }
}
