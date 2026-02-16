<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ChatBotStatsController;
use App\Http\Controllers\Api\ApteksCounterController;
use App\Http\Controllers\Api\ApteksEmployeesController;

// API auth (token)
Route::post('/login', [AuthController::class, 'loginApi'])
    ->name('login.api'); // /api/login

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/apteks/counter', [ApteksCounterController::class, 'show']);
    Route::get('/chatbot/stats', [ChatBotStatsController::class, 'show']);
    Route::get('/apteks/{id}/employees', [ApteksEmployeesController::class, 'show']);
});
