<?php

namespace Modules\Farmer\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Farmer\Http\Requests\StoreFarmerRequest;
use Modules\Farmer\Http\Requests\UpdateFarmerRequest;
use Modules\Farmer\Http\Resources\FarmerResource;
use Modules\Farmer\Services\CommodityService;
use Modules\Farmer\Services\FarmerGroupService;
use Modules\Farmer\Services\FarmerService;
use Modules\Region\Services\RegionService;
use Modules\Region\Services\VillageService;

class FarmerAdminController extends Controller
{
    public function __construct(
        private readonly FarmerService $service,
        private readonly RegionService $regionService,
        private readonly VillageService $villageService,
        private readonly FarmerGroupService $farmerGroupService,
        private readonly CommodityService $commodityService,
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

        $villages = $isDistrictAdmin && $regionId
            ? $this->villageService->listByRegion($regionId)
            : $this->villageService->list();

        $farmerGroups = $isDistrictAdmin && $regionId
            ? $this->farmerGroupService->listByRegion($regionId)
            : $this->farmerGroupService->list();

        return InertiaQuery::render(
            'Admin/Farmer/Index',
            $paginator,
            FarmerResource::class,
            [
                'regions'      => $regions,
                'villages'     => $villages,
                'farmerGroups' => $farmerGroups,
            ],
            'farmers'
        );
    }

    public function create(Request $request): Response
    {
        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        $villages = $isDistrictAdmin && $regionId
            ? $this->villageService->listByRegion($regionId)
            : $this->villageService->list();

        $farmerGroups = $isDistrictAdmin && $regionId
            ? $this->farmerGroupService->listByRegion($regionId)
            : $this->farmerGroupService->list();

        return Inertia::render('Admin/Farmer/Create', [
            'regions'      => $regions,
            'villages'     => $villages,
            'farmerGroups' => $farmerGroups,
            'commodities'  => $this->commodityService->list(),
            'default_region_id' => $regionId,
        ]);
    }

    public function store(StoreFarmerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if ($user?->isDistrictAdmin() && $user?->getAssignedRegionId()) {
            $data['region_id'] = $user->getAssignedRegionId();
        }

        $this->service->create($data);

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Petani berhasil ditambahkan.');
    }

    public function show(Request $request, int $id): Response
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        return Inertia::render('Admin/Farmer/Show', [
            'farmer' => (new FarmerResource($model))->resolve(),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        $villages = $isDistrictAdmin && $regionId
            ? $this->villageService->listByRegion($regionId)
            : $this->villageService->list();

        $farmerGroups = $isDistrictAdmin && $regionId
            ? $this->farmerGroupService->listByRegion($regionId)
            : $this->farmerGroupService->list();

        return Inertia::render('Admin/Farmer/Edit', [
            'farmer'       => (new FarmerResource($model))->resolve(),
            'regions'      => $regions,
            'villages'     => $villages,
            'farmerGroups' => $farmerGroups,
            'commodities'  => $this->commodityService->list(),
            'default_region_id' => $regionId,
        ]);
    }

    public function update(UpdateFarmerRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $data = $request->validated();
        if ($request->user()?->isDistrictAdmin() && $request->user()?->getAssignedRegionId()) {
            $data['region_id'] = $request->user()->getAssignedRegionId();
        }

        $this->service->update($model, $data);

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Petani berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Petani berhasil dihapus.');
    }

    private function authorizeDistrictAccess(?User $user, ?int $resourceRegionId): void
    {
        if ($user && $user->isDistrictAdmin()) {
            abort_if(
                (int) $resourceRegionId !== (int) $user->getAssignedRegionId(),
                403,
                'Akses ditolak: Anda hanya dapat mengelola data petani pada distrik yang ditugaskan.'
            );
        }
    }
}
