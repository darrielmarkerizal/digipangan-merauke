<?php

namespace Modules\Farmer\Services;

use App\Services\BaseService;
use Modules\Farmer\Repositories\Contracts\CommodityRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CommodityService extends BaseService
{
    public function __construct(CommodityRepositoryInterface $repository)
    {
        parent::__construct($repository);
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
