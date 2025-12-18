<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}
    public function singUp(RegisterRequest $request)
    {
        $result = $this->authService->signUp($request);

        return response()->json($result, 201);
    }

    public function singIn(LoginRequest $request)
    {
        $result = $this->authService->signIn($request);

        $status = isset($result['token']) ? 200 : 401;

        return response()->json($result, $status);
    }

    public function logout(Request $request)
    {
        $result = $this->authService->logout($request->user());

        return response()->json($result, 200);
    }
}
