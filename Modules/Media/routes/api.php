<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('v1/media')->group(function () {
});
