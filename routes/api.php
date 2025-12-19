<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::post('signup', [AuthController::class, 'singUp']);
route::post('signin', [AuthController::class, 'singIn']);

route::middleware('auth:sanctum')->group(function () {
    route::post('logout', [AuthController::class, 'logout']);

    // Product Routes
    route::get('products', [ProductController::class, 'index']);
    route::get('products/{id}', [ProductController::class, 'show']);

    // Cart Routes
    route::post('cart/add', [CartController::class, 'add']);
    route::get('cart', [CartController::class, 'view']);
    route::delete('cart/{product_id}', [CartController::class, 'remove']);
});
