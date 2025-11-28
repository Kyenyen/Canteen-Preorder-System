<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CanteenController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

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
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Order Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/menu', [ProductController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/history', [OrderController::class, 'index']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/payments', [PaymentController::class, 'store']);

    // Admin Routes
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/orders', [OrderController::class, 'indexAdmin']);
        Route::put('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
