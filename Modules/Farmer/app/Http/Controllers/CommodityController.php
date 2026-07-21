<?php

namespace Modules\Farmer\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Farmer\Http\Requests\StoreCommodityRequest;
use Modules\Farmer\Http\Requests\UpdateCommodityRequest;
use Modules\Farmer\Http\Resources\CommodityResource;
use Modules\Farmer\Services\CommodityService;

class CommodityController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly CommodityService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageMasterData->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(CommodityResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new CommodityResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreCommodityRequest $request): JsonResponse
    {
        return $this->successResponse(
            new CommodityResource($this->service->create($request->validated())),
            'Komoditas berhasil dibuat.',
            201
        );
    }

    public function update(UpdateCommodityRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new CommodityResource($this->service->update($model, $request->validated())),
            'Komoditas berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Komoditas berhasil dihapus.');
    }
}
