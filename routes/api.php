<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    Auth\AuthController,
    ProductController,
    SpecificationController
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

        Route::post('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::group([
            'middleware' => ['can:access-dashboard']
        ], function () {
            Route::resource('/product', ProductController::class);
            Route::post('product/search', [ProductController::class, 'search']);

            Route::resource('/specification', SpecificationController::class);
            Route::get('/allSpecifications', [SpecificationController::class, 'all']);
        });
    });
});
