<?php

namespace Modules\Page\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Page\Http\Requests\StoreFaqRequest;
use Modules\Page\Http\Requests\UpdateFaqRequest;
use Modules\Page\Http\Resources\FaqResource;
use Modules\Page\Services\FaqService;

class FaqController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly FaqService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageAboutPage->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(FaqResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new FaqResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreFaqRequest $request): JsonResponse
    {
        return $this->successResponse(
            new FaqResource($this->service->create($request->validated())),
            'FAQ berhasil dibuat.',
            201
        );
    }

    public function update(UpdateFaqRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new FaqResource($this->service->update($model, $request->validated())),
            'FAQ berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'FAQ berhasil dihapus.');
    }
}
