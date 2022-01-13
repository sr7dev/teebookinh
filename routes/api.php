<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Auth routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::any('un-authenticated', [AuthController::class, 'no_authenticated'])->name('unauthenticated');
    Route::get('google', [SocialiteAuthController::class, 'googleRedirect'])->name('auth/google');
    Route::get('/auth/google-callback', [SocialiteAuthController::class, 'loginWithGoogle']);
});

// Authenticated admin routes
Route::group([
    'middleware' => ['auth:api', 'admin'],
    'namespace' => '\App\Http\Controllers\Api',
    // 'prefix' => 'admin',

], function ($router) {
    Route::apiResource('users', UserController::class);

});

// Authenticated user routes
Route::group([
    'middleware' => ['auth:api'],
    'namespace' => '\App\Http\Controllers\Api',

], function ($router) {
    Route::get('events/export-csv', [EventController::class, 'exportCsv']);
    Route::post('events/import-csv',  [EventController::class, 'importCsv']);
    Route::apiResource('events', EventController::class)->except('show');
});
