<?php

namespace Modules\Region\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->ensureDistrictAssignment($user);
        $regionId = $user?->isDistrictAdmin() ? $user->getAssignedRegionId() : null;

        $paginator = $regionId !== null
            ? $this->service->paginateFilteredForDistrict($regionId)
            : $this->service->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(RegionResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $this->authorizeDistrictAccess($request->user(), $id);

        return $this->successResponse(
            new RegionResource($this->service->findOrFail($id))
        );
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        $this->ensureGlobalManagement($request->user());

        return $this->successResponse(
            new RegionResource($this->service->create($request->validated())),
            'Wilayah berhasil dibuat.',
            201
        );
    }

    public function update(UpdateRegionRequest $request, int $id): JsonResponse
    {
        $this->authorizeDistrictAccess($request->user(), $id);
        $model = $this->service->findOrFail($id);

        return $this->successResponse(
            new RegionResource($this->service->update($model, $request->validated())),
            'Wilayah berhasil diperbarui.'
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->ensureGlobalManagement($request->user());
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return $this->successResponse(null, 'Wilayah berhasil dihapus.');
    }

    private function authorizeDistrictAccess(?User $user, int $regionId): void
    {
        if ($user?->isDistrictAdmin()) {
            abort_if(
                $user->getAssignedRegionId() === null
                    || $user->getAssignedRegionId() !== $regionId,
                403,
                'Akses ditolak: Anda hanya dapat mengelola data pada distrik Anda.'
            );
        }
    }

    private function ensureDistrictAssignment(?User $user): void
    {
        abort_if(
            $user?->isDistrictAdmin() && $user->getAssignedRegionId() === null,
            403,
            'Akun admin distrik belum memiliki distrik yang ditugaskan.'
        );
    }

    private function ensureGlobalManagement(?User $user): void
    {
        abort_if(
            $user?->isDistrictAdmin(),
            403,
            'Akses ditolak: Hanya Super Admin yang dapat melakukan tindakan ini.'
        );
    }
}
