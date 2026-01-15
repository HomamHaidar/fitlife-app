<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Homam',
            'email' => 'homam@example.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Bisher',
            'email' => 'bisher@example.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);


        User::factory()->create([
            'name' => 'Ali',
            'email' => 'ali@example.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);


        $this->call(ExerciseSeeder::class);
    }
}
