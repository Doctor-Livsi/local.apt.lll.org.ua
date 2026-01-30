<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApteksDataController;

// API auth (token)
Route::post('/login', [AuthController::class, 'loginApi'])
    ->name('login.api'); // /api/login

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/regions/{status}', [ApteksDataController::class, 'getRegions']);
    Route::get('/towns/{status}',   [ApteksDataController::class, 'getTowns']);
    Route::post('/apteks/{status}/data', [ApteksDataController::class, 'getData']);
});
