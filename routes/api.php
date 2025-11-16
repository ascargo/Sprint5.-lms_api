<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\PatronController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class,'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('books', BookController::class);
        Route::apiResource('patrons', PatronController::class);
        Route::apiResource('loans', LoanController::class);
    });
});
