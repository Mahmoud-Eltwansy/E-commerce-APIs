<?php


namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    public function signUp(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $device_name = $request->input('device_name', $request->userAgent());
        $token = $user->createToken($device_name);

        return [
            'message' => 'Signed Up successfully',
            'token' => $token->plainTextToken,
            'user' => $user,
        ];
    }

    public function signIn(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return [
                'message' => 'The provided credentials are incorrect.',
            ];
        }

        $device_name = $request->input('device_name', $request->userAgent());
        $token = $user->createToken($device_name);

        return [
            'message' => 'Signed In successfully',
            'token' => $token->plainTextToken,
            'user' => $user,
        ];
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();

        return ['message' => 'Logged out successfully'];
    }
}
