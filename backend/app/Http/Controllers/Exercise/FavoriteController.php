<?php

namespace App\Http\Controllers\Exercise;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        $user = Auth::user();


        $favorites = $user->favorites()->get();

        return ExerciseResource::collection($favorites);
    }
    public function toggle(Exercise $exercise)
    {
        $user = Auth::user();
        $status = $user->favorites()->toggle($exercise->id);
        $isAdded = count($status['attached']) > 0;

        return response()->json([
            'message' => $isAdded ? 'Added to favorites' : 'Removed from favorites',
            'status' => $isAdded ? 'attached' : 'detached',
            'is_favorite' => $isAdded
        ]);
    }


}
