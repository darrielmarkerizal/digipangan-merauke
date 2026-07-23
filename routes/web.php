<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'laravelVersion' => app()->version(),
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

if (! app()->isProduction()) {
    Route::get('/ui', fn () => Inertia::render('Ui/Index'))->name('ui.showcase');
}
