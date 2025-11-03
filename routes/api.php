<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;

Route::prefix('v1')->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
});
