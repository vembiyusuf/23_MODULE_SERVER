<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
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

        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not Found'
            ]);
        }

        $user->update($request->only([
            'username',
            'password'
        ]));
        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not Found',
            ]);
        }

        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
