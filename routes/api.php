<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RunsController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ArticlesController;
use App\Models\User;
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

Route::get('health-check', [UserController::class, 'health_check']);

Route::middleware(['access_token:' . User::ADMIN])->group(function () {
    Route::post('articles', [ArticlesController::class, 'store']);
    Route::put('articles/{Id}', [ArticlesController::class, 'update']);
    Route::delete('articles/{Id}', [ArticlesController::class, 'destroy']);
});

Route::middleware(['access_token:'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('users-search/{page?}/{keyword?}', [UserController::class, 'search']);
    Route::put('users-running/{running?}', [UserController::class, 'update_running']);

    Route::apiResource('runs', RunsController::class);
    Route::get('runs-search/{page}/{user_id?}', [RunsController::class, 'search']);
    Route::put('runs-running/{run_id}/{running?}', [RunsController::class, 'update_running']);
    Route::post('runs/sync', [RunsController::class, 'sync']);

    Route::get('locations-search/{page}/{run_id}', [LocationsController::class, 'search']);
    Route::post('locations/sync/{run_id?}', [LocationsController::class, 'sync']);
    Route::get('locations-next/{run_id}/{last_location_position}', [LocationsController::class, 'get_next_locations']);

    Route::get('categories', [ArticlesController::class, 'categories']);
    Route::get('articles/{category_id?}', [ArticlesController::class, 'search']);

    Route::get('profile', [UserController::class, 'profile']);
    Route::get('logout', [UserController::class, 'logout']);

    Route::apiResource('messages', MessagesController::class);
    Route::get('messages-search/{page}/{friend_id?}', [MessagesController::class, 'search']);
    Route::get('messages-get/{friend_id}/{type?}/{last_id?}', [MessagesController::class, 'getMessages']);
    Route::put('messages-mark-read/{friend_id?}', [MessagesController::class, 'markAllRead']);
});

Route::middleware(['optional_access_token:'])->group(function () {
    Route::post('change-password', [UserController::class, 'changePassword']);
});

Route::withoutMiddleware([ValidateAccessToken::class])->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('send-reset-pasword-token', [UserController::class, 'sendResetPasswordToken']);
});
