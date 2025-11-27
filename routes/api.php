<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CanteenController;

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

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Menu Routes
Route::get('/menu', [CanteenController::class, 'getMenu']);

// Order Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [CanteenController::class, 'storeOrder']);
    Route::get('/orders/history', [CanteenController::class, 'getHistory']);
    Route::put('/orders/{id}/cancel', [CanteenController::class, 'cancelOrder']);

    // Admin Routes
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/orders', [CanteenController::class, 'getAdminOrders']);
        Route::put('/admin/orders/{id}/status', [CanteenController::class, 'updateStatus']);
    });
});
