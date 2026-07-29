<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Home\Http\Controllers\Public\HomePageController;
use Modules\Region\Http\Resources\RegionResource;

Route::get('/', [HomePageController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', function (\Modules\User\Http\Requests\LoginRequest $request, \Modules\User\Services\AuthService $authService) {
        $authService->login($request, $request->credentials(), $request->remember());
        return redirect()->intended('/admin/dashboard')->with('success', 'Berhasil masuk ke Portal Admin DigiPangan Merauke.');
    })->name('login.store');
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    if ($request->hasSession()) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    return redirect()->route('login')->with('success', 'Berhasil keluar dari sesi admin.');
})->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard.index');
    Route::get('/produk', fn (\Modules\Product\Services\ProductService $service, \Modules\Product\Services\ProductCategoryService $catService, \Modules\Region\Services\RegionService $regionService) => \App\Support\InertiaQuery::render('Admin/Product/Index', $service->paginateFiltered(), \Modules\Product\Http\Resources\ProductResource::class, [
        'categories' => $catService->list(),
        'regions' => $regionService->list(),
    ]))->name('product.index');

    Route::get('/produk/tambah', fn (
        \Modules\Product\Services\ProductCategoryService $catService,
        \Modules\Product\Services\UnitService $unitService,
        \Modules\Farmer\Services\FarmerService $farmerService,
        \Modules\Region\Services\RegionService $regionService
    ) => Inertia::render('Admin/Product/Create', [
        'categories' => $catService->list(),
        'units' => $unitService->list(),
        'farmers' => $farmerService->list(),
        'regions' => $regionService->list(),
    ]))->name('product.create');

    Route::post('/produk', [\Modules\Product\Http\Controllers\ProductController::class, 'store'])->name('product.store');

    Route::get('/produk/{id}', fn (
        int $id,
        \Modules\Product\Services\ProductService $service
    ) => Inertia::render('Admin/Product/Show', [
        'product' => (new \Modules\Product\Http\Resources\ProductResource($service->findOrFail($id)))->resolve(),
    ]))->name('product.show');

    Route::get('/produk/{id}/edit', fn (
        int $id,
        \Modules\Product\Services\ProductService $service,
        \Modules\Product\Services\ProductCategoryService $catService,
        \Modules\Product\Services\UnitService $unitService,
        \Modules\Farmer\Services\FarmerService $farmerService,
        \Modules\Region\Services\RegionService $regionService
    ) => Inertia::render('Admin/Product/Edit', [
        'product' => (new \Modules\Product\Http\Resources\ProductResource($service->findOrFail($id)))->resolve(),
        'categories' => $catService->list(),
        'units' => $unitService->list(),
        'farmers' => $farmerService->list(),
        'regions' => $regionService->list(),
    ]))->name('product.edit');

    Route::put('/produk/{id}', [\Modules\Product\Http\Controllers\ProductController::class, 'update'])->name('product.update');
    Route::delete('/produk/{id}', [\Modules\Product\Http\Controllers\ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/kategori', fn (\Modules\Product\Services\ProductCategoryService $catService) => \App\Support\InertiaQuery::render('Admin/Category/Index', $catService->paginateFiltered(), \Modules\Product\Http\Resources\ProductCategoryResource::class, [], 'categories'))->name('category.index');
    Route::post('/kategori', function (\Modules\Product\Http\Requests\StoreProductCategoryRequest $request, \Modules\Product\Services\ProductCategoryService $service) {
        $service->create($request->validated());
        return redirect()->back()->with('success', 'Kategori produk berhasil ditambahkan.');
    })->name('category.store');
    Route::put('/kategori/{id}', function (\Modules\Product\Http\Requests\UpdateProductCategoryRequest $request, int $id, \Modules\Product\Services\ProductCategoryService $service) {
        $model = $service->findOrFail($id);
        $service->update($model, $request->validated());
        return redirect()->back()->with('success', 'Kategori produk berhasil diperbarui.');
    })->name('category.update');
    Route::delete('/kategori/{id}', function (int $id, \Modules\Product\Services\ProductCategoryService $service) {
        $model = $service->findOrFail($id);
        $service->delete($model);
        return redirect()->back()->with('success', 'Kategori produk berhasil dihapus.');
    })->name('category.destroy');
    Route::get('/satuan', fn (\Modules\Product\Services\UnitService $unitService) => \App\Support\InertiaQuery::render('Admin/Unit/Index', $unitService->paginateFiltered(), \Modules\Product\Http\Resources\UnitResource::class, [], 'units'))->name('unit.index');
    Route::post('/satuan', function (\Modules\Product\Http\Requests\StoreUnitRequest $request, \Modules\Product\Services\UnitService $service) {
        $service->create($request->validated());
        return redirect()->back()->with('success', 'Satuan berhasil ditambahkan.');
    })->name('unit.store');
    Route::put('/satuan/{id}', function (\Modules\Product\Http\Requests\UpdateUnitRequest $request, int $id, \Modules\Product\Services\UnitService $service) {
        $model = $service->findOrFail($id);
        $service->update($model, $request->validated());
        return redirect()->back()->with('success', 'Satuan berhasil diperbarui.');
    })->name('unit.update');
    Route::delete('/satuan/{id}', function (int $id, \Modules\Product\Services\UnitService $service) {
        $model = $service->findOrFail($id);
        $service->delete($model);
        return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
    })->name('unit.destroy');
    Route::get('/komoditas', fn (\Modules\Farmer\Services\CommodityService $commodityService) => \App\Support\InertiaQuery::render('Admin/Commodity/Index', $commodityService->paginateFiltered(), \Modules\Farmer\Http\Resources\CommodityResource::class, [], 'commodities'))->name('commodity.index');
    Route::post('/komoditas', function (\Modules\Farmer\Http\Requests\StoreCommodityRequest $request, \Modules\Farmer\Services\CommodityService $service) {
        $service->create($request->validated());
        return redirect()->back()->with('success', 'Komoditas berhasil ditambahkan.');
    })->name('commodity.store');
    Route::put('/komoditas/{id}', function (\Modules\Farmer\Http\Requests\UpdateCommodityRequest $request, int $id, \Modules\Farmer\Services\CommodityService $service) {
        $model = $service->findOrFail($id);
        $service->update($model, $request->validated());
        return redirect()->back()->with('success', 'Komoditas berhasil diperbarui.');
    })->name('commodity.update');
    Route::delete('/komoditas/{id}', function (int $id, \Modules\Farmer\Services\CommodityService $service) {
        $model = $service->findOrFail($id);
        $service->delete($model);
        return redirect()->back()->with('success', 'Komoditas berhasil dihapus.');
    })->name('commodity.destroy');
    Route::get('/petani', fn (
        \Modules\Farmer\Services\FarmerService $farmerService,
        \Modules\Region\Services\RegionService $regionService,
        \Modules\Region\Services\VillageService $villageService,
        \Modules\Farmer\Services\FarmerGroupService $farmerGroupService
    ) => \App\Support\InertiaQuery::render('Admin/Farmer/Index', $farmerService->paginateFiltered(), \Modules\Farmer\Http\Resources\FarmerResource::class, [
        'regions' => $regionService->list(),
        'villages' => $villageService->list(),
        'farmerGroups' => $farmerGroupService->list(),
    ], 'farmers'))->name('farmer.index');

    Route::get('/petani/create', fn (
        \Modules\Region\Services\RegionService $regionService,
        \Modules\Region\Services\VillageService $villageService,
        \Modules\Farmer\Services\FarmerGroupService $farmerGroupService,
        \Modules\Farmer\Services\CommodityService $commodityService
    ) => Inertia::render('Admin/Farmer/Create', [
        'regions' => $regionService->list(),
        'villages' => $villageService->list(),
        'farmerGroups' => $farmerGroupService->list(),
        'commodities' => $commodityService->list(),
    ]))->name('farmer.create');

    Route::post('/petani', function (\Modules\Farmer\Http\Requests\StoreFarmerRequest $request, \Modules\Farmer\Services\FarmerService $service) {
        $service->create($request->validated());
        return redirect()->route('admin.farmer.index')->with('success', 'Petani berhasil ditambahkan.');
    })->name('farmer.store');

    Route::get('/petani/{id}', fn (
        int $id,
        \Modules\Farmer\Services\FarmerService $service
    ) => Inertia::render('Admin/Farmer/Show', [
        'farmer' => (new \Modules\Farmer\Http\Resources\FarmerResource($service->findOrFail($id)))->resolve(),
    ]))->name('farmer.show');

    Route::get('/petani/{id}/edit', fn (
        int $id,
        \Modules\Farmer\Services\FarmerService $service,
        \Modules\Region\Services\RegionService $regionService,
        \Modules\Region\Services\VillageService $villageService,
        \Modules\Farmer\Services\FarmerGroupService $farmerGroupService,
        \Modules\Farmer\Services\CommodityService $commodityService
    ) => Inertia::render('Admin/Farmer/Edit', [
        'farmer' => (new \Modules\Farmer\Http\Resources\FarmerResource($service->findOrFail($id)))->resolve(),
        'regions' => $regionService->list(),
        'villages' => $villageService->list(),
        'farmerGroups' => $farmerGroupService->list(),
        'commodities' => $commodityService->list(),
    ]))->name('farmer.edit');

    Route::put('/petani/{id}', function (\Modules\Farmer\Http\Requests\UpdateFarmerRequest $request, int $id, \Modules\Farmer\Services\FarmerService $service) {
        $model = $service->findOrFail($id);
        $service->update($model, $request->validated());
        return redirect()->route('admin.farmer.index')->with('success', 'Petani berhasil diperbarui.');
    })->name('farmer.update');

    Route::delete('/petani/{id}', function (int $id, \Modules\Farmer\Services\FarmerService $service) {
        $model = $service->findOrFail($id);
        $service->delete($model);
        return redirect()->back()->with('success', 'Petani berhasil dihapus.');
    })->name('farmer.destroy');
    Route::get('/kelompok-tani', fn (\Modules\Farmer\Services\FarmerGroupService $farmerGroupService, \Modules\Region\Services\RegionService $regionService) => \App\Support\InertiaQuery::render('Admin/FarmerGroup/Index', $farmerGroupService->paginateFiltered(), \Modules\Farmer\Http\Resources\FarmerGroupResource::class, ['regions' => $regionService->list()], 'farmerGroups'))->name('farmer-group.index');
    Route::get('/kelompok-tani/create', fn (\Modules\Region\Services\RegionService $regionService) => Inertia::render('Admin/FarmerGroup/Create', ['regions' => $regionService->list()]))->name('farmer-group.create');
    Route::post('/kelompok-tani', function (\Modules\Farmer\Http\Requests\StoreFarmerGroupRequest $request, \Modules\Farmer\Services\FarmerGroupService $service) {
        $service->create($request->validated());
        return redirect()->route('admin.farmer-group.index')->with('success', 'Kelompok tani berhasil ditambahkan.');
    })->name('farmer-group.store');
    Route::get('/kelompok-tani/{id}', function (int $id, \Modules\Farmer\Services\FarmerGroupService $farmerGroupService) {
        $group = $farmerGroupService->findOrFail($id);
        $members = $group->farmers()->get();
        return Inertia::render('Admin/FarmerGroup/Show', [
            'farmerGroup' => (new \Modules\Farmer\Http\Resources\FarmerGroupResource($group))->resolve(),
            'members' => \Modules\Farmer\Http\Resources\FarmerResource::collection($members)->resolve(),
        ]);
    })->name('farmer-group.show');
    Route::get('/kelompok-tani/{id}/edit', function (int $id, \Modules\Farmer\Services\FarmerGroupService $farmerGroupService, \Modules\Region\Services\RegionService $regionService, \Modules\Farmer\Services\FarmerService $farmerService) {
        $group = $farmerGroupService->findOrFail($id);
        $members = $group->farmers()->get();
        $availableFarmers = \Modules\Farmer\Models\Farmer::where('region_id', $group->region_id)
                                    ->whereNull('farmer_group_id')
                                    ->get();
        return Inertia::render('Admin/FarmerGroup/Edit', [
            'farmerGroup' => (new \Modules\Farmer\Http\Resources\FarmerGroupResource($group))->resolve(),
            'regions' => $regionService->list(),
            'members' => \Modules\Farmer\Http\Resources\FarmerResource::collection($members)->resolve(),
            'availableFarmers' => \Modules\Farmer\Http\Resources\FarmerResource::collection($availableFarmers)->resolve(),
        ]);
    })->name('farmer-group.edit');
    Route::put('/kelompok-tani/{id}', function (\Modules\Farmer\Http\Requests\UpdateFarmerGroupRequest $request, int $id, \Modules\Farmer\Services\FarmerGroupService $service) {
        $model = $service->findOrFail($id);
        $service->update($model, $request->validated());
        return redirect()->route('admin.farmer-group.index')->with('success', 'Kelompok tani berhasil diperbarui.');
    })->name('farmer-group.update');
    Route::delete('/kelompok-tani/{id}', function (int $id, \Modules\Farmer\Services\FarmerGroupService $service) {
        $model = $service->findOrFail($id);
        $service->delete($model);
        return redirect()->back()->with('success', 'Kelompok tani berhasil dihapus.');
    })->name('farmer-group.destroy');
    Route::post('/kelompok-tani/{id}/tambah-petani', function (int $id, \Illuminate\Http\Request $request, \Modules\Farmer\Services\FarmerGroupService $farmerGroupService, \Modules\Farmer\Services\FarmerService $farmerService) {
        $request->validate(['farmer_id' => 'required|exists:farmers,id']);
        $group = $farmerGroupService->findOrFail($id);
        $farmer = $farmerService->findOrFail($request->farmer_id);
        if ($farmer->region_id !== $group->region_id) abort(400, 'Petani tidak berada di distrik yang sama dengan kelompok tani ini.');
        $farmer->update(['farmer_group_id' => $group->id]);
        return redirect()->back()->with('success', 'Petani berhasil ditambahkan ke kelompok.');
    })->name('farmer-group.attach-farmer');
    Route::post('/kelompok-tani/{id}/keluarkan-petani', function (int $id, \Illuminate\Http\Request $request, \Modules\Farmer\Services\FarmerGroupService $farmerGroupService, \Modules\Farmer\Services\FarmerService $farmerService) {
        $request->validate(['farmer_id' => 'required|exists:farmers,id']);
        $group = $farmerGroupService->findOrFail($id);
        $farmer = $farmerService->findOrFail($request->farmer_id);
        if ($farmer->farmer_group_id !== $group->id) abort(400, 'Petani tersebut bukan anggota kelompok ini.');
        $farmer->update(['farmer_group_id' => null]);
        return redirect()->back()->with('success', 'Petani berhasil dikeluarkan dari kelompok.');
    })->name('farmer-group.detach-farmer');
    Route::get('/wilayah', fn (\Modules\Region\Services\RegionService $regionService) => \App\Support\InertiaQuery::render('Admin/Region/Index', $regionService->paginateFiltered(), \Modules\Region\Http\Resources\RegionResource::class, [], 'regions'))->name('region.index');

    Route::get('/wilayah/{id}', function (int $id, \Modules\Region\Services\RegionService $service) {
        $model = $service->findOrFail($id);
        return Inertia::render('Admin/Region/Show', [
            'region' => (new \Modules\Region\Http\Resources\RegionResource($model))->resolve(),
        ]);
    })->name('region.show');
    Route::get('/wilayah/{id}/edit', function (int $id, \Modules\Region\Services\RegionService $service) {
        $model = $service->findOrFail($id);
        return Inertia::render('Admin/Region/Edit', [
            'region' => (new RegionResource($model))->resolve(),
        ]);
    })->name('region.edit');
    Route::put('/wilayah/{id}', function (\Modules\Region\Http\Requests\UpdateRegionRequest $request, int $id, \Modules\Region\Services\RegionService $service) {
        $model = $service->findOrFail($id);
        $service->update($model, $request->validated());
        return redirect()->route('admin.region.index')->with('success', 'Wilayah berhasil diperbarui.');
    })->name('region.update');

    Route::get('/desa', fn () => Inertia::render('Admin/Village/Index'))->name('village.index');
    Route::get('/mitra', fn () => Inertia::render('Admin/Partner/Index'))->name('partner.index');
    Route::get('/faq', fn () => Inertia::render('Admin/Faq/Index'))->name('faq.index');
    Route::get('/pengaturan', fn () => Inertia::render('Admin/Setting'))->name('setting.index');
    Route::get('/user', fn () => Inertia::render('Admin/User/Index'))->name('user.index');
    Route::get('/berita', fn () => Inertia::render('Admin/Post/Index'))->name('post.index');
    Route::get('/audit-log', fn () => Inertia::render('Admin/AuditLog'))->name('audit.index');
});

if (! app()->isProduction()) {
    Route::get('/ui', fn () => Inertia::render('Ui/Index'))->name('ui.showcase');
}
