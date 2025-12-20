<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::post('signup', [AuthController::class, 'singUp']);
route::post('signin', [AuthController::class, 'singIn']);

// Product Routes
route::get('products', [ProductController::class, 'index']);
route::get('products/{id}', [ProductController::class, 'show']);

route::middleware('auth:sanctum')->group(function () {
    route::post('logout', [AuthController::class, 'logout']);

    // Cart Routes
    route::post('cart/add', [CartController::class, 'add']);
    route::get('cart', [CartController::class, 'view']);
    route::delete('cart/{product_id}', [CartController::class, 'remove']);

    // Order Routes
    route::post('orders', [OrderController::class, 'create']);
    route::get('orders', [OrderController::class, 'index']);
    route::get('orders/{id}', [OrderController::class, 'show']);
});
