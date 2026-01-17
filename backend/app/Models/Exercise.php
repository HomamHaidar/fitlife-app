<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'target_muscle',
        'equipment',
        'difficulty',
        'instructions',
        'image_url',
    ];

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'exercise_user')->withTimestamps();
    }

    // ------------------- SCOPES -------------------



    public function scopeSearch($query, $term)
    {

        $term = trim($term);

        if (empty($term)) {
            return $query;
        }


        $words = explode(' ', $term);

        return $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                // The exercise name MUST contain this word...
                $q->where('name', 'like', '%' . $word . '%');
            }
        });
    }

    public function scopeForMuscle($query, $muscle)
    {
        return $query->where('target_muscle', $muscle);
    }
    public function scopeForEquipment($query, $equipment)
    {
        return $query->where('equipment', $equipment);
    }
    public function scopeForDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeWithFavoriteStatus($query)
    {
        return $query->with(['favorites' => function ($q) {
            $q->where('user_id', auth()->id());
        }]);
    }
}
