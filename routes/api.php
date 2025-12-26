<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReceiptController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| NOTE: The resourceful route for 'categories' has been replaced 
| with explicit routes for index, show, store, update, and destroy.
|
*/

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'reset']);

// Stripe Webhook (must be outside auth middleware)
Route::post('/webhooks/stripe', [PaymentController::class, 'handleWebhook']);

// Public Menu Routes (no auth required)
Route::get('/menu', [ProductController::class, 'index']);
Route::get('/menu/{id}', [ProductController::class, 'show']);

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User Profile Management
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('/home-data', [HomeController::class, 'index']);

    // Order Management
    Route::get('/orders/history', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/send-cancellation-email', [OrderController::class, 'sendCancellationEmail']);

    // Receipt Management
    Route::get('/orders/{orderId}/receipt', [ReceiptController::class, 'viewReceipt']);
    Route::get('/orders/{orderId}/receipt/download', [ReceiptController::class, 'downloadReceipt']);
    Route::post('/orders/{orderId}/request-receipt', [ReceiptController::class, 'requestReceipt']);

    // Payment Processing
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::post('/payments/stripe/intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/payments/stripe/confirm', [PaymentController::class, 'confirmStripePayment']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart Management
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart', [App\Http\Controllers\CartController::class, 'store']);
    Route::put('/cart/{productId}', [App\Http\Controllers\CartController::class, 'update']);
    Route::delete('/cart/{productId}', [App\Http\Controllers\CartController::class, 'destroy']);
    Route::delete('/cart', [App\Http\Controllers\CartController::class, 'clear']);

    // Admin Routes (require admin role)
    Route::middleware('role:admin')->group(function () {

        // Admin Product and Order Management
        Route::prefix('admin')->group(function () {
            // Order Management
            Route::get('/orders', [OrderController::class, 'indexAdmin']);
            Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
            Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrderByAdmin']);

            // Product Management
            Route::post('/products', [ProductController::class, 'store']);
            Route::post('/products/{id}', [ProductController::class, 'updateProduct']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);

            // Payment Refunds
            Route::post('/payments/{paymentId}/refund', [PaymentController::class, 'refundPayment']);

            // Receipt Downloads
            Route::get('/orders/{orderId}/receipt/download', [ReceiptController::class, 'downloadReceipt']);

            // Sales Reports
            Route::get('/sales-report', [SalesReportController::class, 'getSalesReport']);
            Route::get('/sales-report/pdf', [SalesReportController::class, 'generatePDF']);

            // User Management
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{id}', [UserController::class, 'updateUser']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            Route::get('/users/{userId}/orders', [UserController::class, 'getUserOrders']);
        });

        // Category Management
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });
});

    // Admin Routes (require admin role)
    Route::middleware('role:admin')->group(function () {

        // Admin Product and Order Management
        Route::prefix('admin')->group(function () {
            // Order Management
            Route::get('/orders', [OrderController::class, 'indexAdmin']);
            Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
            Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrderByAdmin']);

            // Product Management
            Route::post('/products', [ProductController::class, 'store']);
            Route::post('/products/{id}', [ProductController::class, 'updateProduct']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);

            // Payment Refunds
            Route::post('/payments/{paymentId}/refund', [PaymentController::class, 'refundPayment']);

            // Receipt Downloads
            Route::get('/orders/{orderId}/receipt/download', [ReceiptController::class, 'downloadReceipt']);

            // Sales Reports
            Route::get('/sales-report', [SalesReportController::class, 'getSalesReport']);
            Route::get('/sales-report/pdf', [SalesReportController::class, 'generatePDF']);

            // User Management
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{id}', [UserController::class, 'updateUser']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            Route::get('/users/{userId}/orders', [UserController::class, 'getUserOrders']);
        });

        // Category Management
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });