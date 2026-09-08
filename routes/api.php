<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProfileApiController;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/profiles', [ProfileApiController::class, 'index']);
    Route::post('/profiles', [ProfileApiController::class, 'store']);
    Route::get('/profiles/{id}', [ProfileApiController::class, 'show']);
    Route::put('/profiles/{id}', [ProfileApiController::class, 'update']);
    Route::delete('/profiles/{id}', [ProfileApiController::class, 'destroy']);
    Route::get('/profiles/{id}/analytics', [ProfileApiController::class, 'analytics']);
});
