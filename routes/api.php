<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\PatronController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::get('/patrons/me', [PatronController::class, 'showMe']);
        Route::put('/patrons/me', [PatronController::class, 'updateMe']);

        Route::get('/loans/my', [LoanController::class, 'myLoans']);
        Route::put('/loans/my/{loan}', [LoanController::class, 'requestExtension']);

        Route::middleware('admin')->group(function () {
            Route::post('/books', [BookController::class, 'store']);
            Route::put('/books/{book}', [BookController::class, 'update']);
            Route::delete('/books/{book}', [BookController::class, 'destroy']);

            Route::get('/patrons', [PatronController::class, 'index']);
            Route::get('/patrons/{patron}', [PatronController::class, 'show']);
            Route::post('/patrons', [PatronController::class, 'store']);
            Route::put('/patrons/{patron}', [PatronController::class, 'update']);
            Route::delete('/patrons/{patron}', [PatronController::class, 'destroy']);

            Route::get('/loans', [LoanController::class, 'index']);
            Route::get('/loans/{loan}', [LoanController::class, 'show']);
            Route::post('/loans', [LoanController::class, 'store']);
            Route::put('/loans/{loan}', [LoanController::class, 'update']);
            Route::delete('/loans/{loan}', [LoanController::class, 'destroy']);
        });
    });
});
