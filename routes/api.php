<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\Auth\AuthController;


Route::group(['prefix' => 'v1/auth'], function() {
    Route::post('/authenticate', [AuthController::class, 'authenticate']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::middleware('jwt')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
    });
});
