<?php

namespace Modules\Farmer\Services;

use App\Services\BaseService;
use Modules\Farmer\Repositories\Contracts\FarmerGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class FarmerGroupService extends BaseService
{
    public function __construct(FarmerGroupRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function listByRegion(int $regionId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->listByRegion($regionId);
    }

    public function delete(Model $model): bool
    {
        try {
            return parent::delete($model);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === "23000") {
                abort(409, "Tidak dapat menghapus data karena masih memiliki relasi (sedang digunakan).");
            }
            throw $e;
        }
    }
}
