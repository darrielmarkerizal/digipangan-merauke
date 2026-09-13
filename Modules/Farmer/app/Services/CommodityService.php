<?php

namespace Modules\Farmer\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Modules\Farmer\Repositories\Contracts\CommodityRepositoryInterface;

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
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                abort(409, 'Tidak dapat menghapus data karena masih memiliki relasi (sedang digunakan).');
            }
            throw $e;
        }
    }
}
