<?php

use Illuminate\Support\Facades\Route;
use Modules\Farmer\Http\Controllers\CommodityController;
use Modules\Farmer\Http\Controllers\FarmerController;
use Modules\Farmer\Http\Controllers\FarmerGroupController;
use Modules\Farmer\Http\Controllers\Public\FarmerController as PublicFarmerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('farmer-groups', FarmerGroupController::class)->names('farmer_group');
    Route::apiResource('commodities', CommodityController::class)->names('commodity');
    Route::apiResource('farmers', FarmerController::class)->names('farmer');
});

// Public farmer profiles (guest, read-only). Active farmers only; slug identifiers.
Route::middleware(['throttle:120,1'])->prefix('v1/public')->name('public.')->group(function () {
    Route::get('farmers/{slug}', [PublicFarmerController::class, 'show'])->name('farmer.show');
});
