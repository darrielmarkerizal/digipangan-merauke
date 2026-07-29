<?php

namespace Modules\Farmer\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
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

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Farmer/Index',
            $this->service->paginateFiltered(),
            FarmerResource::class,
            [
                'regions'      => $this->regionService->list(),
                'villages'     => $this->villageService->list(),
                'farmerGroups' => $this->farmerGroupService->list(),
            ],
            'farmers'
        );
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Farmer/Create', [
            'regions'      => $this->regionService->list(),
            'villages'     => $this->villageService->list(),
            'farmerGroups' => $this->farmerGroupService->list(),
            'commodities'  => $this->commodityService->list(),
        ]);
    }

    public function store(StoreFarmerRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Petani berhasil ditambahkan.');
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/Farmer/Show', [
            'farmer' => (new FarmerResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/Farmer/Edit', [
            'farmer'       => (new FarmerResource($this->service->findOrFail($id)))->resolve(),
            'regions'      => $this->regionService->list(),
            'villages'     => $this->villageService->list(),
            'farmerGroups' => $this->farmerGroupService->list(),
            'commodities'  => $this->commodityService->list(),
        ]);
    }

    public function update(UpdateFarmerRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Petani berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Petani berhasil dihapus.');
    }
}
