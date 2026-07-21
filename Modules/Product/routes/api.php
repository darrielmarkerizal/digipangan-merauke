<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\UnitController;
use Modules\Product\Http\Controllers\ProductCategoryController;
use Modules\Product\Http\Controllers\ProductController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource("units", UnitController::class)->names("unit");
    Route::apiResource("product-categories", ProductCategoryController::class)->names("product_category");
    Route::apiResource('products', ProductController::class)->names('product');
});
