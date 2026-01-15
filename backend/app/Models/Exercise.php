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

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'exercise_user')->withTimestamps();
    }
}
