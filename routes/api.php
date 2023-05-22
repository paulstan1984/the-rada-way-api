<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


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

    Route::get('profile', [UserController::class, 'profile']);
    Route::get('new-password', [UserController::class, 'setNewPasword']);
    Route::post('reset-password', [UserController::class, 'resetPassword']);

});

Route::withoutMiddleware([ValidateAccessToken::class])->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout']);
});