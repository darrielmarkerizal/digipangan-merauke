<?php

use Illuminate\Support\Facades\Route;
use Modules\Home\Http\Controllers\Public\HomeController;

// Public home feed (guest, read-only): featured products, latest products, region cards.
Route::middleware(['throttle:120,1'])->prefix('v1/public')->name('public.')->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home.index');
});
