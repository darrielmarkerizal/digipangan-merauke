<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Home\Http\Controllers\Public\HomePageController;

Route::get('/', [HomePageController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::middleware('guest')->get('/login', fn () => Inertia::render('Auth/Login'))->name('login');

if (! app()->isProduction()) {
    Route::get('/ui', fn () => Inertia::render('Ui/Index'))->name('ui.showcase');
}
