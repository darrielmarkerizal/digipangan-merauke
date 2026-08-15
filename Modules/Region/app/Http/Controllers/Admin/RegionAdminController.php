<?php

namespace Modules\Region\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Region\Http\Requests\UpdateRegionRequest;
use Modules\Region\Http\Resources\RegionResource;
use Modules\Region\Services\RegionService;

class RegionAdminController extends Controller
{
    public function __construct(private readonly RegionService $service) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $paginator = $isDistrictAdmin && $regionId
            ? $this->service->paginateFilteredForDistrict($regionId)
            : $this->service->paginateFiltered();

        return InertiaQuery::render(
            'Admin/Region/Index',
            $paginator,
            RegionResource::class,
            [],
            'regions'
        );
    }

    public function show(Request $request, int $id): Response
    {
        $this->authorizeDistrictAccess($request->user(), $id);

        return Inertia::render('Admin/Region/Show', [
            'region' => (new RegionResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $this->authorizeDistrictAccess($request->user(), $id);

        return Inertia::render('Admin/Region/Edit', [
            'region' => (new RegionResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function update(UpdateRegionRequest $request, int $id): RedirectResponse
    {
        $this->authorizeDistrictAccess($request->user(), $id);

        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.region.index')
            ->with('success', 'Wilayah berhasil diperbarui.');
    }

    private function authorizeDistrictAccess(?User $user, int $regionId): void
    {
        if ($user && $user->isDistrictAdmin()) {
            abort_if(
                $regionId !== (int) $user->getAssignedRegionId(),
                403,
                'Akses ditolak: Anda hanya dapat mengelola data pada distrik Anda.'
            );
        }
    }
}
