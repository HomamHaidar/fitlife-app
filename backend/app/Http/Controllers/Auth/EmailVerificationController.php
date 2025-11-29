<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    // Verify email
    public function verify(Request $request,$id,$hash)
    {


        if (! $request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired verification link.'
            ], 403);
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification link.'
            ], 403);
        }


        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified.'
            ], 200);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully.'
        ]);
    }


    public function resend(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $user = $user->fresh();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified.'
            ]);
        }


        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification email resent.'
        ]);
    }
}
