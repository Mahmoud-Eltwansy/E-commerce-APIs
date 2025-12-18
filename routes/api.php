<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::post('signup', [AuthController::class, 'singUp']);
route::post('signin', [AuthController::class, 'singIn']);

route::middleware('auth:sanctum')->group(function () {
    route::post('logout', [AuthController::class, 'logout']);
});
