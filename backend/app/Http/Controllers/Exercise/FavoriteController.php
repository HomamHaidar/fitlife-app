<?php

namespace App\Http\Controllers\Exercise;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Http\Responses\ApiResponse;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    public function index(): JsonResponse
    {
        $user = Auth::user();


        $favorites = $user->favorites()->get();

        return ApiResponse::success(
            ExerciseResource::collection($favorites)
        );
    }
    public function toggle(Exercise $exercise)
    {
        $user = Auth::user();
        $status = $user->favorites()->toggle($exercise->id);
        $isAdded = count($status['attached']) > 0;

        return ApiResponse::success([
            'status' => $isAdded ? 'attached' : 'detached',
            'is_favorite' => $isAdded
        ], $isAdded ? 'Added to favorites' : 'Removed from favorites');
    }


}
