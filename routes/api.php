<?php

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\User\UserCatalogueController;
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

Route::group(['prefix' => 'v1'], function() {
    Route::middleware('jwt')->group(function () {
        Route::resource('user_catalogues', UserCatalogueController::class)->except(['create', 'edit']);
    });
});

