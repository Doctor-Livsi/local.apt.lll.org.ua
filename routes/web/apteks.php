<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApteksController;
use App\Http\Controllers\Api\ApteksDataController;

/*
|--------------------------------------------------------------------------
| АПТЕКИ — WEB (інтерфейс користувача)
|--------------------------------------------------------------------------
| Усі маршрути працюють через web + session (cookie-based auth).
| Розділ відображає список аптек за статусом та картку аптеки.
| Права доступу (permission) буде додано пізніше.
*/

Route::middleware(['web', 'auth'])
    ->prefix('apteks')
    ->name('apteks.')
    ->group(function () {

        // /apteks → тимчасово редірект на статус "working"
        Route::get('/', fn () => redirect()->route('apteks.status', ['status' => 'working']))
            ->name('index');

        // Сторінки списку аптек за статусом (4 окремі Blade-шаблони)
        Route::get('/status/{status}', [ApteksController::class, 'showStatusPage'])
            ->whereIn('status', ['working', 'projected', 'closed', 'connected'])
            ->name('status');

        // Картка конкретної аптеки
        Route::get('/{id}', [ApteksController::class, 'details'])
            ->whereNumber('id')
            ->name('details');

        /*
         * Fallback для некоректних URL усередині /apteks
         * Наприклад: /apteks/blablabla
         * Відображає сторінку-огляд з посиланнями на 4 статуси
         */
        Route::fallback([ApteksController::class, 'index']);
    });

/*
|--------------------------------------------------------------------------
| АПТЕКИ — "API" для DataTables, фільтрів та експорту
|--------------------------------------------------------------------------
| Семантично URL починаються з /api, але технічно це web-маршрути,
| щоб зберегти session, cookies та авторизацію.
*/

Route::middleware(['web', 'auth'])
    ->prefix('api/apteks')
    ->name('api.apteks.')
    ->group(function () {

        // DataTables server-side: отримання даних за статусом
        Route::match(['GET', 'POST'], '/status/{status}', [ApteksDataController::class, 'getDataStatus'])
            ->whereIn('status', ['working', 'projected', 'closed', 'connected'])
            ->name('datatable');

        // Довідники для фільтрів (select над таблицею)
        Route::get('/filters/regions', [ApteksDataController::class, 'regions'])
            ->name('filters.regions');

        Route::get('/filters/cities', [ApteksDataController::class, 'cities'])
            ->name('filters.cities'); // ?region_id=

        Route::get('/filters/companies', [ApteksDataController::class, 'companies'])
            ->name('filters.companies');

        Route::get('/filters/brands', [ApteksDataController::class, 'brands'])
            ->name('filters.brands');

        // Експорт даних (кожна операція — окрема кнопка, без dropdown)
        Route::get('/status/{status}/export/{format}', [ApteksDataController::class, 'export'])
            ->whereIn('status', ['working', 'projected', 'closed', 'connected'])
            ->whereIn('format', ['print', 'copy', 'pdf', 'excel', 'csv'])
            ->name('export');
    });
