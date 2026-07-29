<?php

namespace Modules\Region\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
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

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Village/Index',
            $this->service->paginateFiltered(),
            VillageResource::class,
            [],
            'villages'
        );
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/Village/Show', [
            'village' => (new VillageResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/Village/Edit', [
            'village' => (new VillageResource($this->service->findOrFail($id)))->resolve(),
            'regions' => $this->regionService->list(),
        ]);
    }

    public function update(UpdateVillageRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.village.index')
            ->with('success', 'Desa berhasil diperbarui.');
    }
}
