<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {

        $jsonPath = database_path('data/exercises.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File not found at: $jsonPath");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);


        foreach ($data as $item) {
            Exercise::create([
                'name' => $item['name'],


                'target_muscle' => isset($item['primaryMuscles'][0])
                    ? ucfirst($item['primaryMuscles'][0])
                    : 'General',


                'equipment' => isset($item['equipment'])
                    ? ucfirst($item['equipment'])
                    : 'Body Only',


                'instructions' => implode(" ", $item['instructions']),


                'difficulty' => match (strtolower($item['level'])) {
                    'expert' => 'Hard',
                    'intermediate' => 'Medium',
                    default => ucfirst($item['level']),
                },


                'image_url' => 'https://placehold.co/600x400?text=' . urlencode($item['name']),
            ]);
        }
    }
}
