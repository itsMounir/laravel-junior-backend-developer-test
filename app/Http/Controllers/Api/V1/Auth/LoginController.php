<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function Login(LoginRequest $request): JsonResponse
    {

        $credentials = $request->only("email", "password");

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Your provided credentials cannot be verified.'], 401);
        }

        $user = Auth::user();

        $access_token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully.',
            'user' => $user,
            'access_token' => $access_token,
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully.'
        ]);
    }
}
