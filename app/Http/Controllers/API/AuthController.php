<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' =>  'required|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'massage' => $validator->errors()
            ], 400);
        }

        $data = $request->all();

        $imagePath = null;

        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            $file = $request->file('profile_picture');

            $fileName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('storage/profile', $fileName));
            $imagePath = 'storage/profile/'.$fileName;
        }

        $data['profile_picture'] = $imagePath;
        User::create($data);


        return response()->json([
            'status' => 'success',
            'message' => 'New user created successfully.'
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $response['token'] = $user->createToken('BlogApp')->plainTextToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;

            return response()->json([
                'status' => 'success',
                'message' => 'Logged in successfully',
                'data' => $response
            ], 200);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Credentials invalid'
        ], 400);
    }

    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
