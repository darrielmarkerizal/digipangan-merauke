<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Http\Controllers\MediaController;

Route::middleware(['auth'])->prefix('admin/media')->group(function () {
    Route::post('/upload', [Modules\Media\Http\Controllers\Api\MediaUploadController::class, 'store'])->name('media.upload');
    Route::delete('/upload', [Modules\Media\Http\Controllers\Api\MediaUploadController::class, 'destroy'])->name('media.delete');
});
