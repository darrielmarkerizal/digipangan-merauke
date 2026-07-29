<?php

namespace Modules\Farmer\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Farmer\Http\Requests\StoreFarmerGroupRequest;
use Modules\Farmer\Http\Requests\UpdateFarmerGroupRequest;
use Modules\Farmer\Http\Resources\FarmerGroupResource;
use Modules\Farmer\Http\Resources\FarmerResource;
use Modules\Farmer\Models\Farmer;
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

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/FarmerGroup/Index',
            $this->service->paginateFiltered(),
            FarmerGroupResource::class,
            ['regions' => $this->regionService->list()],
            'farmerGroups'
        );
    }

    public function create(): Response
    {
        return Inertia::render('Admin/FarmerGroup/Create', [
            'regions' => $this->regionService->list(),
        ]);
    }

    public function store(StoreFarmerGroupRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.farmer-group.index')
            ->with('success', 'Kelompok tani berhasil ditambahkan.');
    }

    public function show(int $id): Response
    {
        $group = $this->service->findOrFail($id);

        return Inertia::render('Admin/FarmerGroup/Show', [
            'farmerGroup' => (new FarmerGroupResource($group))->resolve(),
            'members'     => FarmerResource::collection($group->farmers()->get())->resolve(),
        ]);
    }

    public function edit(int $id): Response
    {
        $group           = $this->service->findOrFail($id);
        $availableFarmers = Farmer::where('region_id', $group->region_id)
            ->whereNull('farmer_group_id')
            ->get();

        return Inertia::render('Admin/FarmerGroup/Edit', [
            'farmerGroup'     => (new FarmerGroupResource($group))->resolve(),
            'regions'         => $this->regionService->list(),
            'members'         => FarmerResource::collection($group->farmers()->get())->resolve(),
            'availableFarmers' => FarmerResource::collection($availableFarmers)->resolve(),
        ]);
    }

    public function update(UpdateFarmerGroupRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.farmer-group.index')
            ->with('success', 'Kelompok tani berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Kelompok tani berhasil dihapus.');
    }

    public function attachFarmer(Request $request, int $id): RedirectResponse
    {
        $request->validate(['farmer_id' => 'required|exists:farmers,id']);

        $group  = $this->service->findOrFail($id);
        $farmer = $this->farmerService->findOrFail($request->farmer_id);

        if ($farmer->region_id !== $group->region_id) {
            abort(400, 'Petani tidak berada di distrik yang sama dengan kelompok tani ini.');
        }

        $farmer->update(['farmer_group_id' => $group->id]);

        return redirect()->back()
            ->with('success', 'Petani berhasil ditambahkan ke kelompok.');
    }

    public function detachFarmer(Request $request, int $id): RedirectResponse
    {
        $request->validate(['farmer_id' => 'required|exists:farmers,id']);

        $group  = $this->service->findOrFail($id);
        $farmer = $this->farmerService->findOrFail($request->farmer_id);

        if ($farmer->farmer_group_id !== $group->id) {
            abort(400, 'Petani tersebut bukan anggota kelompok ini.');
        }

        $farmer->update(['farmer_group_id' => null]);

        return redirect()->back()
            ->with('success', 'Petani berhasil dikeluarkan dari kelompok.');
    }
}
