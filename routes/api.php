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
    });

    Route::middleware(['auth:api', 'role:admin'])->group(function () {
        Route::apiResource('books', BookController::class);
        Route::apiResource('patrons', PatronController::class);
    });

    Route::middleware('auth:api')->group(function () {

        Route::get('loans', [LoanController::class, 'index']);
        Route::post('loans', [LoanController::class, 'store']);

        Route::middleware('patron.owns.loan')->group(function () {
            Route::get('loans/{loan}', [LoanController::class, 'show']);
        });

        Route::middleware('role:admin')->group(function () {
            Route::put('loans/{loan}', [LoanController::class, 'update']);
            Route::delete('loans/{loan}', [LoanController::class, 'destroy']);
        });
    });
});
