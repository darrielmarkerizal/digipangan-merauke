<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/profile', [AuthController::class, 'profile'])->name('auth.profile');
        Route::match(['put', 'patch'], 'auth/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');

        Route::apiResource('users', UserController::class)->names('user');
    });
});
