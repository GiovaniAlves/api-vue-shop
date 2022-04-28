<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    Auth\AuthController,
    ProductController,
    OrderController,
    SpecificationController,
    UserController
};

Route::group([
    'prefix' => env('API_VERSION')
], function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Rotas autenticadas
    Route::group([
        'prefix' => 'auth',
        'middleware' => ['auth:sanctum']
    ], function () {

        Route::post('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Rotas do usuários adm
        Route::group([
            'middleware' => ['can:access-dashboard']
        ], function () {
            Route::resource('/product', ProductController::class);

            Route::resource('/specification', SpecificationController::class);
            Route::get('/allSpecifications', [SpecificationController::class, 'all']);

            Route::post('/user/search', [UserController::class, 'search']);

            Route::post('/order/search', [OrderController::class, 'search']);
        });

        // Rotas do usuários comuns
        Route::resource('/order', OrderController::class);
    });

    // Rotas da área aberta do site - Só devolvem produtos disponíveis
    Route::post('/product/search', [ProductController::class, 'search']);
    Route::get('/product/showProduct/{url}', [ProductController::class, 'showProduct']);
});
