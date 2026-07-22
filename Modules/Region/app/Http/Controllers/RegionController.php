<?php

namespace Modules\Region\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Region\Http\Requests\StoreRegionRequest;
use Modules\Region\Http\Requests\UpdateRegionRequest;
use Modules\Region\Http\Resources\RegionResource;
use Modules\Region\Services\RegionService;

class RegionController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly RegionService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageRegions->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(RegionResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new RegionResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        return $this->successResponse(
            new RegionResource($this->service->create($request->validated())),
            'Wilayah berhasil dibuat.',
            201
        );
    }

    public function update(UpdateRegionRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new RegionResource($this->service->update($model, $request->validated())),
            'Wilayah berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Wilayah berhasil dihapus.');
    }
}
