<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    // Verify email
    public function verify(Request $request,$id,$hash)
    {

        if (! $request->hasValidSignature()) {
            return ApiResponse::error('Invalid or expired verification link.', 403);
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return ApiResponse::error('Invalid verification link.', 403);
        }


        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified.');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return ApiResponse::success(null, 'Email verified successfully.');
    }


    public function resend(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        $user = $user->fresh();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified.');
        }


        $user->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'Verification email resent.');
    }
}
