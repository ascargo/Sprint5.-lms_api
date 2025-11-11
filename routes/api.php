<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\PatronController;
use App\Http\Controllers\Api\V1\LoanController;

Route::prefix('v1')->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    Route::apiResource('patrons', PatronController::class);

    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loans', [LoanController::class,'store']);
    Route::get('/loans/{loan}', [LoanController::class,'show']);
    Route::put('/loans/{loan}', [LoanController::class, 'update']);
    Route::delete('/loans/{loan}', [LoanController::class, 'destroy']);
});
