<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApteksDataController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])
    ->middleware(['web'])
    ->name('login.web');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Route::middleware('auth')->group(function () {
//    Route::get('/', function () {
//        return view('home');
//    })->name('home');
Route::get(
    '/', function () {
        return view('home');
        })->name('home');
    require __DIR__ . '/web/apteks.php'; //маршруты для раздела apteks
    require __DIR__ . '/web/providers.php'; //маршруты для раздела providers
    require __DIR__ . '/web/technics.php'; //маршруты для раздела technics
//});

Route::get('/dev/ws-counter', function () {
    return view('dev.ws-counter');
});

Route::get('/__auth_debug', function (Request $request) {
    $sid = $request->session()->getId();

    $row = null;
    try {
        $row = DB::table('sessions')->where('id', $sid)->first();
    } catch (\Throwable $e) {}

    return response()->json([
        'url' => $request->fullUrl(),
        'auth_check' => Auth::check(),
        'user_id' => Auth::id(),
        'session_id' => $sid,
        'cookie_laravel_session_present' => (bool) $request->cookie('laravel_session'),
        'db_session_row_found' => (bool) $row,
        'db_user_id' => $row->user_id ?? null,
        'db_last_activity' => $row->last_activity ?? null,
    ]);
});
