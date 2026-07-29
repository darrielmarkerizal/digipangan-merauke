<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Http\Controllers\Api\MediaUploadController;

Route::middleware(['web', 'auth'])->prefix('v1/media')->group(function () {
    Route::post('/upload', [MediaUploadController::class, 'store'])->name('media.upload');
    Route::delete('/upload', [MediaUploadController::class, 'destroy'])->name('media.delete');
});
