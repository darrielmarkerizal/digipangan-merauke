<?php

namespace Modules\Post\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface;

class PostCategoryService extends BaseService
{
    public function __construct(PostCategoryRepositoryInterface $repository)
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
