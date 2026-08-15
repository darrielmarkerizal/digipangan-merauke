<?php

namespace Modules\Region\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Region\Http\Requests\UpdateVillageRequest;
use Modules\Region\Http\Resources\VillageResource;
use Modules\Region\Services\RegionService;
use Modules\Region\Services\VillageService;

class VillageAdminController extends Controller
{
    public function __construct(
        private readonly VillageService $service,
        private readonly RegionService $regionService,
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $paginator = $isDistrictAdmin && $regionId
            ? $this->service->paginateFilteredForDistrict($regionId)
            : $this->service->paginateFiltered();

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        return InertiaQuery::render(
            'Admin/Village/Index',
            $paginator,
            VillageResource::class,
            [
                'regions' => $regions,
            ],
            'villages'
        );
    }

    public function show(Request $request, int $id): Response
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        return Inertia::render('Admin/Village/Show', [
            'village' => (new VillageResource($model))->resolve(),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        return Inertia::render('Admin/Village/Edit', [
            'village' => (new VillageResource($model))->resolve(),
            'regions' => $regions,
        ]);
    }

    public function update(UpdateVillageRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $this->service->update($model, $request->validated());

        return redirect()->route('admin.village.index')
            ->with('success', 'Desa berhasil diperbarui.');
    }

    private function authorizeDistrictAccess(?User $user, ?int $resourceRegionId): void
    {
        if ($user && $user->isDistrictAdmin()) {
            abort_if(
                (int) $resourceRegionId !== (int) $user->getAssignedRegionId(),
                403,
                'Akses ditolak: Anda hanya dapat mengelola data desa pada distrik yang ditugaskan.'
            );
        }
    }
}
