<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    Auth\AuthController
};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'prefix' => env('API_VERSION')
], function () {



    Route::group([
        'middleware' => ['auth:sanctum']
    ], function () {


    });
});
