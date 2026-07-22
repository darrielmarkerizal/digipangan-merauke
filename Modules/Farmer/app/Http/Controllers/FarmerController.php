<?php

namespace Modules\Farmer\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Farmer\Http\Requests\StoreFarmerRequest;
use Modules\Farmer\Http\Requests\UpdateFarmerRequest;
use Modules\Farmer\Http\Resources\FarmerResource;
use Modules\Farmer\Services\FarmerService;

class FarmerController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly FarmerService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageFarmers->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(FarmerResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new FarmerResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreFarmerRequest $request): JsonResponse
    {
        return $this->successResponse(
            new FarmerResource($this->service->create($request->validated())),
            'Petani berhasil dibuat.',
            201
        );
    }

    public function update(UpdateFarmerRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new FarmerResource($this->service->update($model, $request->validated())),
            'Petani berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Petani berhasil dihapus.');
    }
}
