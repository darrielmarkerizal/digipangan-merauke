<?php

namespace Modules\Post\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Post\Http\Requests\StorePostRequest;
use Modules\Post\Http\Requests\UpdatePostRequest;
use Modules\Post\Http\Resources\PostResource;
use Modules\Post\Services\PostService;

class PostController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly PostService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManagePosts->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(PostResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new PostResource($this->service->findOrFail($id))
        );
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        return $this->successResponse(
            new PostResource($this->service->create($request->validated())),
            'Berita berhasil dibuat.',
            201
        );
    }

    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new PostResource($this->service->update($model, $request->validated())),
            'Berita berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Berita berhasil dihapus.');
    }
}
