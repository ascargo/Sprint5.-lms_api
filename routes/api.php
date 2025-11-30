<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\PatronController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class,'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('books', [BookController::class, 'index']);
        Route::get('books/{book}', [BookController::class, 'show']);
    });

    Route::middleware(['auth:api', 'role:admin'])->group(function () {
        Route::post('books', [BookController::class, 'store']);
        Route::put('books/{book}', [BookController::class, 'update']);
        Route::delete('books/{book}', [BookController::class, 'destroy']);

        Route::apiResource('patrons', PatronController::class);
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('loans', [LoanController::class, 'index']);

        Route::middleware('patron.owns.loan')->group(function () {
            Route::get('loans/{loan}', [LoanController::class, 'show'])
            ->middleware('patron.owns.loan');
        });

        Route::middleware('role:admin')->group(function () {
            Route::post('loans', [LoanController::class, 'store']);
            Route::put('loans/{loan}', [LoanController::class, 'update']);
            Route::delete('loans/{loan}', [LoanController::class, 'destroy']);
        });
    });
});
