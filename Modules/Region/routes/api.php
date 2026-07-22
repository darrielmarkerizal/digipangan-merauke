<?php

use Illuminate\Support\Facades\Route;
use Modules\Region\Http\Controllers\Public\RegionController as PublicRegionController;
use Modules\Region\Http\Controllers\RegionController;
use Modules\Region\Http\Controllers\VillageController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('villages', VillageController::class)->names('village');
    Route::apiResource('regions', RegionController::class)->names('region');
});

// Public region profiles (guest, read-only). Active regions only; slug identifiers.
Route::middleware(['throttle:120,1'])->prefix('v1/public')->name('public.')->group(function () {
    Route::get('regions', [PublicRegionController::class, 'index'])->name('region.index');
    Route::get('regions/{slug}', [PublicRegionController::class, 'show'])->name('region.show');
    Route::get('regions/{slug}/products', [PublicRegionController::class, 'products'])->name('region.products');
});
