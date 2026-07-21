<?php

namespace Modules\Farmer\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Farmer\Http\Requests\StoreFarmerGroupRequest;
use Modules\Farmer\Http\Requests\UpdateFarmerGroupRequest;
use Modules\Farmer\Http\Resources\FarmerGroupResource;
use Modules\Farmer\Services\FarmerGroupService;

class FarmerGroupController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly FarmerGroupService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageMasterData->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(FarmerGroupResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new FarmerGroupResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreFarmerGroupRequest $request): JsonResponse
    {
        return $this->successResponse(
            new FarmerGroupResource($this->service->create($request->validated())),
            'Kelompok Tani berhasil dibuat.',
            201
        );
    }

    public function update(UpdateFarmerGroupRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new FarmerGroupResource($this->service->update($model, $request->validated())),
            'Kelompok Tani berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Kelompok Tani berhasil dihapus.');
    }
}
