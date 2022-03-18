<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    Auth\AuthController
};

Route::group([
    'prefix' => env('API_VERSION')
], function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group([
        'prefix' => 'auth',
        'middleware' => ['auth:sanctum']
    ], function () {

        Route::post('/logout', [AuthController::class, 'logout']);

    });
});
