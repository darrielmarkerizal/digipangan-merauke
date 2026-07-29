<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
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

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Product/Index',
            $this->service->paginateFiltered(),
            ProductResource::class,
            [
                'categories' => $this->categoryService->list(),
                'regions'    => $this->regionService->list(),
            ]
        );
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Product/Create', [
            'categories' => $this->categoryService->list(),
            'units'      => $this->unitService->list(),
            'farmers'    => $this->farmerService->list(),
            'regions'    => $this->regionService->list(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/Product/Show', [
            'product' => (new ProductResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/Product/Edit', [
            'product'    => (new ProductResource($this->service->findOrFail($id)))->resolve(),
            'categories' => $this->categoryService->list(),
            'units'      => $this->unitService->list(),
            'farmers'    => $this->farmerService->list(),
            'regions'    => $this->regionService->list(),
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
