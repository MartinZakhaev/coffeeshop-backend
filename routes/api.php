<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
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

// Public API endpoints
Route::prefix('v1')->group(function () {
    // Categories
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    
    // Products
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::get('categories/{category}/products', [ProductController::class, 'byCategory']);
    
    // Customers
    Route::apiResource('customers', CustomerController::class);
    
    // Orders
    Route::apiResource('orders', OrderController::class);
    Route::get('customers/{customer}/orders', [OrderController::class, 'customerOrders']);
});