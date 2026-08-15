<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Farmer\Services\FarmerService;
use Modules\Product\Http\Requests\StoreProductCategoryRequest;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductCategoryRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Services\ProductCategoryService;
use Modules\Product\Services\ProductService;
use Modules\Product\Services\UnitService;
use Modules\Region\Services\RegionService;

class ProductAdminController extends Controller
{
    public function __construct(
        private readonly ProductService $service,
        private readonly ProductCategoryService $categoryService,
        private readonly UnitService $unitService,
        private readonly FarmerService $farmerService,
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
            'Admin/Product/Index',
            $paginator,
            ProductResource::class,
            [
                'categories' => $this->categoryService->list(),
                'regions'    => $regions,
            ]
        );
    }

    public function create(Request $request): Response
    {
        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $farmers = $isDistrictAdmin && $regionId
            ? $this->farmerService->listByRegion($regionId)
            : $this->farmerService->list();

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        return Inertia::render('Admin/Product/Create', [
            'categories' => $this->categoryService->list(),
            'units'      => $this->unitService->list(),
            'farmers'    => $farmers,
            'regions'    => $regions,
            'default_region_id' => $regionId,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if ($user?->isDistrictAdmin() && $user?->getAssignedRegionId()) {
            $data['region_id'] = $user->getAssignedRegionId();
        }

        $this->service->create($data);

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Request $request, int $id): Response
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        return Inertia::render('Admin/Product/Show', [
            'product' => (new ProductResource($model))->resolve(),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $user = $request->user();
        $isDistrictAdmin = $user?->isDistrictAdmin() ?? false;
        $regionId = $isDistrictAdmin ? $user?->getAssignedRegionId() : null;

        $farmers = $isDistrictAdmin && $regionId
            ? $this->farmerService->listByRegion($regionId)
            : $this->farmerService->list();

        $regions = $isDistrictAdmin && $user?->region
            ? collect([['id' => $user->region->id, 'name' => $user->region->name]])
            : $this->regionService->list();

        return Inertia::render('Admin/Product/Edit', [
            'product'    => (new ProductResource($model))->resolve(),
            'categories' => $this->categoryService->list(),
            'units'      => $this->unitService->list(),
            'farmers'    => $farmers,
            'regions'    => $regions,
            'default_region_id' => $regionId,
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $data = $request->validated();
        if ($request->user()?->isDistrictAdmin() && $request->user()?->getAssignedRegionId()) {
            $data['region_id'] = $request->user()->getAssignedRegionId();
        }

        $this->service->update($model, $data);

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->authorizeDistrictAccess($request->user(), $model->region_id);

        $this->service->delete($model);

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function authorizeDistrictAccess(?User $user, ?int $resourceRegionId): void
    {
        if ($user && $user->isDistrictAdmin()) {
            abort_if(
                (int) $resourceRegionId !== (int) $user->getAssignedRegionId(),
                403,
                'Akses ditolak: Anda hanya dapat mengelola produk pada distrik yang ditugaskan.'
            );
        }
    }
}
