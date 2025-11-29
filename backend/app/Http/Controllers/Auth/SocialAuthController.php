<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
// Import the config facade if you plan to use 'config('services.google.client_id')'
// use Illuminate\Support\Facades\Config;

class SocialAuthController extends Controller
{

    public function socialLogin(Request $request)
    {
        $request->validate([
            'provider'     => 'required|in:google',
            'access_token' => 'required',
            'device_name'  => 'required'
        ]);

        $googleUser = Http::get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'access_token' => $request->access_token
        ]);

        if (!$googleUser->successful()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired social token.'
            ], 422);
        }

        $data = $googleUser->json();
        $email = $data['email'] ?? null;
        $providerId = $data['sub'] ?? null;

        if (!$email || !$providerId) {
            return response()->json([
                'status' => false,
                'message' => 'The provider did not return a valid email or user ID.'
            ], 422);
        }


        $user = User::where('email', $email)->first();

        if ($user) {
            if (!$user->provider_id) {
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $providerId,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'avatar' => $data['picture'] ?? $user->avatar,
                ]);
            }


        } else {

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $email,
                'provider' => 'google',
                'provider_id' => $providerId,
                'password' => null,
                'email_verified_at' => now(),
                'avatar' => $data['picture'] ?? null,
            ]);
        }


        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
