<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\PatronController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {


    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/books', [BookController::class, 'index']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::post('/books', [BookController::class, 'store']);
        Route::get('/books/{id}', [BookController::class, 'show']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);

        Route::apiResource('patrons', PatronController::class);

        Route::get('/loans', [LoanController::class, 'index']);
        Route::post('/loans', [LoanController::class, 'store']);
    });
});
