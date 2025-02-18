<?php

use App\Enum\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1/auth'], function() {
    Route::post('/authenticate', [AuthController::class, 'authenticate']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [AuthController::class, 'forgot']);
    Route::put('/reset-password/{token}', [AuthController::class, 'reset']);

    Route::middleware('jwt')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
    });
});
