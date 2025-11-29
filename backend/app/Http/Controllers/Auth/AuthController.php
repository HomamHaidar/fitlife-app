<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(RegisterRequest  $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' =>  Hash::make($data['password']),
        ]);


        event(new Registered($user));

        $token = $user->createToken($data['device_name'] ?? 'device-'.now()->timestamp)->plainTextToken;


        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully. Please verify your email.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }


    public function login(LoginRequest  $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || is_null($user->password) || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user'  => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
