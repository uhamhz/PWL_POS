<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:m_user',
            'password' => 'required|string|min:8',
            'nama' => 'required|string|max:255',
            'level_id' => 'required|integer|exists:m_level,level_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Buat user baru
        $user = UserModel::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'nama' => $request->nama,
            'level_id' => $request->level_id,
        ]);
        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Failed to create user',
        ], 409);
    }
}