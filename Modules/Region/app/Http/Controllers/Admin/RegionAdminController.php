<?php

namespace Modules\Region\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\InertiaQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Region\Http\Requests\StoreRegionRequest;
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

        abort_if(
            $isDistrictAdmin && $regionId === null,
            403,
            'Akun admin distrik belum memiliki distrik yang ditugaskan.'
        );

        $paginator = $regionId !== null
            ? $this->service->paginateFilteredForDistrict($regionId)
            : $this->service->paginateFiltered();

        return InertiaQuery::render(
            'Admin/Region/Index',
            $paginator,
            RegionResource::class,
            ['can_create' => $request->user()?->isSuperAdmin() ?? false],
            'regions'
        );
    }

    public function create(Request $request): Response
    {
        $this->authorizeSuperAdmin($request->user());

        return Inertia::render('Admin/Region/Create');
    }

    public function store(StoreRegionRequest $request): RedirectResponse
    {
        $this->authorizeSuperAdmin($request->user());

        $region = $this->service->create($request->validated());

        return redirect()->route('admin.region.show', $region->getKey())
            ->with('success', 'Wilayah berhasil ditambahkan.');
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
        if ($user?->isDistrictAdmin()) {
            abort_if(
                $user->getAssignedRegionId() === null
                    || $regionId !== $user->getAssignedRegionId(),
                403,
                'Akses ditolak: Anda hanya dapat mengelola data pada distrik Anda.'
            );
        }
    }

    private function authorizeSuperAdmin(?User $user): void
    {
        abort_unless($user?->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat menambahkan wilayah.');
    }
}
