<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\Exercise\FavoriteController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/testapi', function () {
    return 'API is working';
});

Route::post('/register', [AuthController::class, 'register'])
    ->name('register')
    ->middleware('throttle:5,60');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('throttle:10,1');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

Route::post('/email/verification-notification',[EmailVerificationController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);


Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum','verified')->group(function () {

    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::get('/exercises/{exercise}', [ExerciseController::class, 'show']);

    Route::get('/user/favorites', [FavoriteController::class, 'index']);
    Route::post('/exercises/{exercise}/favorite', [FavoriteController::class, 'toggle']);

});
