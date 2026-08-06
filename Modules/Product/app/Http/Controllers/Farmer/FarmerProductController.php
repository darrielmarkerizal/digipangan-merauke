<?php

namespace Modules\Product\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Farmer\Models\Farmer;
use Modules\Product\Http\Requests\StoreFarmerProductRequest;
use Modules\Product\Http\Requests\UpdateFarmerProductRequest;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Product\Services\ProductCategoryService;
use Modules\Product\Services\ProductService;
use Modules\Product\Services\UnitService;

class FarmerProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service,
        private readonly ProductRepositoryInterface $products,
        private readonly ProductCategoryService $categoryService,
        private readonly UnitService $unitService,
    ) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Farmer/Product/Index',
            $this->products->paginateFilteredForFarmer($this->currentFarmer()->id),
            ProductResource::class,
            [],
            'products'
        );
    }

    public function create(): Response
    {
        return Inertia::render('Farmer/Product/Create', [
            'categories' => $this->categoryService->list(),
            'units' => $this->unitService->list(),
        ]);
    }

    public function store(StoreFarmerProductRequest $request): RedirectResponse
    {
        $farmer = $this->currentFarmer();

        $data = $request->validated();
        $data['farmer_id'] = $farmer->id;
        $data['region_id'] = $farmer->region_id;

        $this->service->create($data);

        return redirect()->route('farmer.dashboard.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id): Response
    {
        $product = $this->authorizeOwnedProduct($id);

        return Inertia::render('Farmer/Product/Edit', [
            'product' => (new ProductResource($product))->resolve(),
            'categories' => $this->categoryService->list(),
            'units' => $this->unitService->list(),
        ]);
    }

    public function update(UpdateFarmerProductRequest $request, int $id): RedirectResponse
    {
        $product = $this->authorizeOwnedProduct($id);

        $this->service->update($product, $request->validated());

        return redirect()->route('farmer.dashboard.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = $this->authorizeOwnedProduct($id);

        $this->service->delete($product);

        return redirect()->route('farmer.dashboard.product.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function currentFarmer(): Farmer
    {
        $farmer = auth()->user()->farmer;

        abort_if(! $farmer, 403, 'Profil petani tidak ditemukan untuk akun ini.');

        return $farmer;
    }

    private function authorizeOwnedProduct(int $id)
    {
        $product = $this->service->findOrFail($id);

        abort_if($product->farmer_id !== $this->currentFarmer()->id, 403, 'Produk ini bukan milik Anda.');

        return $product;
    }
}
