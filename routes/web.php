<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Modules\Farmer\Http\Controllers\Admin\CommodityAdminController;
use Modules\Farmer\Http\Controllers\Admin\FarmerAdminController;
use Modules\Farmer\Http\Controllers\Admin\FarmerGroupAdminController;
use Modules\Home\Http\Controllers\Public\HomePageController;
use Modules\Post\Http\Controllers\Admin\PostAdminController;
use Modules\Product\Http\Controllers\Admin\CategoryAdminController;
use Modules\Product\Http\Controllers\Admin\ProductAdminController;
use Modules\Product\Http\Controllers\Admin\UnitAdminController;
use Modules\Region\Http\Controllers\Admin\RegionAdminController;
use Modules\Region\Http\Controllers\Admin\VillageAdminController;
use Modules\User\Http\Controllers\Admin\AuthAdminController;
use Modules\User\Http\Controllers\Admin\UserAdminController;
use Modules\Farmer\Http\Controllers\Public\FarmerRegisterController;
use Modules\Farmer\Http\Controllers\Farmer\FarmerProfileController;
use Modules\Product\Http\Controllers\Farmer\FarmerProductController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use Modules\Page\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\AuditLogAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;
use Inertia\Inertia;

Route::get('/', [HomePageController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/wilayah', [\Modules\Region\Http\Controllers\Public\RegionPageController::class, 'index'])->name('region.public.index');
Route::get('/wilayah/{slug}', [\Modules\Region\Http\Controllers\Public\RegionPageController::class, 'show'])->name('region.public.show');

Route::get('/produk', [\Modules\Product\Http\Controllers\Public\ProductPageController::class, 'index'])->name('product.public.index');
Route::get('/produk/{slug}', [\Modules\Product\Http\Controllers\Public\ProductPageController::class, 'show'])->name('product.public.show');

Route::middleware(['auth', 'role:farmer'])->prefix('petani/dashboard')->name('farmer.dashboard.')->group(function () {
    Route::get('/', [FarmerDashboardController::class, 'index'])->name('index');

    Route::get('/profil', [FarmerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [FarmerProfileController::class, 'update'])->name('profile.update');

    Route::get('/produk', [FarmerProductController::class, 'index'])->name('product.index');
    Route::get('/produk/tambah', [FarmerProductController::class, 'create'])->name('product.create');
    Route::post('/produk', [FarmerProductController::class, 'store'])->name('product.store');
    Route::get('/produk/{id}/edit', [FarmerProductController::class, 'edit'])->name('product.edit');
    Route::put('/produk/{id}', [FarmerProductController::class, 'update'])->name('product.update');
    Route::delete('/produk/{id}', [FarmerProductController::class, 'destroy'])->name('product.destroy');
});

Route::get('/petani', [\Modules\Farmer\Http\Controllers\Public\FarmerPageController::class, 'index'])->name('farmer.public.index');
Route::get('/petani/{slug}', [\Modules\Farmer\Http\Controllers\Public\FarmerPageController::class, 'show'])->name('farmer.public.show');

Route::get('/berita', [\Modules\Post\Http\Controllers\Public\PostPageController::class, 'index'])->name('post.public.index');
Route::get('/berita/{slug}', [\Modules\Post\Http\Controllers\Public\PostPageController::class, 'show'])->name('post.public.show');

Route::get('/tentang', [\Modules\Page\Http\Controllers\Public\AboutPageController::class, 'show'])->name('about.public.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthAdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthAdminController::class, 'login'])->name('login.store');
    Route::get('/daftar', [FarmerRegisterController::class, 'create'])->name('farmer.register');
    Route::post('/daftar', [FarmerRegisterController::class, 'store'])->name('farmer.register.store');
});

Route::post('/logout', [AuthAdminController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin|super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard.index');

    Route::get('/produk', [ProductAdminController::class, 'index'])->name('product.index');
    Route::get('/produk/tambah', [ProductAdminController::class, 'create'])->name('product.create');
    Route::post('/produk', [ProductAdminController::class, 'store'])->name('product.store');
    Route::get('/produk/{id}', [ProductAdminController::class, 'show'])->name('product.show');
    Route::get('/produk/{id}/edit', [ProductAdminController::class, 'edit'])->name('product.edit');
    Route::put('/produk/{id}', [ProductAdminController::class, 'update'])->name('product.update');
    Route::delete('/produk/{id}', [ProductAdminController::class, 'destroy'])->name('product.destroy');

    Route::get('/kategori', [CategoryAdminController::class, 'index'])->name('category.index');
    Route::post('/kategori', [CategoryAdminController::class, 'store'])->name('category.store');
    Route::put('/kategori/{id}', [CategoryAdminController::class, 'update'])->name('category.update');
    Route::delete('/kategori/{id}', [CategoryAdminController::class, 'destroy'])->name('category.destroy');

    Route::get('/satuan', [UnitAdminController::class, 'index'])->name('unit.index');
    Route::post('/satuan', [UnitAdminController::class, 'store'])->name('unit.store');
    Route::put('/satuan/{id}', [UnitAdminController::class, 'update'])->name('unit.update');
    Route::delete('/satuan/{id}', [UnitAdminController::class, 'destroy'])->name('unit.destroy');

    Route::get('/komoditas', [CommodityAdminController::class, 'index'])->name('commodity.index');
    Route::post('/komoditas', [CommodityAdminController::class, 'store'])->name('commodity.store');
    Route::put('/komoditas/{id}', [CommodityAdminController::class, 'update'])->name('commodity.update');
    Route::delete('/komoditas/{id}', [CommodityAdminController::class, 'destroy'])->name('commodity.destroy');

    Route::get('/petani', [FarmerAdminController::class, 'index'])->name('farmer.index');
    Route::get('/petani/create', [FarmerAdminController::class, 'create'])->name('farmer.create');
    Route::post('/petani', [FarmerAdminController::class, 'store'])->name('farmer.store');
    Route::get('/petani/{id}', [FarmerAdminController::class, 'show'])->name('farmer.show');
    Route::get('/petani/{id}/edit', [FarmerAdminController::class, 'edit'])->name('farmer.edit');
    Route::put('/petani/{id}', [FarmerAdminController::class, 'update'])->name('farmer.update');
    Route::delete('/petani/{id}', [FarmerAdminController::class, 'destroy'])->name('farmer.destroy');

    Route::get('/kelompok-tani', [FarmerGroupAdminController::class, 'index'])->name('farmer-group.index');
    Route::get('/kelompok-tani/create', [FarmerGroupAdminController::class, 'create'])->name('farmer-group.create');
    Route::post('/kelompok-tani', [FarmerGroupAdminController::class, 'store'])->name('farmer-group.store');
    Route::get('/kelompok-tani/{id}', [FarmerGroupAdminController::class, 'show'])->name('farmer-group.show');
    Route::get('/kelompok-tani/{id}/edit', [FarmerGroupAdminController::class, 'edit'])->name('farmer-group.edit');
    Route::put('/kelompok-tani/{id}', [FarmerGroupAdminController::class, 'update'])->name('farmer-group.update');
    Route::delete('/kelompok-tani/{id}', [FarmerGroupAdminController::class, 'destroy'])->name('farmer-group.destroy');
    Route::post('/kelompok-tani/{id}/tambah-petani', [FarmerGroupAdminController::class, 'attachFarmer'])->name('farmer-group.attach-farmer');
    Route::post('/kelompok-tani/{id}/keluarkan-petani', [FarmerGroupAdminController::class, 'detachFarmer'])->name('farmer-group.detach-farmer');

    Route::get('/wilayah', [RegionAdminController::class, 'index'])->name('region.index');
    Route::get('/wilayah/{id}', [RegionAdminController::class, 'show'])->name('region.show');
    Route::get('/wilayah/{id}/edit', [RegionAdminController::class, 'edit'])->name('region.edit');
    Route::put('/wilayah/{id}', [RegionAdminController::class, 'update'])->name('region.update');

    Route::get('/desa', [VillageAdminController::class, 'index'])->name('village.index');
    Route::get('/desa/{id}', [VillageAdminController::class, 'show'])->name('village.show');
    Route::get('/desa/{id}/edit', [VillageAdminController::class, 'edit'])->name('village.edit');
    Route::put('/desa/{id}', [VillageAdminController::class, 'update'])->name('village.update');

    Route::get('/berita', [PostAdminController::class, 'index'])->name('post.index');
    Route::get('/berita/tambah', [PostAdminController::class, 'create'])->name('post.create');
    Route::post('/berita', [PostAdminController::class, 'store'])->name('post.store');
    Route::get('/berita/{id}', [PostAdminController::class, 'show'])->name('post.show');
    Route::get('/berita/{id}/edit', [PostAdminController::class, 'edit'])->name('post.edit');
    Route::put('/berita/{id}', [PostAdminController::class, 'update'])->name('post.update');
    Route::delete('/berita/{id}', [PostAdminController::class, 'destroy'])->name('post.destroy');

    Route::get('/faq', [FaqAdminController::class, 'index'])->name('faq.index');
    Route::get('/faq/tambah', [FaqAdminController::class, 'create'])->name('faq.create');
    Route::post('/faq', [FaqAdminController::class, 'store'])->name('faq.store');
    Route::get('/faq/{id}/edit', [FaqAdminController::class, 'edit'])->name('faq.edit');
    Route::put('/faq/{id}', [FaqAdminController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{id}', [FaqAdminController::class, 'destroy'])->name('faq.destroy');
    Route::get('/pengaturan', fn () => Inertia::render('Admin/Setting'))->name('setting.index');
    Route::get('/user', [UserAdminController::class, 'index'])->name('user.index');
    Route::get('/user/tambah', [UserAdminController::class, 'create'])->name('user.create');
    Route::post('/user', [UserAdminController::class, 'store'])->name('user.store');
    Route::get('/user/{id}', [UserAdminController::class, 'show'])->name('user.show');
    Route::get('/user/{id}/edit', [UserAdminController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserAdminController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserAdminController::class, 'destroy'])->name('user.destroy');
    Route::get('/audit-log', [AuditLogAdminController::class, 'index'])->name('audit.index');
});

if (! app()->isProduction()) {
    Route::get('/ui', fn () => Inertia::render('Ui/Index'))->name('ui.showcase');
}
