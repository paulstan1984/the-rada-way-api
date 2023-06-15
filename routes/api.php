<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RunsController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\MessagesController;

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

Route::middleware(['access_token:'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('users-search/{page?}/{keyword?}', [UserController::class, 'search']);

    Route::apiResource('runs', RunsController::class);
    Route::get('runs-search/{page?}/{user_id?}', [RunsController::class, 'search']);
    Route::post('runs/sync', [RunsController::class, 'sync']);

    Route::get('locations-search/{page}/{run_id}', [LocationsController::class, 'search']);
    Route::post('locations/sync/{run_id?}', [LocationsController::class, 'sync']);

    Route::get('profile', [UserController::class, 'profile']);
    Route::get('logout', [UserController::class, 'logout']);

    Route::apiResource('messages', MessagesController::class);
    Route::get('messages-search/{page?}/{friend_id?}', [MessagesController::class, 'search']);
    Route::get('my-last-messages/{page?}', [MessagesController::class, 'my_last_messages']);
});

Route::middleware(['optional_access_token:'])->group(function () {
    Route::post('change-password', [UserController::class, 'changePassword']);
});

Route::withoutMiddleware([ValidateAccessToken::class])->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('send-reset-pasword-token', [UserController::class, 'sendResetPasswordToken']);
});