<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Home\Http\Controllers\Public\HomePageController;

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
    Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');
    Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard.index');
    Route::get('/produk', fn () => Inertia::render('Admin/Product/Index'))->name('product.index');
    Route::get('/kategori', fn () => Inertia::render('Admin/Category/Index'))->name('category.index');
    Route::get('/satuan', fn () => Inertia::render('Admin/Unit/Index'))->name('unit.index');
    Route::get('/komoditas', fn () => Inertia::render('Admin/Commodity/Index'))->name('commodity.index');
    Route::get('/petani', fn () => Inertia::render('Admin/Farmer/Index'))->name('farmer.index');
    Route::get('/kelompok-tani', fn () => Inertia::render('Admin/FarmerGroup/Index'))->name('farmer-group.index');
    Route::get('/wilayah', fn () => Inertia::render('Admin/Region/Index'))->name('region.index');
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
