<?php

namespace Modules\Farmer\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Farmer\Http\Requests\AttachFarmerToGroupRequest;
use Modules\Farmer\Http\Requests\DetachFarmerFromGroupRequest;
use Modules\Farmer\Http\Requests\StoreFarmerGroupRequest;
use Modules\Farmer\Http\Requests\UpdateFarmerGroupRequest;
use Modules\Farmer\Http\Resources\FarmerGroupResource;
use Modules\Farmer\Http\Resources\FarmerResource;
use Modules\Farmer\Services\FarmerGroupService;
use Modules\Farmer\Services\FarmerService;
use Modules\Region\Services\RegionService;

class FarmerGroupAdminController extends Controller
{
    public function __construct(
        private readonly FarmerGroupService $service,
        private readonly RegionService $regionService,
        private readonly FarmerService $farmerService,
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
            'Admin/FarmerGroup/Index',
            $paginator,
            FarmerGroupResource::class,
            ['regions' => $regions],
            'farmerGroups'
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

        return Inertia::render('Admin/FarmerGroup/Create', [
            'regions' => $regions,
            'default_region_id' => $regionId,
        ]);
    }

    public function store(StoreFarmerGroupRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if ($user?->isDistrictAdmin() && $user?->getAssignedRegionId()) {
            $data['region_id'] = $user->getAssignedRegionId();
        }

        $this->service->create($data);

        return redirect()->route('admin.farmer-group.index')
            ->with('success', 'Kelompok tani berhasil ditambahkan.');
    }

    public function show(Request $request, int $id): Response
    {
        $group = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $group->region_id);

        return Inertia::render('Admin/FarmerGroup/Show', [
            'farmerGroup' => (new FarmerGroupResource($group))->resolve(),
            'members'     => FarmerResource::collection($group->farmers()->get())->resolve(),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $group = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $group->region_id);

        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        $availableFarmers = $this->farmerService->availableForGroup($group->region_id);

        return Inertia::render('Admin/FarmerGroup/Edit', [
            'farmerGroup'      => (new FarmerGroupResource($group))->resolve(),
            'regions'          => $regions,
            'members'          => FarmerResource::collection($group->farmers()->get())->resolve(),
            'availableFarmers' => FarmerResource::collection($availableFarmers)->resolve(),
            'default_region_id' => $regionId,
        ]);
    }

    public function update(UpdateFarmerGroupRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $data = $request->validated();
        if ($request->user()?->isDistrictAdmin() && $request->user()?->getAssignedRegionId()) {
            $data['region_id'] = $request->user()->getAssignedRegionId();
        }

        $this->service->update($model, $data);

        return redirect()->route('admin.farmer-group.index')
            ->with('success', 'Kelompok tani berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Kelompok tani berhasil dihapus.');
    }

    public function attachFarmer(AttachFarmerToGroupRequest $request, int $id): RedirectResponse
    {
        $group  = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $group->region_id);

        $farmer = $this->farmerService->findOrFail($request->validated('farmer_id'));

        if ($farmer->region_id !== $group->region_id) {
            abort(400, 'Petani tidak berada di distrik yang sama dengan kelompok tani ini.');
        }

        $this->farmerService->update($farmer, ['farmer_group_id' => $group->id]);

        return redirect()->back()
            ->with('success', 'Petani berhasil ditambahkan ke kelompok.');
    }

    public function detachFarmer(DetachFarmerFromGroupRequest $request, int $id): RedirectResponse
    {
        $group  = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $group->region_id);

        $farmer = $this->farmerService->findOrFail($request->validated('farmer_id'));

        if ($farmer->farmer_group_id !== $group->id) {
            abort(400, 'Petani tersebut bukan anggota kelompok ini.');
        }

        $this->farmerService->update($farmer, ['farmer_group_id' => null]);

        return redirect()->back()
            ->with('success', 'Petani berhasil dikeluarkan dari kelompok.');
    }

    private function authorizeDistrictAccess(?User $user, ?int $resourceRegionId): void
    {
        if ($user && $user->isDistrictAdmin()) {
            abort_if(
                (int) $resourceRegionId !== (int) $user->getAssignedRegionId(),
                403,
                'Akses ditolak: Anda hanya dapat mengelola kelompok tani pada distrik yang ditugaskan.'
            );
        }
    }
}
