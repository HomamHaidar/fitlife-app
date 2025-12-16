<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Google_Client;

class SocialAuthController extends Controller
{
    public function socialLogin(Request $request)
    {

        $request->validate([
            'id_token' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $idToken = $request->input('id_token');



        $client = new Google_Client(['client_id' => config('services.google.client_id')]);

        try {

            $payload = $client->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token verification failed: ' . $e->getMessage()], 401);
        }

        if ($payload) {

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;


            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'google_id' => $googleId,
                    'password' => null,
                    'avatar' => $avatar,
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->google_id) {
                $user->google_id = $googleId;
                $user->save();
            }


            $token = $user->createToken($request->device_name)->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);

        } else {
            return response()->json(['status' => false, 'message' => 'Invalid Google Token'], 401);
        }
    }
}
