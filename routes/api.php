<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApteksDataController;
use App\Http\Controllers\Api\ApteksCounterController;

// API auth (token)
Route::post('/login', [AuthController::class, 'loginApi'])
    ->name('login.api'); // /api/login

Route::get('/apteks/counter', [ApteksCounterController::class, 'show']);

//Route::get('/apteks/counter', function () {
//    return response()->json([
//        'id' => 1,
//        'total_apteks' => 1725,
//        'connected_apteks' => 1691,
//        'disconnected_total' => 105,
//        'disconnected_min' => 11,
//        'disconnected_lvl1' => 94,
//        'disconnected_lvl2' => 4,
//        'disconnected_max' => 18,
//        'created_at' => now()->toDateTimeString(),
//    ]);
//});

Route::get('/chatbot/stats', function () {
    return response()->json([
        'queue' => 1,
        'inWork' => 2,
        'total' => 3,
        'employees' => 1,
        'updated_at' => now()->toDateTimeString(),
    ]);
});
