<?php
use Illuminate\Support\Facades\Route;
Route::get('/test-session-web', function () {
    return response()->json(['auth' => auth()->check(), 'id' => auth()->id(), 'session_id' => session()->getId()]);
})->middleware('web');

Route::get('/test-session-api', function () {
    return response()->json(['auth' => auth()->check(), 'id' => auth()->id(), 'session_id' => session()->getId()]);
})->middleware('api');

Route::get('/test-session-both', function () {
    return response()->json(['auth' => auth()->check(), 'id' => auth()->id(), 'session_id' => session()->getId()]);
})->middleware(['web', 'api']);
