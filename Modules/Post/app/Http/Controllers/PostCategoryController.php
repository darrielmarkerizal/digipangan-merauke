<?php

namespace Modules\Post\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Post\Http\Requests\StorePostCategoryRequest;
use Modules\Post\Http\Requests\UpdatePostCategoryRequest;
use Modules\Post\Http\Resources\PostCategoryResource;
use Modules\Post\Services\PostCategoryService;

class PostCategoryController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly PostCategoryService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageMasterData->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(PostCategoryResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new PostCategoryResource($this->service->findOrFail($id))
        );
    }

    public function store(StorePostCategoryRequest $request): JsonResponse
    {
        return $this->successResponse(
            new PostCategoryResource($this->service->create($request->validated())),
            'Kategori Berita berhasil dibuat.',
            201
        );
    }

    public function update(UpdatePostCategoryRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new PostCategoryResource($this->service->update($model, $request->validated())),
            'Kategori Berita berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Kategori Berita berhasil dihapus.');
    }
}
