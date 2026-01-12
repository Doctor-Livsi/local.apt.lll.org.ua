<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApteksDataController;

// API маршрут для логіну
Route::post('/api/login', [AuthController::class, 'login']);
// Вихід
Route::post('/logout', [AuthController::class, 'logout']);


// Головна сторінка
Route::view('/', 'horizontal')->name('home')->middleware('auth');

// Сторінка логіну (без middleware, щоб була доступна)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Перенаправлення GET-запитів на /login
Route::get('/api/login', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/regions/{status}', [ApteksDataController::class, 'getRegions']);
    Route::get('/towns/{status}', [ApteksDataController::class, 'getTowns']);
    Route::post('/apteks/{status}/data', [ApteksDataController::class, 'getData']);
});

//Route::middleware('auth')
//    ->post('/api/apteks/{status}/data', [ApteksDataController::class, 'getData']);

//Route::middleware('auth')->group(function () {
    require __DIR__ . '/web/apteks.php';
    require __DIR__ . '/web/providers.php';
    require __DIR__ . '/web/technics.php';
//});

/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
|
*/
Route::view('/Horizontal', 'horizontal')->name('horizontal')->middleware('auth');
Route::view('/Vertical', 'vertical')->name('vertical')->middleware('auth');
