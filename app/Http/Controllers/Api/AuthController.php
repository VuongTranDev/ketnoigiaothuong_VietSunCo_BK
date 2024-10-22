<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|email|unique:Users,email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => ' Lỗi xác thực',
                'errors' => $validator->errors(),
                'status' => false
            ], 403);
        }
        $user = new User();
        $user->password = $request->password;
        $user->email = $request->email;
        $user->status = 1;
        $user->role_id = 2;
        $user->save();

        return response()->json([
            'message' => 'Create user success',
            'data' => $user,
            'status' => true
        ], 201);
    }
    public function login(Request $request){

        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => false
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'status' => true
        ], 200);
    }

    public function getInfo(Request $request)
    {
        return response()->json($request->user());
    }


}
