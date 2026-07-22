<?php

namespace Modules\Page\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Page\Http\Requests\StorePartnerRequest;
use Modules\Page\Http\Requests\UpdatePartnerRequest;
use Modules\Page\Http\Resources\PartnerResource;
use Modules\Page\Services\PartnerService;

class PartnerController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly PartnerService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageAboutPage->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(PartnerResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new PartnerResource($this->service->findOrFail($id))
        );
    }

    public function store(StorePartnerRequest $request): JsonResponse
    {
        return $this->successResponse(
            new PartnerResource($this->service->create($request->validated())),
            'Mitra berhasil dibuat.',
            201
        );
    }

    public function update(UpdatePartnerRequest $request, int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new PartnerResource($this->service->update($model, $request->validated())),
            'Mitra berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Mitra berhasil dihapus.');
    }
}
