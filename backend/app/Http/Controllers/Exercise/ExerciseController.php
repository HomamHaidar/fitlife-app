<?php

namespace App\Http\Controllers\Exercise;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExerciseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Exercise::query()->when(
            auth()->check(),
            fn($q) => $q->with(['favoritedBy' => fn($q) => $q->where('user_id', auth()->id())])
        );


        if ($request->has('muscle')) {
            $query->where('target_muscle', $request->muscle);
        }


        if ($request->has('equipment')) {
            $query->where('equipment', $request->equipment);
        }


        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        return ExerciseResource::collection($query->paginate(20));
    }
    // ... inside the class, after the index function

    public function show(Exercise $exercise): ExerciseResource
    {
        return new ExerciseResource($exercise);
    }




}
