<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::create($request->validated());

            $access_token = $user->createToken('access_token')->plainTextToken;


            return response()->json([
                'message' => 'User created successfully.',
                'user' => $user,
                'access_token' => $access_token,
            ], 201);
        });
    }
}
