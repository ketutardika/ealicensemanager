<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MqlAccountController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LicenseManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->group(function () {
    Route::post('/webhook/woocommerce', [WebhookController::class, 'handleWooCommerce']);
});

Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    
    // User Routes (Protected)
    Route::middleware(['auth:sanctum', 'role:user|admin'])->group(function () {
        Route::get('/user/{id}', [UserController::class, 'show']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
    });

    // Admin Routes (Protected, Admin Only)
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/user', [UserController::class, 'index']);
    });


    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/licenses', [LicenseController::class, 'index']);
        Route::post('/licenses', [LicenseController::class, 'store']);
        Route::get('/licenses/{id}', [LicenseController::class, 'show']);
        Route::put('/licenses/{id}', [LicenseController::class, 'update']);
        Route::delete('/licenses/{id}', [LicenseController::class, 'destroy']);
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    });

    Route::middleware(['auth:sanctum', 'role:user|admin'])->group(function () {
        Route::get('/orders/user/{id}', [OrderController::class, 'getUserOrders']);
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/mql-accounts', [MQLAccountController::class, 'index']);
        Route::post('/mql-accounts', [MQLAccountController::class, 'store']);
        Route::get('/mql-accounts/{id}', [MQLAccountController::class, 'show']);
        Route::put('/mql-accounts/{id}', [MQLAccountController::class, 'update']);
        Route::delete('/mql-accounts/{id}', [MQLAccountController::class, 'destroy']);
    });

    // API route to handle order completion and license creation
    Route::post('/order-completed', [LicenseManagementController::class, 'handleOrderComplete']);
});
