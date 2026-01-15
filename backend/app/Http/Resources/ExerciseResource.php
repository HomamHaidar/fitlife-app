<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'target_muscle' => $this->target_muscle,
            'equipment'     => $this->equipment,
            'difficulty'    => $this->difficulty,
            'image_url'     => $this->image_url,
            'instructions'  => $this->instructions,
        ];
    }
}
