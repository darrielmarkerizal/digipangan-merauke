<?php

use Illuminate\Support\Facades\Route;
use Modules\Farmer\Http\Controllers\FarmerGroupController;
use Modules\Farmer\Http\Controllers\CommodityController;
use Modules\Farmer\Http\Controllers\FarmerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource("farmer-groups", FarmerGroupController::class)->names("farmer_group");
    Route::apiResource("commodities", CommodityController::class)->names("commodity");
    Route::apiResource('farmers', FarmerController::class)->names('farmer');
});
