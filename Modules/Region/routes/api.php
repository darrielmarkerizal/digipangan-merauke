<?php

use Illuminate\Support\Facades\Route;
use Modules\Region\Http\Controllers\VillageController;
use Modules\Region\Http\Controllers\RegionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource("villages", VillageController::class)->names("village");
    Route::apiResource('regions', RegionController::class)->names('region');
});
