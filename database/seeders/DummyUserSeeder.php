<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UserGameInfo;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('players');

            // Create game info with random data
            UserGameInfo::create([
                'user_id' => $user->id,
                'game_id' => 1, // Fixed game ID
                'ingame_id' => Str::upper(Str::random(8)), // Example: "AB3F7H2K"
                'ingame_name' => 'Player' . rand(1000, 9999), // Example: "Player4287"
            ]);
        });
    }
}
