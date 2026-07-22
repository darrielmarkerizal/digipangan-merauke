<?php

namespace Modules\Product\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Services\ProductService;

class ProductController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly ProductService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageProducts->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(ProductResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new ProductResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->successResponse(
            new ProductResource($this->service->create($request->validated())),
            'Produk berhasil dibuat.',
            201
        );
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new ProductResource($this->service->update($model, $request->validated())),
            'Produk berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Produk berhasil dihapus.');
    }
}
