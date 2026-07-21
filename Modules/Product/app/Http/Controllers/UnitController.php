<?php

namespace Modules\Product\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Product\Http\Requests\StoreUnitRequest;
use Modules\Product\Http\Requests\UpdateUnitRequest;
use Modules\Product\Http\Resources\UnitResource;
use Modules\Product\Services\UnitService;

class UnitController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly UnitService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageMasterData->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(UnitResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new UnitResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        return $this->successResponse(
            new UnitResource($this->service->create($request->validated())),
            'Satuan berhasil dibuat.',
            201
        );
    }

    public function update(UpdateUnitRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new UnitResource($this->service->update($model, $request->validated())),
            'Satuan berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Satuan berhasil dihapus.');
    }
}
