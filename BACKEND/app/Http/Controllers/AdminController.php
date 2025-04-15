<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $admin = Administrator::all();

        return response()->json([
            'status' => true,
            'admin' => $admin,
        ]);
    }

    public function show($id)
    {
        $admin = Administrator::findOrFail($id);

        return response()->json([
            'status' => true,
            'admin' => $admin,
        ]);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required | string | max: 60',
            'password' => 'required | string | min: 5 | max: 10',
        ]);

        $user = Administrator::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors(),
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully',
            'admin' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = Administrator::findOrFail($id);

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

        $admin->update([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Admin updated successfully',
            'admin' => $admin,
        ]);
    }

    public function destroy($id)
    {
        $admin = Administrator::findOrFail($id);
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ]);
        }

        $admin->delete();

        return response()->json([
            'status' => true,
            'message' => 'Admin deleted successfully'
        ]);
    }
}
