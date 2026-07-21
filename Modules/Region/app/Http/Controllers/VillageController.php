<?php

namespace Modules\Region\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Region\Http\Requests\StoreVillageRequest;
use Modules\Region\Http\Requests\UpdateVillageRequest;
use Modules\Region\Http\Resources\VillageResource;
use Modules\Region\Services\VillageService;

class VillageController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly VillageService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageMasterData->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(VillageResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new VillageResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreVillageRequest $request): JsonResponse
    {
        return $this->successResponse(
            new VillageResource($this->service->create($request->validated())),
            'Desa berhasil dibuat.',
            201
        );
    }

    public function update(UpdateVillageRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new VillageResource($this->service->update($model, $request->validated())),
            'Desa berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Desa berhasil dihapus.');
    }
}
