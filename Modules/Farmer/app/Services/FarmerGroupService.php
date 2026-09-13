<?php

namespace Modules\Farmer\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Modules\Farmer\Repositories\Contracts\FarmerGroupRepositoryInterface;

class FarmerGroupService extends BaseService
{
    public function __construct(FarmerGroupRepositoryInterface $repository)
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
