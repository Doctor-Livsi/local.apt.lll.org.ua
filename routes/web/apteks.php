<?php
#
# /web/apteks.php
#
//Route::middleware('auth')->group(function () {
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApteksController;
use App\Http\Controllers\Api\ApteksDataController;
// Маршруты для аптек
Route::middleware('auth')->prefix('apteks')->group(function () {
    // Главная страница списка всех аптек
    Route::get('/', [ApteksController::class, 'index'])->name('apteks.index');


    // Динамический маршрут для отображения аптек по статусам
    Route::get('/{status?}', [ApteksController::class, 'showStatusPage'])
        ->whereIn('status', ['working', 'projected', 'closed', 'connected'])
        ->name('apteks.status');

    // Динамический маршрут для получения JSON-данных по статусу
    Route::post('/{status}/data', [ApteksController::class, 'getData'])
        ->whereIn('status', ['working', 'projected', 'closed', 'connected'])
        ->name('apteks.data'); // Динамический маршрут для получения JSON-данных по статусу

    // Маршруты для получения данных в формате JSON

    // Карточка конкретной аптеки
    Route::get('/{id}', [ApteksController::class, 'details'])
        ->where('id', '[0-9]+')
        ->name('apteks.details');

    Route::post('/connection-data', [ApteksController::class, 'getConnectionData'])
        ->name('apteks.connectionData');

});

