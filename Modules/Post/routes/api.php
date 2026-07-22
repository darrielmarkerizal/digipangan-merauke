<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\Http\Controllers\PostCategoryController;
use Modules\Post\Http\Controllers\PostController;
use Modules\Post\Http\Controllers\Public\PostController as PublicPostController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('post-categories', PostCategoryController::class)->names('post_category');
    Route::apiResource('posts', PostController::class)->names('post');
});

// Public news (guest, read-only). Published posts only; slug identifiers.
Route::middleware(['throttle:120,1'])->prefix('v1/public')->name('public.')->group(function () {
    Route::get('posts', [PublicPostController::class, 'index'])->name('post.index');
    Route::get('posts/{slug}', [PublicPostController::class, 'show'])->name('post.show');
});
