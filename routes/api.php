<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Api\DiscountRequestController;
use App\Http\Controllers\Api\SanctumAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['prefix'=>'auth'], function(){
    Route::post('/register', [SanctumAuthController::class, 'store']);
    Route::post('/login', [SanctumAuthController::class, 'login']);
    Route::post('/logout', [SanctumAuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::post('/activity', [ActivityController::class, 'store']);

Route::get('/discount-requests', [DiscountRequestController::class, 'index']);
Route::get('/discount-requests/{id}', [DiscountRequestController::class, 'show']);
Route::patch('/discount-requests/{id}/approve', [DiscountRequestController::class, 'approve']);
Route::patch('/discount-requests/{id}/reject', [DiscountRequestController::class, 'reject']);
Route::post('/discount-requests', [DiscountRequestController::class,'store']);
Route::get('/discount-requests/status/{tempCartId}', [DiscountRequestController::class,'statusByTempCart']);
Route::post('/discount-requests/decision', [DiscountRequestController::class,'decision']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function(){
    Route::patch('/discount-requests/{discountRequest}/approve', [DiscountRequestController::class,'approve']);
    Route::patch('/discount-requests/{discountRequest}/reject', [DiscountRequestController::class,'reject']);
});