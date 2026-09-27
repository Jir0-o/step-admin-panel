<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Api\DiscountRequestController;
use App\Http\Controllers\Api\SanctumAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [SanctumAuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/logout', [SanctumAuthController::class, 'logout'])->middleware('auth:sanctum');

    // API users are managed from the authenticated Mother App UI. Do not let
    // anonymous callers create their own user and then obtain a valid token.
    Route::post('/register', [SanctumAuthController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:5,1']);
});

Route::post('/activity', [ActivityController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authenticated Discount API
|--------------------------------------------------------------------------
| Offline/online POS clients must first call /api/auth/login and then send
| Authorization: Bearer <token>. No discount request/status/decision endpoint
| remains anonymous.
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/discount-requests', [DiscountRequestController::class, 'index']);
    Route::get('/discount-requests/status/{tempCartId}', [DiscountRequestController::class, 'statusByTempCart']);
    Route::get('/discount-requests/{id}', [DiscountRequestController::class, 'show']);
    Route::post('/discount-requests', [DiscountRequestController::class, 'store']);
    Route::patch('/discount-requests/{id}/approve', [DiscountRequestController::class, 'approve']);
    Route::patch('/discount-requests/{id}/reject', [DiscountRequestController::class, 'reject']);
    Route::delete('/discount-requests/{id}/delete', [DiscountRequestController::class, 'destroy']);
    Route::post('/discount-requests/decision', [DiscountRequestController::class, 'decision']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
