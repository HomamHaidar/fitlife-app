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
        $exercises = Exercise::query()
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('muscle'), fn($q) => $q->forMuscle($request->muscle))
            ->when($request->filled('equipment'), fn($q) => $q->forEquipment($request->equipment))
            ->when($request->filled('difficulty'), fn($q) => $q->forDifficulty($request->difficulty))
            ->withFavoriteStatus()
            ->paginate(20);

        return ExerciseResource::collection($exercises);
    }

    public function show(Exercise $exercise): ExerciseResource
    {
        return new ExerciseResource($exercise);
    }
}
