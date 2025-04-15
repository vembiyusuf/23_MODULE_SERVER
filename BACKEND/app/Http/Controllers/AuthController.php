<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required | string | max: 60',
            'password' => 'required | string | min: 5 | max: 10',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors(),
            ]);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function signin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required | string | max: 60',
            'password' => 'required | string | min: 5 | max: 10',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors(),
            ]);
        }
    }

    public function signout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User signed out successfully',
        ]);
    }
}
