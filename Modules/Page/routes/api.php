<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\FaqController;
use Modules\Page\Http\Controllers\PartnerController;
use Modules\Page\Http\Controllers\Public\AboutController as PublicAboutController;
use Modules\Page\Http\Controllers\SiteSettingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('partners', PartnerController::class)->names('partner');
    Route::apiResource('faqs', FaqController::class)->names('faq');

    Route::get('site-settings', [SiteSettingController::class, 'index'])->name('site_setting.index');
    Route::put('site-settings', [SiteSettingController::class, 'update'])->name('site_setting.update');
});

// Public About page (guest, read-only): narrative settings, partners, and FAQs.
Route::middleware(['throttle:120,1'])->prefix('v1/public')->name('public.')->group(function () {
    Route::get('about', [PublicAboutController::class, 'show'])->name('about.show');
});
