<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\SocialiteAuthController;

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
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::any('un-authenticated', [AuthController::class, 'no_authenticated'])->name('unauthenticated');
});

Route::group([
    'middleware' => 'api',
    // 'prefix' => 'auth',

], function ($router) {
    // Route::get('/auth/google', [SocialiteAuthController::class, 'googleRedirect'])->name('auth/google');
    Route::post('/auth/google-callback', [SocialiteAuthController::class, 'loginWithGoogle']);
});

// Authenticated admin routes
Route::group([
    'middleware' => ['auth:api', 'admin'],
    'namespace' => '\App\Http\Controllers\Api',
], function ($router) {
    Route::apiResource('users', UserController::class);

});


Route::group([
    'middleware' => 'auth:api',
], function ($router) {
    Route::apiResource('courses', EventController::class)->except('show');
    Route::get('/courses/{course_id}', [EventController::class, 'getPersonID']);
    Route::put('/courses/{course_id}', [EventController::class, 'update']);
    Route::delete('/courses/{course_id}', [EventController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:api',
], function ($router) {
    Route::get('/credits', [CreditController::class, 'getCredits']);
    Route::put('/add-credits', [CreditController::class, 'addCredits']);
    Route::put('/deduct-credits', [CreditController::class, 'deductCredits']);
});
