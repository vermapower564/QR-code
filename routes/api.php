<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProfileApiController;

/*
|--------------------------------------------------------------------------
| API Routes v1 (Sanctum Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Profiles API
    Route::get('/profiles', [ProfileApiController::class, 'index']);
    Route::post('/profiles', [ProfileApiController::class, 'store']);
    Route::get('/profiles/{id}', [ProfileApiController::class, 'show']);
    Route::match(['put', 'patch'], '/profiles/{id}', [ProfileApiController::class, 'update']);
    Route::delete('/profiles/{id}', [ProfileApiController::class, 'destroy']);
    Route::get('/profiles/{id}/analytics', [ProfileApiController::class, 'analytics']);
    Route::get('/profiles/{id}/qr', [ProfileApiController::class, 'show']);

    // Business Outbound Webhooks API
    Route::get('/webhooks', [ProfileApiController::class, 'index']);
    Route::post('/webhooks', [ProfileApiController::class, 'store']);
    Route::get('/webhooks/{id}', [ProfileApiController::class, 'show']);
    Route::match(['put', 'patch'], '/webhooks/{id}', [ProfileApiController::class, 'update']);
    Route::delete('/webhooks/{id}', [ProfileApiController::class, 'destroy']);
});
