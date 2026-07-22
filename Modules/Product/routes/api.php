<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductCategoryController;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductInteractionController;
use Modules\Product\Http\Controllers\UnitController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('units', UnitController::class)->names('unit');
    Route::apiResource('product-categories', ProductCategoryController::class)->names('product_category');
    Route::apiResource('products', ProductController::class)->names('product');
});

// Public interaction tracking (no auth). Bound by product slug; throttled to
// prevent metric inflation. Recording happens after the response is sent.
Route::middleware(['throttle:60,1'])->prefix('v1')->group(function () {
    Route::post('products/{product}/view', [ProductInteractionController::class, 'view'])->name('product.view');
    Route::post('products/{product}/contact', [ProductInteractionController::class, 'contact'])->name('product.contact');
});
