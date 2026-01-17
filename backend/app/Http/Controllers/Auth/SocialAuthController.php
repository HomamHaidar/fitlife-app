<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    public function __construct(protected GoogleAuthService $googleService) {}

    public function socialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'id_token'    => 'required|string',
            'device_name' => 'required|string',
        ]);

        $googleUser = $this->googleService->verifyToken($request->input('id_token'));

        if (! $googleUser) {
            return ApiResponse::error('Invalid Google Token', 401);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser['email']],
            [
                'name'              => $googleUser['name'],
                'google_id'         => $googleUser['google_id'],
                'password'          => null, // Social users have no password
                'email_verified_at' => now(),
            ]
        );

        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser['google_id']]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return ApiResponse::success([
            'user'  => new UserResource($user),
            'token' => $token
        ], 'Login successful');
    }
}
