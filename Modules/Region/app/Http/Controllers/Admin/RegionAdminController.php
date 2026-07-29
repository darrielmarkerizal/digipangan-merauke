<?php

namespace Modules\Region\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Region\Http\Requests\UpdateRegionRequest;
use Modules\Region\Http\Resources\RegionResource;
use Modules\Region\Services\RegionService;

class RegionAdminController extends Controller
{
    public function __construct(private readonly RegionService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Region/Index',
            $this->service->paginateFiltered(),
            RegionResource::class,
            [],
            'regions'
        );
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/Region/Show', [
            'region' => (new RegionResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/Region/Edit', [
            'region' => (new RegionResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function update(UpdateRegionRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.region.index')
            ->with('success', 'Wilayah berhasil diperbarui.');
    }
}
