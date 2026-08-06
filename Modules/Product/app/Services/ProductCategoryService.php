<?php

namespace Modules\Product\Services;

use App\Services\BaseService;
use Modules\Product\Repositories\Contracts\ProductCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryService extends BaseService
{
    public function __construct(private readonly ProductCategoryRepositoryInterface $categories)
    {
        parent::__construct($categories);
    }

    public function orderedList(): Collection
    {
        return $this->categories->orderedList();
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
