<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\StatisticsController;

Route::middleware(['auth:sanctum'])->prefix('v1/dashboard')->group(function () {
    Route::get('statistics', [StatisticsController::class, 'summary'])->name('statistics.summary');
    Route::get('uncontacted-products', [StatisticsController::class, 'uncontactedProducts'])->name('statistics.uncontacted');
});
