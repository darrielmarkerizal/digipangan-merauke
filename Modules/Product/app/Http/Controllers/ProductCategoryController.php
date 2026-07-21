<?php

namespace Modules\Product\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Product\Http\Requests\StoreProductCategoryRequest;
use Modules\Product\Http\Requests\UpdateProductCategoryRequest;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Services\ProductCategoryService;

class ProductCategoryController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly ProductCategoryService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageMasterData->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(ProductCategoryResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new ProductCategoryResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        return $this->successResponse(
            new ProductCategoryResource($this->service->create($request->validated())),
            'Kategori Produk berhasil dibuat.',
            201
        );
    }

    public function update(UpdateProductCategoryRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new ProductCategoryResource($this->service->update($model, $request->validated())),
            'Kategori Produk berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Kategori Produk berhasil dihapus.');
    }
}
