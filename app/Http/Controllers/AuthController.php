<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            //'user' => new UserResource($user),
            'token' => $token ?? null,
        ], 201);
    }
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        // چک کردن اینکه آیا کاربر احراز هویت شده است
        if ($request->user()) {
            // حذف توکن فعلی
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'No authenticated user found'
        ], 401);
    }
}
