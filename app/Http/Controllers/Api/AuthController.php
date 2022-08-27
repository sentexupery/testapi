<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);
        if (User::where('role','admin')->exists())
        {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden',
                'data' => '',
            ], 403);
        } else {
            $user = User::create([
                'name' => $fields['name'],
                'role' => 'admin',
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);
            $token = $user->createToken('yode')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];
            return response()->json([
                'status' => 201,
                'message' => 'Created',
                'data' => '',
            ], 201);
        }
    }
    public function login(Request $request): JsonResponse {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Bad Request',
                'data' => '',
            ], 400);
        }

        $token = $user->createToken('yode',[$user->role])->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response()->json([
            'status' => 200,
            'message' => 'OK',
            'data' => $response,
        ], 200);
    }
    public function logout(Request $request):JsonResponse {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
            'data' => '',
        ], 401);
    }
}
