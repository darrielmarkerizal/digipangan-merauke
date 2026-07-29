<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Product\Http\Requests\StoreProductCategoryRequest;
use Modules\Product\Http\Requests\UpdateProductCategoryRequest;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Services\ProductCategoryService;

class CategoryAdminController extends Controller
{
    public function __construct(private readonly ProductCategoryService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Category/Index',
            $this->service->paginateFiltered(),
            ProductCategoryResource::class,
            [],
            'categories'
        );
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->back()
            ->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function update(UpdateProductCategoryRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->back()
            ->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Kategori produk berhasil dihapus.');
    }
}
